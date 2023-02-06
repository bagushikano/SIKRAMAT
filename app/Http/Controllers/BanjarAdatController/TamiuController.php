<?php

namespace App\Http\Controllers\BanjarAdatController;
use App\Http\Controllers\Controller;
use App\Models\AnggotaTamiu;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\CacahTamiu;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\LogPenduduk;
use App\Models\Tamiu;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\Tempekan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $tamiu = Tamiu::with('cacah_tamiu.penduduk','cacah_tamiu.wna', 'cacah_tamiu.banjar_dinas', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id);

        if (isset($request->status)) {
            if($request->status == '1'){
                $tamiu->where('status', '1');
            }else if($request->status == '0'){
                $tamiu->where('status', '0')->where('tanggal_nonaktif', '!=', NULL);
            }else if($request->status == '2'){
                $tamiu->where('banjar_adat_id', $banjar_adat_id)->where('status', '1');
                if(isset($request->rentang_waktu)){
                    $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                        $rentang_waktu = explode(' - ', $request->rentang_waktu);
                        $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                        $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                        return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
                    });
                }
        
                if(isset($request->rentang_waktu_registrasi)){
                    $rentang_waktu = explode(' - ', $request->rentang_waktu_registrasi);
                    $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                    $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                    $tamiu->whereBetween('tanggal_registrasi', [$start_date, $end_date]);
                }
        
                if (isset($request->golongan_darah)) {
                    $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                        return $query->whereIn('golongan_darah', $request->golongan_darah);
                    });
                }
        
                if (isset($request->pekerjaan)) {
                    $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                        return $query->whereIn('profesi_id', $request->pekerjaan);
                    });
                }
        
                if (isset($request->pendidikan)) {
                    $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                        return $query->whereIn('pendidikan_id', $request->pendidikan);
                    });
                }
                $tamiu->orWhere(function ($query) use ($banjar_adat_id) {
                    $query->where('status', '0')
                        ->where('tanggal_nonaktif', '!=', NULL)
                        ->where('banjar_adat_id', $banjar_adat_id);
                });
            }
        }else{
            $tamiu->where('status', '1');
        }

        if(isset($request->rentang_waktu)){
            $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                $rentang_waktu = explode(' - ', $request->rentang_waktu);
                $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
            });
        }

        if(isset($request->rentang_waktu_registrasi)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu_registrasi);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $tamiu->whereBetween('tanggal_registrasi', [$start_date, $end_date]);
        }

        if (isset($request->golongan_darah)) {
            $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                return $query->whereIn('golongan_darah', $request->golongan_darah);
            });
        }

        if (isset($request->pekerjaan)) {
            $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                return $query->whereIn('profesi_id', $request->pekerjaan);
            });
        }

        if (isset($request->pendidikan)) {
            $tamiu->whereHas('cacah_tamiu.penduduk', function ($query) use ($request) {
                return $query->whereIn('pendidikan_id', $request->pendidikan);
            });
        }

        $tamiu->orderBy('tanggal_registrasi', 'DESC');
        return DataTables::of($tamiu)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $nama = '';
                if($data->cacah_tamiu->penduduk->gelar_depan != ''){
                    $nama = $nama.$data->cacah_tamiu->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$data->cacah_tamiu->penduduk->nama;
                if($data->cacah_tamiu->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$data->cacah_tamiu->penduduk->gelar_belakang;
                }
                $return = '';
                // if($data->cacah_tamiu->penduduk->tanggal_kematian != NULL || $data->cacah_tamiu->status == '0'){
                //     if($data->cacah_tamiu->penduduk->tanggal_kematian != NULL){
                //         $tanggal_kematian = date("d-m-Y", strtotime($data->cacah_tamiu->penduduk->tanggal_kematian));
                //     }else{
                //         $tanggal_kematian = date("d-m-Y", strtotime($data->cacah_tamiu->tanggal_nonaktif));
                //     }
                //     $return .= '<button class="btn btn-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Peringatan" onclick="tindakan('.$data->id.', \''.$tanggal_kematian.'\', \''.$nama.'\')"><i class="fas fa-exclamation-triangle"></i></button>';
                // }
                if($data->status == '1'){
                    $return .= '<a class="btn btn-primary btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-tamiu-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_krama('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }else if($data->status == '0'){
                    $return .= '<a class="btn btn-primary btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-tamiu-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                }
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }

    public function get_penduduk($nik){
        $penduduk = Penduduk::with('ayah', 'ibu')->where('nik', $nik)->where('tanggal_kematian', NULL)->first();
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($penduduk){
            $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));
            $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->first();
            $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('tanggal_keluar', NULL)->first();
            $cacah_tamiu = CacahTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('tanggal_keluar', NULL)->first();
            if($cacah_krama_mipil){
                $status = 'terdaftar_krama_mipil';
                return response()->json([
                    'status' => $status
                ]);
            }else if($cacah_krama_tamiu){
                $status = 'terdaftar_krama_tamiu';
                return response()->json([
                    'status' => $status
                ]);
            }else if($cacah_tamiu){
                $status = 'terdaftar_tamiu';
                return response()->json([
                    'status' => $status
                ]);
            }else{
                $status = 'ditemukan';
                $desa = DesaDinas::find($penduduk->desa_id);
                $kecamatan = Kecamatan::where('id', $desa->kecamatan_id)->first();
                $kabupaten = Kabupaten::where('id', $kecamatan->kabupaten_id)->first();
                $provinsi = Provinsi::where('id', $kabupaten->provinsi_id)->first();
                
                //Data Master
                $provinsis = Provinsi::get();
                $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
                $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
                $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();
                $pekerjaans = Pekerjaan::get();
                $pendidikans = Pendidikan::get();
                return response()->json([
                    'status' => $status,
                    'penduduk' => $penduduk,
                    'desa' => $desa,
                    'kecamatan' => $kecamatan,
                    'kabupaten' => $kabupaten,
                    'provinsi' => $provinsi,
                    'desas' => $desas,
                    'kecamatans' => $kecamatans,
                    'kabupatens' => $kabupatens,
                    'provinsis' => $provinsis,
                    'pendidikans' => $pendidikans,
                    'pekerjaans' => $pekerjaans
                ]);
            }
        }else{
            $status = 'tidak_ditemukan';
            return response()->json([
                'status' => $status
            ]);
        }
    }
    
    public function index(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $pekerjaan = Pekerjaan::get();
        $pendidikan = Pendidikan::get();
        return view('pages.banjar.tamiu.tamiu', compact('pekerjaan', 'pendidikan'));
    }

    public function create()
    {
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
        $provinsis = Provinsi::get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
        return view('pages.banjar.tamiu.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'kabupatens', 'banjar_dinas', 'desas'));
    }

    public function store(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $kramas = CacahTamiu::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();

        //INSERT PENDUDUK
        if($penduduk == NULL){
            $validator = Validator::make($request->all(), [
                'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'pekerjaan' => 'required',
                'pendidikan' => 'required',
                'golongan_darah' => 'required',
                'status_perkawinan' => 'required',
                'alamat' => 'required',
                'desa' => 'required',
                'banjar_dinas_id' => 'required',
                'provinsi_asal' => 'required',
                'kabupaten_asal' => 'required',
                'kecamatan_asal' => 'required',
                'desa_asal' => 'required',
            ],[
                'nik.regex' => "NIK hanya boleh mengandung angka",
                'nik.unique' => "NIK yang dimasukkan telah terdaftar",
                'nama.required' => "Nama wajib diisi",
                'nama.regex' => "Nama hanya boleh mengandung huruf",
                'tempat_lahir.required' => "Tempat Lahir wajib diisi",
                'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
                'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
                'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
                'pekerjaan.required' => "Pekerjaan wajib dipilih",
                'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
                'golongan_darah.required' => "Golongan Darah wajib dipilih",
                'status_perkawinan.required' => "Status Perkawinan wajib dipilih",
                'alamat.required' => "Alamat wajib diisi",
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
                'provinsi_asal.required' => "Provinsi Asal wajib dipilih",
                'kabupaten_asal.required' => "Kabupaten Asal wajib dipilih",
                'kecamatan_asal.required' => "Kecamatan Asal wajib dipilih",
                'desa_asal.required' => "Desa/Kelurahan Asal wajib dipilih",
            ]);

            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $penduduk = new Penduduk();
            $penduduk->nik = $request->nik;
            $penduduk->desa_id = $request->desa;
            $penduduk->profesi_id = $request->pekerjaan;
            $penduduk->pendidikan_id = $request->pendidikan;
            $penduduk->gelar_depan = $request->gelar_depan;
            $penduduk->nama = $request->nama;
            $penduduk->gelar_belakang = $request->gelar_belakang;
            $penduduk->nama_alias = $request->nama_alias;
            $penduduk->agama = $request->agama;
            $penduduk->tempat_lahir = $request->tempat_lahir;
            $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $penduduk->jenis_kelamin = $request->jenis_kelamin;
            $penduduk->golongan_darah = $request->golongan_darah;
            $penduduk->status_perkawinan = $request->status_perkawinan;
            $penduduk->telepon = $request->telepon;
            $penduduk->alamat = $request->alamat;
            $penduduk->koordinat_alamat = $request->koordinat_alamat;
            $penduduk->ayah_kandung_id = $request->ayah_kandung;
            $penduduk->ibu_kandung_id = $request->ibu_kandung;
            if($request->foto != ''){
                $image_parts = explode(';base64', $request->foto);
                $image_type_aux = explode('image/', $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid().'.png';
                $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
                $path = $fileLocation."/".$filename;
                $penduduk->foto = '/storage'.$path;
                Storage::disk('public')->put($path, $image_base64);
            }
            $penduduk->save();
        }else{
            $validator = Validator::make($request->all(), [
                'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
                'nik' => [
                    Rule::unique('tb_penduduk')->ignore($penduduk->id),
                ],
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'pekerjaan' => 'required',
                'pendidikan' => 'required',
                'golongan_darah' => 'required',
                'status_perkawinan' => 'required',
                'alamat' => 'required',
                'desa' => 'required',
                'banjar_dinas_id' => 'required',
                'provinsi_asal' => 'required',
                'kabupaten_asal' => 'required',
                'kecamatan_asal' => 'required',
                'desa_asal' => 'required',
            ],[
                'nik.regex' => "NIK hanya boleh mengandung angka",
                'nik.unique' => "NIK yang dimasukkan telah terdaftar",
                'nama.required' => "Nama wajib diisi",
                'nama.regex' => "Nama hanya boleh mengandung huruf",
                'tempat_lahir.required' => "Tempat Lahir wajib diisi",
                'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
                'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
                'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
                'pekerjaan.required' => "Pekerjaan wajib dipilih",
                'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
                'golongan_darah.required' => "Golongan Darah wajib dipilih",
                'status_perkawinan.required' => "Status Perkawinan wajib dipilih",
                'alamat.required' => "Alamat wajib diisi",
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
                'provinsi_asal' => "Provinsi Asal wajib dipilih",
                'kabupaten_asal' => "Kabupaten Asal wajib dipilih",
                'kecamatan_asal' => "Kecamatan Asal wajib dipilih",
                'desa_asal' => "Desa/Kelurahan Asal wajib dipilih",
            ]);
    
            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $penduduk->nik = $request->nik;
            $penduduk->desa_id = $request->desa;
            $penduduk->profesi_id = $request->pekerjaan;
            $penduduk->pendidikan_id = $request->pendidikan;
            $penduduk->gelar_depan = $request->gelar_depan;
            $penduduk->nama = $request->nama;
            $penduduk->gelar_belakang = $request->gelar_belakang;
            $penduduk->nama_alias = $request->nama_alias;
            $penduduk->agama = $request->agama;
            $penduduk->tempat_lahir = $request->tempat_lahir;
            $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $penduduk->jenis_kelamin = $request->jenis_kelamin;
            $penduduk->golongan_darah = $request->golongan_darah;
            $penduduk->status_perkawinan = $request->status_perkawinan;
            $penduduk->telepon = $request->telepon;
            $penduduk->alamat = $request->alamat;
            $penduduk->koordinat_alamat = $request->koordinat_alamat;
            $penduduk->ayah_kandung_id = $request->ayah_kandung;
            $penduduk->ibu_kandung_id = $request->ibu_kandung;
            if($request->foto != ''){
                $image_parts = explode(';base64', $request->foto);
                $image_type_aux = explode('image/', $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid().'.png';
                $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
                $path = $fileLocation."/".$filename;
                $penduduk->foto = '/storage'.$path;
                Storage::disk('public')->put($path, $image_base64);
            }
            $penduduk->update();
        }

        //NOMOR CACAH TAMIU
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $nomor_cacah_tamiu = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_tamiu = $nomor_cacah_tamiu.'03'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.$jumlah_penduduk_tanggal_sama;
        }

        //INSERT CACAH TAMIU
        $cacah_tamiu = new CacahTamiu();
        $cacah_tamiu->nomor_cacah_tamiu = $nomor_cacah_tamiu;
        $cacah_tamiu->banjar_adat_id = $banjar_adat_id;
        $cacah_tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $cacah_tamiu->penduduk_id = $penduduk->id;
        $cacah_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        $cacah_tamiu->desa_asal_id = $request->desa_asal;
        $cacah_tamiu->alamat_asal = $request->alamat_asal;
        $cacah_tamiu->save();

        //NOMOR TAMIU
        $tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_masuk));
        $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
        $curr_year = Carbon::parse($tanggal_registrasi)->year;
        $jumlah_krama_bulan_regis_sama = Tamiu::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
        $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
        $nomor_tamiu = $banjar_adat->kode_banjar_adat.'03'.$curr_month.$curr_year;
        if($jumlah_krama_bulan_regis_sama < 10){
            $nomor_tamiu = $nomor_tamiu.'00'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 100){
            $nomor_tamiu = $nomor_tamiu.'0'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 1000){
            $nomor_tamiu = $nomor_tamiu.$jumlah_krama_bulan_regis_sama;
        }

        //INSERT TAMIU
        $tamiu = new Tamiu();
        $tamiu->nomor_tamiu = $nomor_tamiu;
        $tamiu->banjar_adat_id = $banjar_adat_id;
        $tamiu->cacah_tamiu_id = $cacah_tamiu->id;
        $tamiu->status = '1';
        $tamiu->alasan_perubahan = 'Tamiu Baru';
        $tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_masuk));
        $tamiu->save();

        return redirect()->route('banjar-tamiu-detail', $tamiu->id)->with('success', 'Tamiu berhasil ditambahkan');
    }

    public function anggota($id){
        $tamiu = Tamiu::with('cacah_tamiu.penduduk')->find($id);

        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $tamiu->banjar_adat_id){
            return redirect()->back();
        }
        if($tamiu->status == '0'){
            return redirect()->back();
        }
        //SET NAMA KRAMA
        $nama = '';
        if($tamiu->cacah_tamiu->penduduk->gelar_depan != ''){
            $nama = $nama.$tamiu->cacah_tamiu->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$tamiu->cacah_tamiu->penduduk->nama;
        if($tamiu->cacah_tamiu->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$tamiu->cacah_tamiu->penduduk->gelar_belakang;
        }
        $tamiu->cacah_tamiu->penduduk->nama = $nama;

        $anggota_tamiu = AnggotaTamiu::with('cacah_tamiu.penduduk')->where('tamiu_id', $tamiu->id)->where('status', '1')->orderBy('status_hubungan', 'ASC')->get();
        return view('pages.banjar.tamiu.detail', compact('tamiu', 'anggota_tamiu'));
    }

    public function edit($id){
        //GET MASTER KRAMA
        $tamiu = Tamiu::with('cacah_tamiu.penduduk.ayah', 'cacah_tamiu.penduduk.ibu')->find($id);
        $cacah_tamiu = CacahTamiu::find($tamiu->cacah_tamiu_id);
        $penduduk = Penduduk::find($cacah_tamiu->penduduk_id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $cacah_tamiu->banjar_adat_id){
            return redirect()->back();
        }

        //CONVERT TANGGAL LAHIR
        $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));

        //MASTER LAINNYA
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();

        $desa_asal = DesaDinas::find($cacah_tamiu->desa_asal_id);
        $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
        $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
        $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

        $desas = DesaDinas::where('kecamatan_id', $kecamatan_asal->id)->get();
        $desa_sekitars = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten_asal->id)->get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi_asal->id)->get();
        $provinsis = Provinsi::get();
        return view('pages.banjar.tamiu.edit', compact('desa_sekitars', 'tamiu', 'cacah_tamiu', 'penduduk', 'pekerjaans', 'pendidikans', 'provinsis', 'banjar_dinas', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal', 'desas', 'kecamatans', 'kabupatens'));
    }

    public function update($id, Request $request){
        $tamiu = Tamiu::find($id);
        $cacah_tamiu= CacahTamiu::find($tamiu->cacah_tamiu_id);
        $penduduk = Penduduk::find($cacah_tamiu->penduduk_id);
        
        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
            'nik' => [
                Rule::unique('tb_penduduk')->ignore($penduduk->id),
            ],
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'pekerjaan' => 'required',
            'pendidikan' => 'required',
            'golongan_darah' => 'required',
            'status_perkawinan' => 'required',
            'alamat' => 'required',
            'desa' => 'required',
            'banjar_dinas_id' => 'required',
            'provinsi_asal' => 'required',
            'kabupaten_asal' => 'required',
            'kecamatan_asal' => 'required',
            'desa_asal' => 'required',
        ],[
            'nik.regex' => "NIK hanya boleh mengandung angka",
            'nik.unique' => "NIK yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'pekerjaan.required' => "Pekerjaan wajib dipilih",
            'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
            'golongan_darah.required' => "Golongan Darah wajib dipilih",
            'status_perkawinan.required' => "Status Perkawinan wajib dipilih",
            'alamat.required' => "Alamat wajib diisi",
            'desa.required' => "Desa/Kelurahan wajib dipilih",
            'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
            'provinsi_asal' => "Provinsi Asal wajib dipilih",
            'kabupaten_asal' => "Kabupaten Asal wajib dipilih",
            'kecamatan_asal' => "Kecamatan Asal wajib dipilih",
            'desa_asal' => "Desa/Kelurahan Asal wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //UPDATE PENDUDUK
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $request->desa;
        $penduduk->profesi_id = $request->pekerjaan;
        $penduduk->pendidikan_id = $request->pendidikan;
        $penduduk->gelar_depan = $request->gelar_depan;
        $penduduk->nama = $request->nama;
        $penduduk->gelar_belakang = $request->gelar_belakang;
        $penduduk->nama_alias = $request->nama_alias;
        $penduduk->agama = $request->agama;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->status_perkawinan = $request->status_perkawinan;
        $penduduk->telepon = $request->telepon;
        $penduduk->alamat = $request->alamat;
        $penduduk->koordinat_alamat = $request->koordinat_alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        if($request->foto != ''){
            $image_parts = explode(';base64', $request->foto);
            $image_type_aux = explode('image/', $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $filename = uniqid().'.png';
            $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
            $path = $fileLocation."/".$filename;
            $penduduk->foto = '/storage'.$path;
            Storage::disk('public')->put($path, $image_base64);
        }
        $penduduk->update();

        //UPDATE CACAH
        $cacah_tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $cacah_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_tamiu->desa_asal_id = $request->desa_asal;
        $cacah_tamiu->alamat_asal = $request->alamat_asal;
        $cacah_tamiu->update();

        //UPDATE TAMIU
        $tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $tamiu->update();

        //UPDATE DATA ANGGOTA
        $anggota_tamiu = AnggotaTamiu::where('tamiu_id', $tamiu->id)->where('status', '1')->get();
        foreach($anggota_tamiu as $anggota){
            //GET CACAH ANGGOTA
            $cacah_anggota = CacahTamiu::find($anggota->cacah_tamiu_id);
            $cacah_anggota->desa_asal_id = $request->desa_asal;
            $cacah_anggota->alamat_asal = $request->alamat_asal;
            $cacah_anggota->update();

            //GET PENDUDUK ANGGOTA
            $penduduk_anggota = Penduduk::find($cacah_anggota->penduduk_id);
            $penduduk_anggota->desa_id = $request->desa;
            $penduduk_anggota->alamat = $request->alamat;
            $penduduk_anggota->koordinat_alamat = $request->koordinat_alamat;
            $penduduk_anggota->update();
        }

        return redirect()->route('banjar-tamiu-detail', $tamiu->id)->with('success', 'Tamiu berhasil diperbaharui');
    }

    public function ganti($id, Request $request){
        $validator = Validator::make($request->all(), [
            'tamiu_baru' => 'required',
            'alasan_penggantian' => 'required',
        ],[
            'tamiu_baru.required' => "Krama Tamiu baru wajib dipilih",
            'alasan_penggantian.required' => "Alasan Pergantian wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET KELUARGA LAMA
        $tamiu_lama = Tamiu::with('cacah_tamiu.penduduk')->find($id);
        $anggota_tamiu_lama = AnggotaTamiu::with('cacah_tamiu.penduduk')->where('tamiu_id', $tamiu_lama->id)->get();

        //INSERT KRAMA TAMIU BARU
        $tamiu_baru = new Tamiu();
        $tamiu_baru->nomor_tamiu = $tamiu_lama->nomor_tamiu;
        $tamiu_baru->banjar_adat_id = $tamiu_lama->banjar_adat_id;
        $tamiu_baru->cacah_tamiu_id = $request->tamiu_baru;
        $tamiu_baru->status = '1';
        $tamiu_baru->alasan_perubahan = ucwords(str_replace('_', ' ', $request->alasan_penggantian));
        $tamiu_baru->tanggal_registrasi = $tamiu_lama->tanggal_registrasi;
        $tamiu_baru->save();

        //INSERT ANGGOTA BARU DAN DELETE ANGGOTA LAMA SEBAGAI HISTORY
        foreach ($anggota_tamiu_lama as $anggota_lama){
            if($anggota_lama->cacah_tamiu_id != $request->tamiu_baru){
                $anggota_tamiu_baru = new AnggotaTamiu();
                $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
                $anggota_tamiu_baru->cacah_tamiu_id = $anggota_lama->cacah_tamiu_id;
                $anggota_tamiu_baru->status_hubungan = $anggota_lama->status_hubungan;
                $anggota_tamiu_baru->tanggal_registrasi = $anggota_lama->tanggal_registrasi;
                $anggota_tamiu_baru->save();
            }else if($anggota_lama->cacah_tamiu_id == $request->tamiu_baru && $request->alasan_penggantian != 'tamiu_meninggal_dunia'){
                    $anggota_tamiu_baru = new AnggotaTamiu();
                    $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
                    $anggota_tamiu_baru->cacah_tamiu_id = $tamiu_lama->cacah_tamiu_id;
                    $anggota_tamiu_baru->status_hubungan = 'famili_lain';
                    $anggota_tamiu_baru->tanggal_registrasi = $tamiu_lama->tanggal_registrasi;
                    $anggota_tamiu_baru->save();
                // if($anggota_lama->status_hubungan == 'anak'){
                //     $anggota_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
                //     $anggota_tamiu_baru->cacah_tamiu_id = $tamiu_lama->cacah_tamiu_id;
                //     if($tamiu_lama->cacah_tamiu->penduduk->jenis_kelamin == 'perempuan'){
                //         $anggota_tamiu_baru->status_hubungan = 'ibu';
                //     }else{
                //         $tamiu_lama->status_hubungan = 'ayah';
                //     }
                //     $anggota_tamiu_baru->save();
                // }else if($anggota_lama->status_hubungan == 'istri'){
                //     $anggota_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
                //     $anggota_tamiu_baru->cacah_tamiu_id = $tamiu_lama->cacah_tamiu_id;
                //     $anggota_tamiu_baru->status_hubungan = 'suami';
                //     $anggota_tamiu_baru->save();
                // }else if($anggota_lama->status_hubungan == 'suami'){
                //     $anggota_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
                //     $anggota_tamiu_baru->cacah_tamiu_id = $tamiu_lama->cacah_tamiu_id;
                //     $anggota_tamiu_baru->status_hubungan = 'istri';
                //     $anggota_tamiu_baru->save();
                // }else if($anggota_lama->status_hubungan == 'ayah' || $anggota_lama->status_hubungan == 'ibu'){
                //     $anggota_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
                //     $anggota_tamiu_baru->cacah_tamiu_id = $tamiu_lama->cacah_tamiu_id;
                //     $anggota_tamiu_baru->status_hubungan = 'anak';
                //     $anggota_tamiu_baru->save();
                // }
                
            }
            $anggota_lama->status == '1';
            $anggota_lama->update();
        }

        //NONAKTIFKAN KRAMA TAMIU LAMA
        $tamiu_lama->status = '0';
        $tamiu_lama->update();

        return redirect()->route('banjar-tamiu-detail', $tamiu_baru->id)->with('success', 'Tamiu berhasil diganti');
    }

    public function delete($id, Request $request){
        //GET TAMIU DAN KELUARGA
        $tamiu = Tamiu::with('cacah_tamiu.penduduk')->find($id);
        $anggota_tamiu = AnggotaTamiu::with('cacah_tamiu.penduduk')->where('tamiu_id', $tamiu->id)->get();

        foreach($anggota_tamiu as $anggota){
            $anggota->status = '0';
            $anggota->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
            $anggota->alasan_keluar = $request->alasan_keluar;
            $anggota->update();

            //NONAKTIFKAN CACAH
            $cacah_anggota = CacahTamiu::find($anggota->cacah_tamiu_id);
            $cacah_anggota->status = '0';
            $cacah_anggota->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
            $cacah_anggota->alasan_keluar = $request->alasan_keluar;
            $cacah_anggota->update();
        }

        $tamiu->status = '0';
        $tamiu->alasan_keluar = $request->alasan_keluar;
        $tamiu->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
        $tamiu->update();

        //NONAKTIFKAN CACAH KRAMA MIPIL
        $cacah_tamiu = CacahKramaTamiu::find($tamiu->cacah_tamiu_id);
        $cacah_tamiu->status = '0';
        $cacah_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_tamiu->alasan_keluar = $request->alasan_keluar;
        $cacah_tamiu->update();

        return redirect()->back()->with('success', 'Tamiu berhasil dinonaktifkan');
    }

    public function detail($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $tamiu = Tamiu::find($id);
        $krama = CacahTamiu::find($tamiu->cacah_tamiu_id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);

        $desa_asal = DesaDinas::find($krama->desa_asal_id);
        $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
        $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
        $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
        return view('pages.banjar.tamiu.detail_tamiu', compact('tamiu', 'krama', 'penduduk', 'desa', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
    }

    public function daftar_riwayat($id){
        //GET CACAH
        $tamiu = Tamiu::find($id);
        $cacah_tamiu = CacahTamiu::with('penduduk')->find($tamiu->cacah_tamiu_id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $penduduk = Penduduk::find($cacah_tamiu->penduduk_id);
        $nama = '';
        if($penduduk->gelar_depan != NULL){
            $nama = $nama.$penduduk->gelar_depan.' ';
        }
        $nama = $nama.$penduduk->nama;
        if($penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$penduduk->gelar_belakang;
        }
        $penduduk->nama = $nama;

        //GET RIWAYAT PERUBAHAN
        $riwayats = LogPenduduk::where('penduduk_id', $penduduk->id)->get();

        //RETURN
        return view('pages.banjar.tamiu.daftar_riwayat', compact('riwayats', 'penduduk', 'cacah_tamiu', 'tamiu'));
    }

    public function detail_riwayat($id, $id_riwayat){
        //GET CACAH
        $tamiu = Tamiu::find($id);
        $cacah_tamiu = CacahTamiu::with('penduduk')->find($tamiu->cacah_tamiu_id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $curr_penduduk = Penduduk::find($cacah_tamiu->penduduk_id);
        $nama = '';
        if($curr_penduduk->gelar_depan != NULL){
            $nama = $nama.$curr_penduduk->gelar_depan.' ';
        }
        $nama = $nama.$curr_penduduk->nama;
        if($curr_penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$curr_penduduk->gelar_belakang;
        }
        $curr_penduduk->nama = $nama;
        
        //GET RIWAYAT PERUBAHAN
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $krama = $cacah_tamiu;
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = LogPenduduk::with('pekerjaan', 'pendidikan')->find($id_riwayat);
        if($krama->penduduk_id != $penduduk->penduduk_id){
            return redirect()->back();
        }

        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);

        $desa_asal = DesaDinas::find($krama->desa_asal_id);
        $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
        $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
        $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
        return view('pages.banjar.tamiu.detail_riwayat', compact('krama', 'tamiu', 'curr_penduduk','penduduk', 'desa', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
    }
}