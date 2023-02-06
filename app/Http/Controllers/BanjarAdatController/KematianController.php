<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluargaKrama;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\KeluargaKrama;
use App\Models\Kematian;
use App\Models\KramaMipil;
use App\Models\Notifikasi;
use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KematianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kematian = Kematian::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan')->where('banjar_adat_id', $banjar_adat_id);
        if(isset($request->rentang_waktu)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $kematian->whereBetween('tanggal_kematian', [$start_date, $end_date]);
        }
        
        if (isset($request->status)) {
            if($request->status == '0'){
                $kematian->where('status', '0');
            }else if($request->status == '1'){
                $kematian->where('status', '1');
            }
        }else{
            $kematian->where(function ($query) {
                $query->where('status', '0')
                      ->orWhere('status', '1');
            });
        }

        $kematian->orderBy('tanggal_kematian', 'DESC');

        return DataTables::of($kematian)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                $return = '';
                if($data->status == '0'){
                    $return .= '<span class="badge badge-warning px-3 py-1"> Draft </span>';
                }else if($data->status == '1'){
                    $return .= '<span class="badge badge-success px-3 py-1"> Sah </span>';
                }
                return $return;
            })
            ->addColumn('link', function ($data) {
                $return = '';
                if($data->status == '0'){
                    $return .= '<a class="btn btn-warning btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-kematian-edit', $data->id).'"><i class="fas fa-edit"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_kematian('.$data->id.')"><i class="fas fa-trash"></i></button>';
                }else if($data->status == '1'){
                    $return .= '<a class="btn btn-primary btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-kematian-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                }
                return $return;
            })
            ->rawColumns(['status', 'link'])
            ->make(true);
    }

    public function datatable_cacah_krama_mipil(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $arr_cacah_krama_mipil_meninggal_id = Kematian::where('banjar_adat_id', $banjar_adat_id)->pluck('cacah_krama_mipil_id')->toArray();
        $kramas = CacahKramaMipil::with('penduduk', 'tempekan')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_cacah_krama_mipil_meninggal_id)->get();
        return Datatables::of($kramas)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $nama = '';
                if($data->penduduk->gelar_depan != ''){
                    $nama = $nama.$data->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$data->penduduk->nama;
                if($data->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$data->penduduk->gelar_belakang;
                }
                $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_cacah_krama_mipil('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }
    
    public function index(){
        return view('pages.banjar.kematian.kematian');
    }

    public function create(){
        return view('pages.banjar.kematian.create');
    }

    public function store($status, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_kematian' => 'required',
            'cacah_krama_mipil' => 'required',
            'penyebab_kematian' => 'required',
            'nomor_suket_kematian' => 'required|unique:tb_kematian',
            'file_suket_kematian' => 'required',
        ],[
            'tanggal_kematian.required' => "Tanggal Kematian wajib diisi",
            'cacah_krama_mipil.required' => "Cacah Krama wajib dipilih",
            'penyebab_kematian.required' => "Penyebab Kematian wajib diisi",
            'nomor_suket_kematian.required' => "Nomor Surat Keterangan wajib diisi",
            'nomor_suket_kematian.unique' => "Nomor Surat Keterangan telah terdaftar",
            'file_suket_kematian.required' => "File Surat Keterangan wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        if($status == '1'){
            //INSERT KEMATIAN
            $kematian = new Kematian();
            $kematian->nomor_akta_kematian = $request->nomor_akta_kematian;
            $kematian->nomor_suket_kematian = $request->nomor_suket_kematian;
            $kematian->cacah_krama_mipil_id = $request->cacah_krama_mipil;
            $kematian->banjar_adat_id = $banjar_adat_id;
            $kematian->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
            $kematian->penyebab_kematian = $request->penyebab_kematian;
            $kematian->keterangan = $request->keterangan;
            $kematian->status = '1';
            $kematian->save();

            if($request->file('file_akta_kematian')!=""){
                $file = $request->file('file_akta_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_akta_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kematian->file_akta_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_suket_kematian')!=""){
                $file = $request->file('file_suket_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_suket_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kematian->file_suket_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kematian->update();
            
            //GET YANG MENINGGAL
            $cacah_krama_mipil = CacahKramaMipil::find($request->cacah_krama_mipil);
            $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

            //NONAKTIFKAN CACAH DAN PENDUDUK
            $cacah_krama_mipil->status = '0';
            $cacah_krama_mipil->alasan_keluar = 'Meninggal dunia';
            $cacah_krama_mipil->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_kematian));
            $cacah_krama_mipil->update();
            $penduduk->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
            $penduduk->update();

            //UPDATE KELUARGA IF ANGGOTA
            $anggota_keluarga = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->where('status', '1')->first();
            if($anggota_keluarga){
                //GET KK LAMA
                $krama_mipil_lama = KramaMipil::find($anggota_keluarga->krama_mipil_id);
                $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

                //COPY DATA
                $krama_mipil_baru = new KramaMipil();
                $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
                $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
                $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
                $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
                $krama_mipil_baru->status = '1';
                $krama_mipil_baru->alasan_perubahan = 'Kematian Anggota Keluarga';
                $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
                $krama_mipil_baru->save();

                foreach($anggota_krama_mipil_lama as $anggota_lama){
                    if($anggota_lama->id != $anggota_keluarga->id){
                        $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                        $anggota_krama_mipil_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                        $anggota_krama_mipil_baru->status_hubungan = $anggota_lama->status_hubungan;
                        $anggota_krama_mipil_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                        $anggota_krama_mipil_baru->status = '1';
                        $anggota_krama_mipil_baru->save();

                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama->status = '0';
                        $anggota_lama->update();
                    }else{
                        //NONAKTIFKAN YANG MENINGGAL
                        $anggota_lama->status = '0';
                        $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_kematian));
                        $anggota_lama->alasan_keluar = 'Meninggal dunia';
                        $anggota_lama->update();
                    }
                    
                }

                //NONAKTIFKAN DATA LAMA
                $krama_mipil_lama->status = '0';
                $krama_mipil_lama->update();
            }
            else{
                $krama_mipil = KramaMipil::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->where('status', '1')->first();
                /**
                 * create notif baru
                 */
                
                $notifikasi = new Notifikasi();
                $notifikasi->notif_kematian_krama($banjar_adat_id, $kematian->id, $krama_mipil->id);
            }
            return redirect()->route('banjar-kematian-home')->with('success', 'Kematian berhasil ditambahkan');

        }else if($status == '0'){
            //INSERT KEMATIAN
            $kematian = new Kematian();
            $kematian->nomor_akta_kematian = $request->nomor_akta_kematian;
            $kematian->nomor_suket_kematian = $request->nomor_suket_kematian;
            $kematian->cacah_krama_mipil_id = $request->cacah_krama_mipil;
            $kematian->banjar_adat_id = $banjar_adat_id;
            $kematian->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
            $kematian->penyebab_kematian = $request->penyebab_kematian;
            $kematian->keterangan = $request->keterangan;
            $kematian->status = '0';
            $kematian->save();

            if($request->file('file_akta_kematian')!=""){
                $file = $request->file('file_akta_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_akta_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kematian->file_akta_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_suket_kematian')!=""){
                $file = $request->file('file_suket_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_suket_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kematian->file_suket_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kematian->update();
            return redirect()->route('banjar-kematian-home')->with('success', 'Draft Kematian berhasil ditambahkan');
        }
    }

    public function edit($id){
        $kematian = Kematian::with('cacah_krama_mipil.penduduk')->find($id);
        $kematian->tanggal_kematian = date("d-m-Y", strtotime($kematian->tanggal_kematian));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($kematian->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$kematian->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$kematian->cacah_krama_mipil->penduduk->nama;
        if($kematian->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$kematian->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $kematian->cacah_krama_mipil->penduduk->nama = $nama;
        return view('pages.banjar.kematian.edit', compact('kematian'));
    }
    
    public function update($id, $status, Request $request){
        //GET DATA KEMATIAN
        $kematian = Kematian::find($id);
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $validator = Validator::make($request->all(), [
            'tanggal_kematian' => 'required',
            'cacah_krama_mipil' => 'required',
            'penyebab_kematian' => 'required',
            'nomor_suket_kematian' => 'required|unique:tb_kematian',
            'nomor_suket_kematian' => [
                Rule::unique('tb_kematian')->ignore($kematian->id),
            ],
        ],[
            'tanggal_kematian.required' => "Tanggal Kematian wajib diisi",
            'cacah_krama_mipil.required' => "Cacah Krama wajib dipilih",
            'penyebab_kematian.required' => "Penyebab Kematian wajib diisi",
            'nomor_suket_kematian.unique' => "Nomor Surat Keterangan telah terdaftar",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        if($status == '1'){
            //GET YANG MENINGGAL
            $cacah_krama_mipil = CacahKramaMipil::find($request->cacah_krama_mipil);
            $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

            //NONAKTIFKAN CACAH DAN PENDUDUK
            $cacah_krama_mipil->status = '0';
            $cacah_krama_mipil->alasan_keluar = 'Meninggal dunia';
            $cacah_krama_mipil->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_kematian));
            $cacah_krama_mipil->update();
            $penduduk->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
            $penduduk->update();

            //UPDATE KELUARGA IF ANGGOTA
            $anggota_keluarga = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->where('status', '1')->first();
            if($anggota_keluarga){
                //GET KK LAMA
                $krama_mipil_lama = KramaMipil::find($anggota_keluarga->krama_mipil_id);
                $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

                //COPY DATA
                $krama_mipil_baru = new KramaMipil();
                $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
                $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
                $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
                $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
                $krama_mipil_baru->status = '1';
                $krama_mipil_baru->alasan_perubahan = 'Kematian Anggota Keluarga';
                $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
                $krama_mipil_baru->save();

                foreach($anggota_krama_mipil_lama as $anggota_lama){
                    if($anggota_lama->id != $anggota_keluarga->id){
                        $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                        $anggota_krama_mipil_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                        $anggota_krama_mipil_baru->status_hubungan = $anggota_lama->status_hubungan;
                        $anggota_krama_mipil_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                        $anggota_krama_mipil_baru->status = '1';
                        $anggota_krama_mipil_baru->save();

                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama->status = '0';
                        $anggota_lama->update();
                    }else{
                        //NONAKTIFKAN YANG MENINGGAL
                        $anggota_lama->status = '0';
                        $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_kematian));
                        $anggota_lama->alasan_keluar = 'Meninggal dunia';
                        $anggota_lama->update();
                    }
                    
                }

                //NONAKTIFKAN DATA LAMA
                $krama_mipil_lama->status = '0';
                $krama_mipil_lama->update();
            }
            //UPDATE KEMATIAN
            $kematian->nomor_akta_kematian = $request->nomor_akta_kematian;
            $kematian->nomor_suket_kematian = $request->nomor_suket_kematian;
            $kematian->cacah_krama_mipil_id = $request->cacah_krama_mipil;
            $kematian->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
            $kematian->penyebab_kematian = $request->penyebab_kematian;
            $kematian->keterangan = $request->keterangan;
            $kematian->status = '1';

            if($request->file('file_akta_kematian')!=""){
                $file = $request->file('file_akta_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_akta_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($kematian->file_akta_kematian != NULL){
                    $old_path = str_replace("/storage","",$kematian->file_akta_kematian);
                    Storage::disk('public')->delete($old_path);
                }
                $kematian->file_akta_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_suket_kematian')!=""){
                $file = $request->file('file_suket_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_suket_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($kematian->file_suket_kematian != NULL){
                    $old_path = str_replace("/storage","",$kematian->file_suket_kematian);
                    Storage::disk('public')->delete($old_path);
                }
                $kematian->file_suket_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kematian->update();
            return redirect()->route('banjar-kematian-home')->with('success', 'Kematian berhasil diperbaharui');

        }else if($status == '0'){
            //UPDATE KEMATIAN
            $kematian->nomor_akta_kematian = $request->nomor_akta_kematian;
            $kematian->nomor_suket_kematian = $request->nomor_suket_kematian;
            $kematian->cacah_krama_mipil_id = $request->cacah_krama_mipil;
            $kematian->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
            $kematian->penyebab_kematian = $request->penyebab_kematian;
            $kematian->keterangan = $request->keterangan;

            if($request->file('file_akta_kematian')!=""){
                $file = $request->file('file_akta_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_akta_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($kematian->file_akta_kematian != NULL){
                    $old_path = str_replace("/storage","",$kematian->file_akta_kematian);
                    Storage::disk('public')->delete($old_path);
                }
                $kematian->file_akta_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_suket_kematian')!=""){
                $file = $request->file('file_suket_kematian');
                $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_suket_kematian';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($kematian->file_suket_kematian != NULL){
                    $old_path = str_replace("/storage","",$kematian->file_suket_kematian);
                    Storage::disk('public')->delete($old_path);
                }
                $kematian->file_suket_kematian = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kematian->update();
            return redirect()->route('banjar-kematian-home')->with('success', 'Draft Kematian berhasil diperbaharui');
        }
    }

    public function detail($id){
        $kematian = Kematian::with('cacah_krama_mipil.penduduk')->find($id);
        $kematian->tanggal_kematian = date("d-m-Y", strtotime($kematian->tanggal_kematian));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($kematian->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$kematian->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$kematian->cacah_krama_mipil->penduduk->nama;
        if($kematian->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$kematian->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $kematian->cacah_krama_mipil->penduduk->nama = $nama;
        return view('pages.banjar.kematian.detail', compact('kematian'));
    }

    public function destroy($id){
        $kematian = Kematian::find($id);
        if($kematian->status == '0'){
            $kematian->delete();
            return redirect()->back()->with('success', 'Draft Kematian berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Kematian yang telah sah tidak dapat dihapus');
        }
    }
}
