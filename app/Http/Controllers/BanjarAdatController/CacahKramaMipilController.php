<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluargaKrama;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\CacahTamiu;
use App\Models\DesaAdat;
use App\Models\KramaMipil;
use App\Models\LogPenduduk;
use App\Models\Tempekan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CacahKramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kk = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('cacah_krama_mipil_id');
        $kramas = CacahKramaMipil::with('penduduk', 'tempekan')->where('banjar_adat_id', $banjar_adat_id)->whereNotIn('id', $kk);
        if (isset($request->status)) {
            if($request->status == '1'){
                $kramas->where('status', '1');
            }else if($request->status == '0'){
                $kramas->where('status', '0')->where('tanggal_nonaktif', '!=', NULL);
            }else if($request->status == '2'){
                $kramas->where('status', '1');
                if(isset($request->rentang_waktu)){
                    $kramas->whereHas('penduduk', function ($query) use ($request) {
                        $rentang_waktu = explode(' - ', $request->rentang_waktu);
                        $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                        $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                        return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
                    });
                }
        
                if (isset($request->golongan_darah)) {
                    $kramas->whereHas('penduduk', function ($query) use ($request) {
                        return $query->whereIn('golongan_darah', $request->golongan_darah);
                    });
                }
        
                if (isset($request->jenis_kelamin)) {
                    $kramas->whereHas('penduduk', function ($query) use ($request) {
                        return $query->where('jenis_kelamin', $request->jenis_kelamin);
                    });
                }
        
                if (isset($request->tempekan)) {
                    $kramas->whereIn('tempekan_id', $request->tempekan);
                }
        
                if (isset($request->pekerjaan)) {
                    $kramas->whereHas('penduduk', function ($query) use ($request) {
                        return $query->whereIn('profesi_id', $request->pekerjaan);
                    });
                }
        
                if (isset($request->pendidikan)) {
                    $kramas->whereHas('penduduk', function ($query) use ($request) {
                        return $query->whereIn('pendidikan_id', $request->pendidikan);
                    });
                }
                $kramas->orWhere(function ($query) use ($banjar_adat_id) {
                    $query->where('status', '0')
                        ->where('tanggal_nonaktif', '!=', NULL)->where('banjar_adat_id', $banjar_adat_id);
                });
            }
        }else{
            $kramas->where('status', '1');
        }

        if(isset($request->rentang_waktu)){
            $kramas->whereHas('penduduk', function ($query) use ($request) {
                $rentang_waktu = explode(' - ', $request->rentang_waktu);
                $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
            });
        }

        if (isset($request->golongan_darah)) {
            $kramas->whereHas('penduduk', function ($query) use ($request) {
                return $query->whereIn('golongan_darah', $request->golongan_darah);
            });
        }

        if (isset($request->jenis_kelamin)) {
            $kramas->whereHas('penduduk', function ($query) use ($request) {
                return $query->where('jenis_kelamin', $request->jenis_kelamin);
            });
        }

        if (isset($request->tempekan)) {
            $kramas->whereIn('tempekan_id', $request->tempekan);
        }

        if (isset($request->pekerjaan)) {
            $kramas->whereHas('penduduk', function ($query) use ($request) {
                return $query->whereIn('profesi_id', $request->pekerjaan);
            });
        }

        if (isset($request->pendidikan)) {
            $kramas->whereHas('penduduk', function ($query) use ($request) {
                return $query->whereIn('pendidikan_id', $request->pendidikan);
            });
        }

        $kramas->orderBy('tanggal_registrasi', 'DESC');
        
        return Datatables::of($kramas)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $return = '';
                if($data->status == '0'){
                    $return .= '<a class="btn btn-primary btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-cacah-krama-mipil-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                }else{
                    $return .= '<a class="btn btn-primary btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-cacah-krama-mipil-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                    $return .= '<a class="btn btn-warning btn-sm mx-1  my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="'.route('banjar-cacah-krama-mipil-edit', $data->id).'"><i class="fas fa-edit"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_krama('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }
    
    public function index(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $pekerjaan = Pekerjaan::get();
        $pendidikan = Pendidikan::get();
        return view('pages.banjar.cacah_krama_mipil.cacah_krama_mipil', compact('tempekan', 'pekerjaan', 'pendidikan'));
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
        $desa_adat_id = $banjar_adat->id;
        $banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        if($penduduk){
            $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));
            $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->where('status', '1')->first();
            $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->whereIn('banjar_adat_id', $banjar_adat_id)->where('tanggal_keluar', NULL)->first();
            $cacah_tamiu = CacahTamiu::where('penduduk_id', $penduduk->id)->whereIn('banjar_adat_id', $banjar_adat_id)->where('tanggal_keluar', NULL)->first();
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

    public function get_anggota_keluarga($id){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->get();

        //SET NAMA LENGKAP KRAMA MIPIL
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        //GET ANGGOTA MENINGGAL
        $krama_mipil_lengkap_id =  KramaMipil::where('nomor_krama_mipil', $krama_mipil->nomor_krama_mipil)->where('tanggal_nonaktif' , NULL)->pluck('id')->toArray();
        $anggota_krama_mipil_lengkap = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->whereIn('krama_mipil_id', $krama_mipil_lengkap_id)->groupBy('cacah_krama_mipil_id')->get();
        $anggota_krama_mipil_meninggal = collect();

        foreach($anggota_krama_mipil_lengkap as $anggota){
            if($anggota->cacah_krama_mipil->penduduk->tanggal_kematian != NULL){
                $nama = '(Alm)';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan;
                }
                $nama = $nama.' '.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;
                $anggota_krama_mipil_meninggal->push($anggota);
            }
        }


        //SET NAMA LENGKAP ANGGOTA
        foreach($anggota_krama_mipil as $anggota){
            $nama = '';
            if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$anggota->cacah_krama_mipil->penduduk->nama;
            if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $anggota->cacah_krama_mipil->penduduk->nama = $nama;
        }

        //GET ALAMAT KRAMA MIPIL
        $penduduk_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        $penduduk_krama_mipil = Penduduk::find($penduduk_krama_mipil->penduduk_id);

        $desa = DesaDinas::find($penduduk_krama_mipil->desa_id);
        $kecamatan = Kecamatan::where('id', $desa->kecamatan_id)->first();
        $kabupaten = Kabupaten::where('id', $kecamatan->kabupaten_id)->first();
        $provinsi = Provinsi::where('id', $kabupaten->provinsi_id)->first();
        
        //Data Master
        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();
        return response()->json([
            'krama_mipil' => $krama_mipil,
            'anggota_krama_mipil'=> $anggota_krama_mipil,
            'anggota_krama_mipil_meninggal' => $anggota_krama_mipil_meninggal,
            'desa' => $desa,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
            'provinsi' => $provinsi,
            'desas' => $desas,
            'kecamatans' => $kecamatans,
            'kabupatens' => $kabupatens,
            'provinsis' => $provinsis,
        ]);
    }

    public function create(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        return view('pages.banjar.cacah_krama_mipil.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'tempekan', 'banjar_dinas'));
    }

    public function store(Request $request)
    {
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil')->find($request->krama_mipil);
        $penduduk_krama_mipil = Penduduk::find($krama_mipil->cacah_krama_mipil->penduduk_id);

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
                'status_hubungan' => 'required',
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
                'provinsi.required' => "Provinsi wajib dipilih",
                'kabupaten.required' => "Kabupaten wajib dipilih",
                'kecamatan.required' => "Kecamatan wajib dipilih",
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
                'status_hubungan.required' => "Status Hubungan wajib dipilih",
                'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
            ]);
    
            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $penduduk = new Penduduk();
            $penduduk->nik = $request->nik;
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
            if($request->pisah_tinggal){
                $penduduk->desa_id = $request->desa_pisah;
                $penduduk->alamat = $request->alamat_pisah;
                $penduduk->koordinat_alamat = $request->koordinat_alamat_pisah;
            }else{
                $penduduk->desa_id = $penduduk_krama_mipil->desa_id;
                $penduduk->alamat = $penduduk_krama_mipil->alamat;
                $penduduk->koordinat_alamat = $penduduk_krama_mipil->koordinat_alamat;
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
                'status_hubungan' => 'required',
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
                'provinsi.required' => "Provinsi wajib dipilih",
                'kabupaten.required' => "Kabupaten wajib dipilih",
                'kecamatan.required' => "Kecamatan wajib dipilih",
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
                'status_hubungan.required' => "Status Hubungan wajib dipilih",
                'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
            ]);
    
            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $penduduk->nik = $request->nik;
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
            if($request->pisah_tinggal){
                $penduduk->desa_id = $request->desa_pisah;
                $penduduk->alamat = $request->alamat_pisah;
                $penduduk->koordinat_alamat = $request->koordinat_alamat_pisah;
            }else{
                $penduduk->desa_id = $penduduk_krama_mipil->desa_id;
                $penduduk->alamat = $penduduk_krama_mipil->alamat;
                $penduduk->koordinat_alamat = $penduduk_krama_mipil->koordinat_alamat;
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

        //NOMOR CACAH KRAMA MIPIL
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $nomor_cacah_krama_mipil = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'01'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.$jumlah_penduduk_tanggal_sama;
        }
        
        //INSERT CACAH KRAMA MIPIL
        $cacah_krama_mipil = new CacahKramaMipil();
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_mipil->tempekan_id = $krama_mipil->cacah_krama_mipil->tempekan_id;
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->status = '1';
        $cacah_krama_mipil->jenis_kependudukan = $krama_mipil->cacah_krama_mipil->jenis_kependudukan;
        if($krama_mipil->cacah_krama_mipil->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $krama_mipil->cacah_krama_mipil->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();

        //GET DATA LAMA
        $krama_mipil_lama = KramaMipil::find($request->krama_mipil);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

        //COPY DATA
        $krama_mipil_baru = new KramaMipil();
        $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
        $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
        $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
        $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
        $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
        $krama_mipil_baru->status = '1';
        $krama_mipil_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru';
        $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
        $krama_mipil_baru->save();

        foreach($anggota_krama_mipil_lama as $anggota_lama){
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
        }

        //INSERT ANGGOTA BARU
        $anggota_krama_mipil = new AnggotaKramaMipil();
        $anggota_krama_mipil->krama_mipil_id = $krama_mipil_baru->id;
        $anggota_krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        $anggota_krama_mipil->status_hubungan = $request->status_hubungan;
        $anggota_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_krama_mipil->status = '1';
        $anggota_krama_mipil->save();

        //NONAKTIFKAN DATA LAMA
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();
        return redirect()->route('banjar-cacah-krama-mipil-home')->with('success', 'Cacah Krama Mipil berhasil ditambahkan');
    }

    public function edit($id){
        //GET MASTER CACAH
        $cacah_krama_mipil = CacahKramaMipil::find($id);
        $anggota_krama = AnggotaKramaMipil::where('cacah_krama_mipil_id', $id)->where('status', '1')->first();
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_mipil->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK KRAMA
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($anggota_krama->krama_mipil_id);
        $cacah = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        $penduduk_krama = Penduduk::find($cacah->penduduk_id);

        //REVERSE TANGGAL LAHIR DAN REGISTRASI CACAH KRAMA MIPIL
        $anggota_krama->tanggal_registrasi = date("d-m-Y", strtotime($anggota_krama->tanggal_registrasi));
        $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));

        //SET NAMA LENGKAP KRAMA
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != NULL){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        //SET REVERSE TANGGAL REGISTRASI
        $krama_mipil->tanggal_registrasi = date("d-m-Y", strtotime($krama_mipil->tanggal_registrasi));

        //SET STATUS TINGGAL
        if($penduduk->alamat != $krama_mipil->cacah_krama_mipil->penduduk->alamat){
            $pisah_tinggal = true;
        }else{
            $pisah_tinggal = false;
        }

        //GET MASTER ALAMAT KRAMA MIPIL
        $desa = DesaDinas::find($penduduk_krama->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);

        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();

        //GET MASTER ALAMAT PENDUDUK
        $desa_penduduk = DesaDinas::find($penduduk->desa_id);
        $kecamatan_penduduk = Kecamatan::find($desa_penduduk->kecamatan_id);
        $kabupaten_penduduk = Kabupaten::find($kecamatan_penduduk->kabupaten_id);
        $provinsi_penduduk = Provinsi::find($kabupaten_penduduk->provinsi_id);

        $kabupatens_penduduk = Kabupaten::where('provinsi_id', $provinsi_penduduk ->id)->get();
        $kecamatans_penduduk = Kecamatan::where('kabupaten_id', $kabupaten_penduduk ->id)->get();
        $desas_penduduk = DesaDinas::where('kecamatan_id', $kecamatan_penduduk ->id)->get();

        //GET MASTER LAiNNYA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();

        //GET ANGGOTA
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where('cacah_krama_mipil_id', '!=', $cacah_krama_mipil->id)->get();
        return view('pages.banjar.cacah_krama_mipil.edit', compact(
            'penduduk', 'cacah_krama_mipil', 'anggota_krama', 'pisah_tinggal', 
            'krama_mipil', 'pekerjaans', 'pendidikans', 
            'provinsis', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 
            'kabupatens', 'kecamatans', 'desas', 
            'provinsi_penduduk', 'kabupaten_penduduk', 'kecamatan_penduduk', 'desa_penduduk',
            'kabupatens_penduduk', 'kecamatans_penduduk', 'desas_penduduk',
            'anggota_krama_mipil'));
    }

    public function update($id, Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');

        //GET MASTER CACAH
        $cacah_krama_mipil = CacahKramaMipil::find($id);
        $anggota_krama_mipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->where('status', '1')->first();
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        //GET MASTER KRAMA
        $krama_mipil = KramaMipil::with('cacah_krama_mipil')->find($anggota_krama_mipil->krama_mipil_id);
        $cacah = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        $penduduk_krama_mipil = Penduduk::find($krama_mipil->cacah_krama_mipil->penduduk_id);

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
            'status_hubungan' => 'required',
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
            'status_hubungan.required' => "Status Hubungan wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $penduduk->nik = $request->nik;
        $penduduk->profesi_id = $request->pekerjaan;
        $penduduk->pendidikan_id = $request->pendidikan;
        $penduduk->gelar_depan = $request->gelar_depan;
        $penduduk->nama = $request->nama;
        $penduduk->gelar_belakang = $request->gelar_belakang;
        $penduduk->nama_alias = $request->nama_alias;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->status_perkawinan = $request->status_perkawinan;
        $penduduk->telepon = $request->telepon;
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
        if($request->pisah_tinggal){
            $penduduk->desa_id = $request->desa_pisah;
            $penduduk->alamat = $request->alamat_pisah;
            $penduduk->koordinat_alamat = $request->koordinat_alamat_pisah;
        }else{
            $penduduk->desa_id = $penduduk_krama_mipil->desa_id;
            $penduduk->alamat = $penduduk_krama_mipil->alamat;
            $penduduk->koordinat_alamat = $penduduk_krama_mipil->koordinat_alamat;
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
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_krama_mipil->update();

        //UPDATE ANGGOTA
        $anggota_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_krama_mipil->status_hubungan = $request->status_hubungan;
        $anggota_krama_mipil->update();

        return redirect()->route('banjar-cacah-krama-mipil-home')->with('success', 'Cacah Krama Mipil berhasil diperbaharui');
    }

    public function destroy($id, Request $request)
    {
        //GET DATA LAMA
        $cacah_krama_mipil = CacahKramaMipil::find($id);
        $anggota_krama_mipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->where('status', '1')->first();
        $krama_mipil_lama = KramaMipil::find($anggota_krama_mipil->krama_mipil_id);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_mipil->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //COPY DATA
        $krama_mipil_baru = new KramaMipil();
        $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
        $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
        $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
        $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
        $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
        $krama_mipil_baru->status = '1';
        $krama_mipil_baru->alasan_perubahan = 'Penghapusan Anggota Keluarga';
        $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
        $krama_mipil_baru->save();

        foreach($anggota_krama_mipil_lama as $anggota_lama){
            if($anggota_lama->id != $anggota_krama_mipil->id){
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
                //NONAKTIFKAN YANG DIHAPUS
                $anggota_lama->status = '0';
                $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
                $anggota_lama->alasan_keluar = date("Y-m-d", strtotime($request->alasan_keluar));
                $anggota_lama->update();
            }
        }

        //NONAKTIFKAN DATA LAMA
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();

        //NONAKTIFKAN CACAHNYA
        $cacah_krama_mipil->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_krama_mipil->alasan_keluar = date("Y-m-d", strtotime($request->alasan_keluar));
        $cacah_krama_mipil->status = '0';
        $cacah_krama_mipil->update();

        return back()->with('success', 'Cacah Krama Mipil berhasil dikeluarkan');
    }

    public function detail($id){
        //GET DATA
        $krama = CacahKramaMipil::find($id);
        $banjar_adat_id = session()->get('banjar_adat_id');

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($krama->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        $banjar_adat = BanjarAdat::find($banjar_adat_id);

        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat->id)->get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);
        $anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $krama->id)->first();
        return view('pages.banjar.cacah_krama_mipil.detail', compact('krama', 'anggota', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas'));
    }

    public function daftar_riwayat($id){
        //GET CACAH
        $cacah_krama_mipil = CacahKramaMipil::with('penduduk')->find($id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_mipil->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
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
        return view('pages.banjar.cacah_krama_mipil.daftar_riwayat', compact('riwayats', 'penduduk','cacah_krama_mipil'));
    }

    public function detail_riwayat($id, $id_riwayat){
        //GET CACAH
        $cacah_krama_mipil = CacahKramaMipil::with('penduduk')->find($id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_mipil->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $curr_penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
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
        $krama = $cacah_krama_mipil;
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = LogPenduduk::with('pekerjaan', 'pendidikan')->find($id_riwayat);
        if($krama->penduduk_id != $penduduk->penduduk_id){
            return redirect()->back();
        }

        //GET DATA
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat->id)->get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);
        $anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $krama->id)->where('status', '1')->first();
        return view('pages.banjar.cacah_krama_mipil.detail_riwayat', compact('curr_penduduk', 'krama', 'penduduk', 'anggota', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas'));
    }
}
