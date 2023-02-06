<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaTamiu;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\CacahKramaTamiu;
use App\Models\Negara;
use App\Models\CacahTamiu;
use App\Models\DesaAdat;
use App\Models\LogPenduduk;
use App\Models\Tamiu;
use App\Models\WNA;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CacahTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable_tamiu(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kramas = Tamiu::with('cacah_tamiu.penduduk', 'cacah_tamiu.banjar_dinas', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->get()->map(function ($item){
            $item->anggota_keluarga = AnggotaTamiu::with('cacah_tamiu.penduduk')->where('tamiu_id', $item->id)->get();
            return $item;
        });
        return Datatables::of($kramas)
        ->addIndexColumn()
        ->addColumn('anggota', function ($data) {
            $return = '<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>';
            return $return;
        })
        ->addColumn('link', function ($data) {
            $nama = '';
            if($data->cacah_tamiu->penduduk->gelar_depan != ''){
                $nama = $nama.$data->cacah_tamiu->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$data->cacah_tamiu->penduduk->nama;
            if($data->cacah_tamiu->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$data->cacah_tamiu->penduduk->gelar_belakang;
            }
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_tamiu('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function get_anggota_keluarga($id){
        $tamiu = Tamiu::with('cacah_tamiu.penduduk')->find($id);
        $anggota_tamiu = AnggotaTamiu::with('cacah_tamiu.penduduk')->where('tamiu_id', $tamiu->id)->get();

        //SET NAMA LENGKAP KRAMA MIPIL
        $nama = '';
        if($tamiu->cacah_tamiu->penduduk->gelar_depan != ''){
            $nama = $nama.$tamiu->cacah_tamiu->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$tamiu->cacah_tamiu->penduduk->nama;
        if($tamiu->cacah_tamiu->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$tamiu->cacah_tamiu->penduduk->gelar_belakang;
        }
        $tamiu->cacah_tamiu->penduduk->nama = $nama;

        //SET NAMA LENGKAP ANGGOTA
        foreach($anggota_tamiu as $anggota){
            $nama = '';
            if($anggota->cacah_tamiu->penduduk->gelar_depan != ''){
                $nama = $nama.$anggota->cacah_tamiu->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$anggota->cacah_tamiu->penduduk->nama;
            if($anggota->cacah_tamiu->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anggota->cacah_tamiu->penduduk->gelar_belakang;
            }
            $anggota->cacah_tamiu->penduduk->nama = $nama;
        }
        return response()->json([
            'tamiu' => $tamiu,
            'anggota_tamiu'=> $anggota_tamiu,
            'nomor_tamiu' => $tamiu->nomor_tamiu
        ]);
    }

    public function datatable_wni(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kk = Tamiu::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('cacah_tamiu_id');
        $tamiu_wni = CacahTamiu::with('penduduk')->where('wna_id', NULL)->where('banjar_adat_id', $banjar_adat_id)->whereNotIn('id', $kk);
        if (isset($request->wni_status)) {
            if($request->wni_status == '1'){
                $tamiu_wni->where('status', '1');
            }else if($request->wni_status == '0'){
                $tamiu_wni->where('status', '0');
            }
        }else{
            $tamiu_wni->where('status', '1');
        }

        if(isset($request->wni_rentang_waktu)){
            $tamiu_wni->whereHas('penduduk', function ($query) use ($request) {
                $rentang_waktu = explode(' - ', $request->wni_rentang_waktu);
                $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
            });
        }

        if(isset($request->wni_rentang_waktu_masuk)){
            $rentang_waktu = explode(' - ', $request->wni_rentang_waktu_masuk);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $tamiu_wni->whereBetween('tanggal_masuk', [$start_date, $end_date]);
        }

        if (isset($request->wni_golongan_darah)) {
            $tamiu_wni->whereHas('penduduk', function ($query) use ($request) {
                return $query->whereIn('golongan_darah', $request->wni_golongan_darah);
            });
        }

        if (isset($request->wni_agama)) {
            $tamiu_wni->whereHas('penduduk', function ($query) use ($request) {
                return $query->whereIn('agama', $request->wni_agama);
            });
        }

        if (isset($request->wni_jenis_kelamin)) {
            $tamiu_wni->whereHas('penduduk', function ($query) use ($request) {
                return $query->where('jenis_kelamin', $request->wni_jenis_kelamin);
            });
        }

        if (isset($request->wni_pekerjaan)) {
            $tamiu_wni->whereHas('penduduk', function ($query) use ($request) {
                return $query->whereIn('profesi_id', $request->wni_pekerjaan);
            });
        }

        if (isset($request->wni_pendidikan)) {
            $tamiu_wni->whereHas('penduduk', function ($query) use ($request) {
                return $query->whereIn('pendidikan_id', $request->wni_pendidikan);
            });
        }

        $tamiu_wni->orderBy('tanggal_masuk', 'DESC');

        return DataTables::of($tamiu_wni)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $return = '';
                $return .= '<a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-cacah-tamiu-wni-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                if($data->tanggal_keluar == ''){
                    $return .= '<a class="btn btn-warning btn-sm mx-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="'.route('banjar-cacah-tamiu-wni-edit', $data->id).'"><i class="fas fa-edit"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_tamiu_wni('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }

    public function datatable_wna(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $tamiu_wna = CacahTamiu::with('wna', 'banjar_dinas')->where('penduduk_id', NULL)->where('banjar_adat_id', $banjar_adat_id);
        if (isset($request->wna_status)) {
            if($request->wna_status == '1'){
                $tamiu_wna->where('status', '1');
            }else if($request->wna_status == '0'){
                $tamiu_wna->where('status', '0');
            }
        }else{
            $tamiu_wna->where('status', '1');
        }

        if(isset($request->wna_rentang_waktu)){
            $tamiu_wna->whereHas('wna', function ($query) use ($request) {
                $rentang_waktu = explode(' - ', $request->wna_rentang_waktu);
                $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
            });
        }

        if(isset($request->wna_rentang_waktu_masuk)){
            $rentang_waktu = explode(' - ', $request->wna_rentang_waktu_masuk);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $tamiu_wna->whereBetween('tanggal_masuk', [$start_date, $end_date]);
        }

        if (isset($request->wna_jenis_kelamin)) {
            $tamiu_wna->whereHas('wna', function ($query) use ($request) {
                return $query->where('jenis_kelamin', $request->wna_jenis_kelamin);
            });
        }

        $tamiu_wna->orderBy('tanggal_masuk', 'DESC');

        return DataTables::of($tamiu_wna)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $return = '';
                $return .= '<a class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-cacah-tamiu-wna-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                if($data->tanggal_keluar == ''){
                    $return .= '<a class="btn btn-warning btn-sm mx-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="'.route('banjar-cacah-tamiu-wna-edit', $data->id).'"><i class="fas fa-edit"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_tamiu_wna('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }
    
    public function index(){
        $pekerjaan = Pekerjaan::get();
        $pendidikan = Pendidikan::get();
        return view('pages.banjar.cacah_tamiu.cacah_tamiu', compact('pekerjaan', 'pendidikan'));
    }

    public function search_orang_tua(Request $request)
    {
        $penduduk = Penduduk::where('nik', $request->input('term', ''))->orWhere('nomor_induk_cacah_krama', $request->input('term', ''))->first();
        $response = array();
        $response[] = array(
            "id"=>$penduduk->id,
            "text"=>$penduduk->nama
        );
        return ['results' => $response];
    }

    public function get_penduduk($nik){
        $penduduk = Penduduk::with('ayah', 'ibu')->where('nik', $nik)->first();
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        if($penduduk){
            $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));
            $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->first();
            $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->first();
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
                
                //Data Master
                $provinsis = Provinsi::get();
                $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
                $pekerjaans = Pekerjaan::get();
                $pendidikans = Pendidikan::get();
                return response()->json([
                    'status' => $status,
                    'penduduk' => $penduduk,
                    'desa' => $desa,
                    'desas' => $desas,
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

    public function get_wna($nomor_paspor){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $wna = WNA::with('negara')->where('nomor_paspor', $nomor_paspor)->first();
        if($wna){
            $wna->tanggal_lahir = date("d-m-Y", strtotime($wna->tanggal_lahir));
            $cacah_krama = CacahTamiu::where('wna_id', $wna->id)->where('banjar_adat_id', $banjar_adat_id)->first();
            if($cacah_krama == ''){
                $status = 'ditemukan';
                $negaras = Negara::get();
                return response()->json([
                    'status' => $status,
                    'wna' => $wna,
                    'negaras' => $negaras
                ]);
            }else{
                $status = 'terdaftar';
                return response()->json([
                    'status' => $status
                ]);
            }
    
        }else{
            $status = 'tidak_ditemukan';
            return response()->json([
                'status' => $status
            ]);
        }
    }

    public function create_tamiu_wni(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
        return view('pages.banjar.cacah_tamiu.create_tamiu_wni', compact('pekerjaans', 'pendidikans', 'provinsis', 'banjar_dinas', 'desas'));
    }

    public function create_tamiu_wna(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $negaras = Negara::get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        return view('pages.banjar.cacah_tamiu.create_tamiu_wna', compact('negaras', 'banjar_dinas'));
    }

    public function store_tamiu_wni(Request $request)
    {
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $tamiu = Tamiu::with('cacah_tamiu')->find($request->tamiu);
        $cacah = CacahTamiu::find($tamiu->cacah_tamiu_id);
        $penduduk_tamiu = Penduduk::find($cacah->penduduk_id);

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
            $penduduk->desa_id = $penduduk_tamiu->desa_id;
            $penduduk->alamat = $penduduk_tamiu->alamat;
            $penduduk->koordinat_alamat = $penduduk_tamiu->koordinat_alamat;
            if($request->cari_ayah_manual){
                $penduduk->ayah_kandung_id = $request->ayah_kandung_manual;
            }else{
                $penduduk->ayah_kandung_id = $request->ayah_kandung;
            }
            if($request->cari_ibu_manual){
                $penduduk->ibu_kandung_id = $request->ibu_kandung_manual;
            }else{
                $penduduk->ibu_kandung_id = $request->ibu_kandung;
            }
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
            $penduduk->desa_id = $penduduk_tamiu->desa_id;
            $penduduk->alamat = $penduduk_tamiu->alamat;
            $penduduk->koordinat_alamat = $penduduk_tamiu->koordinat_alamat;
            if($request->cari_ayah_manual){
                $penduduk->ayah_kandung_id = $request->ayah_kandung_manual;
            }else{
                $penduduk->ayah_kandung_id = $request->ayah_kandung;
            }
            if($request->cari_ibu_manual){
                $penduduk->ibu_kandung_id = $request->ibu_kandung_manual;
            }else{
                $penduduk->ibu_kandung_id = $request->ibu_kandung;
            }
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
        $kramas = CacahTamiu::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();
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
        $cacah_tamiu->banjar_dinas_id = $cacah->banjar_dinas_id;
        $cacah_tamiu->penduduk_id = $penduduk->id;
        $cacah_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_tamiu->desa_asal_id = $cacah->desa_asal_id;
        $cacah_tamiu->alamat_asal = $cacah->alamat_asal;
        $cacah_tamiu->save();

        //GET DATA LAMA
        $tamiu_lama = Tamiu::find($request->tamiu);
        $anggota_tamiu_lama = AnggotaTamiu::where('tamiu_id', $tamiu_lama->id)->where('status', '1')->get();

        //COPY DATA
        $tamiu_baru = new Tamiu();
        $tamiu_baru->nomor_tamiu = $tamiu_lama->nomor_tamiu;
        $tamiu_baru->banjar_adat_id = $tamiu_lama->banjar_adat_id;
        $tamiu_baru->cacah_tamiu_id = $tamiu_lama->cacah_tamiu_id;
        $tamiu_baru->status = '1';
        $tamiu_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru';
        $tamiu_baru->tanggal_registrasi = $tamiu_lama->tanggal_registrasi;
        $tamiu_baru->save();

        foreach($anggota_tamiu_lama as $anggota_lama){
            $anggota_tamiu_baru = new AnggotaTamiu();
            $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
            $anggota_tamiu_baru->cacah_tamiu_id = $anggota_lama->cacah_tamiu_id;
            $anggota_tamiu_baru->status_hubungan = $anggota_lama->status_hubungan;
            $anggota_tamiu_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
            $anggota_tamiu_baru->status = '1';
            $anggota_tamiu_baru->save();

            //NONAKTIFKAN ANGGOTA LAMA
            $anggota_lama->status = '0';
            $anggota_lama->update();
        }

        //INSERT ANGGOTA BARU
        $anggota_tamiu = new AnggotaTamiu();
        $anggota_tamiu->tamiu_id = $tamiu_baru->id;
        $anggota_tamiu->cacah_tamiu_id = $cacah_tamiu->id;
        $anggota_tamiu->status_hubungan = $request->status_hubungan;
        $anggota_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_tamiu->status = '1';
        $anggota_tamiu->save();

        //NONAKTIFKAN DATA LAMA
        $tamiu_lama->status = '0';
        $tamiu_lama->update();
        return redirect()->route('banjar-cacah-tamiu-home')->with('success', 'Cacah Tamiu berhasil ditambahkan');
    }

    public function store_tamiu_wna(Request $request)
    {
        $banjar_adat_id = session()->get('banjar_adat_id');
        $wna = WNA::where('nomor_paspor', $request->nomor_paspor)->first();
        $kramas = CacahTamiu::where('banjar_adat_id', $banjar_adat_id)->pluck('wna_id')->toArray();

        //TAMBAH PENDUDUK BARU
        if($wna == NULL){
            $validator = Validator::make($request->all(), [
                'nomor_paspor' => 'required|unique:tb_wna',
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'tanggal_masuk' => 'required',
                'jenis_kelamin' => 'required',
                'alamat' => 'required',
                'negara' => 'required',
                'banjar_dinas_id' => 'required'
            ],[
                'nomor_paspor.unique' => "Nomor Paspor telah terdaftar",
                'nomor_paspor.required' => "Nomor Paspor wajib diisi",
                'nama.required' => "Nama wajib diisi",
                'nama.regex' => "Nama hanya boleh mengandung huruf",
                'tempat_lahir.required' => "Tempat Lahir wajib diisi",
                'tempat_masuk.required' => "Tanggal Masuk wajib diisi",
                'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
                'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
                'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
                'alamat.required' => "Alamat wajib diisi",
                'negara.required' => "Negara Asal wajib dipilih",
                'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
            ]);

            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $wna = new WNA();
            $wna->nomor_paspor = $request->nomor_paspor;
            $wna->nama = $request->nama;
            $wna->tempat_lahir = $request->tempat_lahir;
            $wna->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $wna->jenis_kelamin = $request->jenis_kelamin;
            $wna->alamat = $request->alamat;
            $wna->koordinat_alamat = $request->koordinat_alamat;
            $wna->negara_id = $request->negara;
            //SET FOTO PENDUDUK
            if($request->foto != ''){
                $image_parts = explode(';base64', $request->foto);
                $image_type_aux = explode('image/', $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid().'.png';
                $fileLocation = '/image/wna/'.$wna->nomor_paspor.'/foto';
                $path = $fileLocation."/".$filename;
                $wna->foto = '/storage'.$path;
                Storage::disk('public')->put($path, $image_base64);
            }
            $wna->save();
        }else{
            $validator = Validator::make($request->all(), [
                'nomor_paspor' => 'required|unique:tb_wna',
                'nomor_paspor' => [
                    Rule::unique('tb_wna')->ignore($wna->id),
                ],
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'tanggal_masuk' => 'required',
                'jenis_kelamin' => 'required',
                'alamat' => 'required',
                'negara' => 'required',
                'banjar_dinas_id' => 'required'
            ],[
                'nomor_paspor.unique' => "Nomor Paspor telah terdaftar",
                'nomor_paspor.required' => "Nomor Paspor wajib diisi",
                'nama.required' => "Nama wajib diisi",
                'nama.regex' => "Nama hanya boleh mengandung huruf",
                'tempat_lahir.required' => "Tempat Lahir wajib diisi",
                'tempat_masuk.required' => "Tanggal Masuk wajib diisi",
                'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
                'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
                'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
                'alamat.required' => "Alamat wajib diisi",
                'negara.required' => "Negara Asal wajib dipilih",
                'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
            ]);

            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $wna->nomor_paspor = $request->nomor_paspor;
            $wna->nama = $request->nama;
            $wna->tempat_lahir = $request->tempat_lahir;
            $wna->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $wna->jenis_kelamin = $request->jenis_kelamin;
            $wna->alamat = $request->alamat;
            $wna->koordinat_alamat = $request->koordinat_alamat;
            $wna->negara_id = $request->negara;
            //SET FOTO PENDUDUK
            if($request->foto != ''){
                $image_parts = explode(';base64', $request->foto);
                $image_type_aux = explode('image/', $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid().'.png';
                $fileLocation = '/image/wna/'.$wna->nomor_paspor.'/foto';
                $path = $fileLocation."/".$filename;
                $wna->foto = '/storage'.$path;
                Storage::disk('public')->put($path, $image_base64);
            }
            $wna->update();
        }

        //NOMOR KRAMA TAMIU & NOMOR INDUK KRAMA
        $jumlah_penduduk_tanggal_sama = WNA::where('tanggal_lahir', $wna->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $nomor_cacah_tamiu = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_tamiu = $nomor_cacah_tamiu.'03'.Carbon::parse($wna->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.$jumlah_penduduk_tanggal_sama;
        }
        
        //INSERT KRAMA tamiu
        $cacah_tamiu = new CacahTamiu();
        $cacah_tamiu->nomor_cacah_tamiu = $nomor_cacah_tamiu;
        $cacah_tamiu->banjar_adat_id = $banjar_adat_id;
        $cacah_tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $cacah_tamiu->wna_id = $wna->id;
        $cacah_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        if($request->tanggal_keluar != ''){
            $cacah_tamiu->status = '0';
            $cacah_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        }
        $cacah_tamiu->save();
        return redirect()->route('banjar-cacah-tamiu-home')->with(['success' => 'Cacah Tamiu berhasil ditambahkan', 'tamiu' => 'wna']);
    }

    public function edit_tamiu_wni($id){
        //GET MASTER ANGGOTA
        $cacah_tamiu = CacahTamiu::find($id);
        $anggota_krama = AnggotaTamiu::where('cacah_tamiu_id', $cacah_tamiu->id)->where('status', '1')->first();
        $penduduk = Penduduk::find($cacah_tamiu->penduduk_id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK KRAMA
        $tamiu = Tamiu::with('cacah_tamiu.penduduk')->find($anggota_krama->tamiu_id);
        $cacah = CacahTamiu::find($tamiu->cacah_tamiu_id);
        $penduduk_krama = Penduduk::find($cacah->penduduk_id);

        //REVERSE TANGGAL LAHIR DAN REGISTRASI CACAH KRAMA MIPIL
        $anggota_krama->tanggal_registrasi = date("d-m-Y", strtotime($anggota_krama->tanggal_registrasi));
        $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));

        //SET NAMA LENGKAP KRAMA
        $nama = '';
        if($tamiu->cacah_tamiu->penduduk->gelar_depan != NULL){
            $nama = $nama.$tamiu->cacah_tamiu->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$tamiu->cacah_tamiu->penduduk->nama;
        if($tamiu->cacah_tamiu->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$tamiu->cacah_tamiu->penduduk->gelar_belakang;
        }
        $tamiu->cacah_tamiu->penduduk->nama = $nama;

        //SET REVERSE TANGGAL REGISTRASI
        $tamiu->tanggal_registrasi = date("d-m-Y", strtotime($tamiu->tanggal_registrasi));

        //GET MASTER LAiNNYA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();

        $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();

        //GET ANGGOTA
        $anggota_tamiu = AnggotaTamiu::with('cacah_tamiu.penduduk')->where('tamiu_id', $tamiu->id)->where('cacah_tamiu_id', '!=', $cacah_tamiu->id)->get();
        return view('pages.banjar.cacah_tamiu.edit_tamiu_wni', compact(
            'penduduk', 'cacah_tamiu', 'anggota_krama',
            'tamiu', 'pekerjaans', 'pendidikans', 
            'anggota_tamiu', 'desas', 'banjar_dinas'));
    }

    public function edit_tamiu_wna($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $krama = CacahTamiu::find($id);
        $wna = WNA::find($krama->wna_id);
        $negara = Negara::find($wna->negara_id);
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        return view('pages.banjar.cacah_tamiu.edit_tamiu_wna', compact('krama', 'wna', 'negara', 'banjar_dinas'));
    }

    public function update_tamiu_wni($id, Request $request)
    {
        $banjar_adat_id = session()->get('banjar_adat_id');

        //GET MASTER CACAH
        $cacah_tamiu = CacahTamiu::find($id);
        $anggota_tamiu = AnggotaTamiu::where('cacah_tamiu_id', $cacah_tamiu->id)->where('status', '1')->first();
        $penduduk = Penduduk::find($cacah_tamiu->penduduk_id);

        //GET MASTER KRAMA
        $tamiu = Tamiu::with('cacah_tamiu')->find($anggota_tamiu->tamiu_id);
        $cacah = CacahTamiu::find($tamiu->cacah_tamiu_id);
        $penduduk_tamiu = Penduduk::find($cacah->penduduk_id);

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
        $penduduk->desa_id = $penduduk_tamiu->desa_id;
        $penduduk->alamat = $penduduk_tamiu->alamat;
        $penduduk->koordinat_alamat = $penduduk_tamiu->koordinat_alamat;
        if($request->cari_ayah_manual){
            $penduduk->ayah_kandung_id = $request->ayah_kandung_manual;
        }else{
            $penduduk->ayah_kandung_id = $request->ayah_kandung;
        }
        if($request->cari_ibu_manual){
            $penduduk->ibu_kandung_id = $request->ibu_kandung_manual;
        }else{
            $penduduk->ibu_kandung_id = $request->ibu_kandung;
        }
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
        $cacah_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_tamiu->desa_asal_id = $cacah->desa_asal_id;
        $cacah_tamiu->update();

        //UPDATE ANGGOTA
        $anggota_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_tamiu->status_hubungan = $request->status_hubungan;
        $anggota_tamiu->update();
        return redirect()->route('banjar-cacah-tamiu-home')->with('success', 'Cacah Tamiu berhasil diperbaharui');
    }

    public function update_tamiu_wna($id, Request $request)
    {
        $tamiu = CacahTamiu::find($id);
        $wna = WNA::find($tamiu->wna_id);

        $validator = Validator::make($request->all(), [
            'nomor_paspor' => 'required|unique:tb_wna',
            'nomor_paspor' => [
                Rule::unique('tb_wna')->ignore($wna->id),
            ],
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'tanggal_masuk' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'negara' => 'required',
            'banjar_dinas_id' => 'required'
        ],[
            'nomor_paspor.unique' => "Nomor Paspor telah terdaftar",
            'nomor_paspor.required' => "Nomor Paspor wajib diisi",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_masuk.required' => "Tanggal Masuk wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'alamat.required' => "Alamat wajib diisi",
            'negara.required' => "Negara Asal wajib dipilih",
            'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $wna->nama = $request->nama;
        $wna->tempat_lahir = $request->tempat_lahir;
        $wna->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $wna->jenis_kelamin = $request->jenis_kelamin;
        $wna->alamat = $request->alamat;
        $wna->koordinat_alamat = $request->koordinat_alamat;
        $wna->negara_id = $request->negara;
        //SET FOTO PENDUDUK
        if($request->foto != ''){
            $image_parts = explode(';base64', $request->foto);
            $image_type_aux = explode('image/', $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $filename = uniqid().'.png';
            $fileLocation = '/image/wna/'.$wna->nomor_paspor.'/foto';
            $path = $fileLocation."/".$filename;
            $wna->foto = '/storage'.$path;
            Storage::disk('public')->put($path, $image_base64);
        }
        $wna->update();

        $tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        if($request->tanggal_keluar != ''){
            $tamiu->status = '0';
            $tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        }
        $tamiu->update();
        return redirect()->route('banjar-cacah-tamiu-home')->with(['success' => 'Cacah Tamiu berhasil diperbaharui', 'tamiu' => 'wna']);
    }

    public function delete_tamiu_wni($id, Request $request)
    {
        //GET DATA LAMA
        $cacah_tamiu = CacahTamiu::find($id);
        $anggota_tamiu = AnggotaTamiu::where('cacah_tamiu_id', $cacah_tamiu->id)->where('status', '1')->first();
        $tamiu_lama = Tamiu::find($anggota_tamiu->tamiu_id);
        $anggota_tamiu_lama = AnggotaTamiu::where('tamiu_id', $tamiu_lama->id)->get();

        //COPY DATA
        $tamiu_baru = new Tamiu();
        $tamiu_baru->nomor_tamiu = $tamiu_lama->nomor_tamiu;
        $tamiu_baru->banjar_adat_id = $tamiu_lama->banjar_adat_id;
        $tamiu_baru->cacah_tamiu_id = $tamiu_lama->cacah_tamiu_id;
        $tamiu_baru->status = '1';
        $tamiu_baru->alasan_perubahan = 'Penghapusan Anggota Keluarga';
        $tamiu_baru->tanggal_registrasi = $tamiu_lama->tanggal_registrasi;
        $tamiu_baru->save();

        foreach($anggota_tamiu_lama as $anggota_lama){
            if($anggota_lama->id != $anggota_tamiu->id){
                $anggota_tamiu_baru = new AnggotaTamiu();
                $anggota_tamiu_baru->tamiu_id = $tamiu_baru->id;
                $anggota_tamiu_baru->cacah_tamiu_id = $anggota_lama->cacah_tamiu_id;
                $anggota_tamiu_baru->status_hubungan = $anggota_lama->status_hubungan;
                $anggota_tamiu_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                $anggota_tamiu_baru->status = '1';
                $anggota_tamiu_baru->save();

                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }else{
                //NONAKTIFKAN YANG DIHAPUS
                $anggota_lama->status = '0';
                $anggota_lama->alasan_keluar = $request->alasan_keluar;
                $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
                $anggota_lama->update();
            }
        }

        //NONAKTIFKAN DATA LAMA
        $tamiu_lama->status = '0';
        $tamiu_lama->update();

        //NONAKTIFKAN CACAHNYA
        $cacah_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_tamiu->alasan_keluar = $request->alasan_keluar;
        $cacah_tamiu->status = '0';
        $cacah_tamiu->update();
        return back()->with('success', 'Cacah Tamiu berhasil dikeluarkan');
    }

    public function delete_tamiu_wna($id, Request $request)
    {
        $tamiu = CacahTamiu::find($id);
        $tamiu->status = '0';
        $tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        $tamiu->alasan_keluar = $request->alasan_keluar;
        $tamiu->update();
        return back()->with(['success' => 'Cacah Tamiu berhasil dinonaktifkan', 'tamiu' => 'wna']);
    }

    public function detail_wni($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $krama = CacahTamiu::find($id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);

        $desa_asal = DesaDinas::find($krama->desa_asal_id);
        $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
        $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
        $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
        return view('pages.banjar.cacah_tamiu.detail_cacah_tamiu_wni', compact('krama', 'penduduk', 'desa', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
    }

    public function detail_wna($id){
        $desa_adat_id = session()->get('desa_adat_id');

        $krama = CacahTamiu::find($id);
        $wna = WNA::find($krama->wna_id);
        $wna->koordinat_alamat = json_decode($wna->koordinat_alamat);
        $negara = Negara::find($wna->negara_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.banjar.cacah_tamiu.detail_cacah_tamiu_wna', compact('krama', 'wna', 'negara', 'banjar_adat', 'banjar_dinas'));
    }

    public function daftar_riwayat_wni($id){
        //GET CACAH
        $cacah_tamiu = CacahTamiu::with('penduduk')->find($id);

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
        return view('pages.banjar.cacah_tamiu.daftar_riwayat_wni', compact('riwayats', 'penduduk','cacah_tamiu'));
    }

    public function detail_riwayat_wni($id, $id_riwayat){
        //GET CACAH
        $cacah_tamiu = CacahTamiu::with('penduduk')->find($id);

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
        return view('pages.banjar.cacah_tamiu.detail_riwayat_wni', compact('krama', 'curr_penduduk','penduduk', 'desa', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
    }
}
