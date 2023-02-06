<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKramaTamiu;
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
use App\Models\KramaTamiu;
use App\Models\LogPenduduk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CacahKramaTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kk = KramaTamiu::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('cacah_krama_tamiu_id');
        $kramas = CacahKramaTamiu::with('penduduk','banjar_dinas')->where('banjar_adat_id', $banjar_adat_id)->whereNotIn('id', $kk);

        if (isset($request->status)) {
            if($request->status == '1'){
                $kramas->where('status', '1');
            }else if($request->status == '0'){
                $kramas->where('status', '0');
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

        if(isset($request->rentang_waktu_masuk)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu_masuk);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $kramas->whereBetween('tanggal_masuk', [$start_date, $end_date]);
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

        if (isset($request->asal)) {
            $kramas->where('asal', $request->asal);
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

        $kramas->orderBy('tanggal_masuk', 'DESC');

        return DataTables::of($kramas)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $return = '';
                $return .= '<a class="btn btn-primary btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-cacah-krama-tamiu-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                if($data->status == '1'){
                    $return .= '<a class="btn btn-warning btn-sm mx-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" href="'.route('banjar-cacah-krama-tamiu-edit', $data->id).'"><i class="fas fa-edit"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_krama('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }

    public function datatable_krama_tamiu(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kramas = KramaTamiu::with('cacah_krama_tamiu.penduduk', 'cacah_krama_tamiu.banjar_dinas', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->get()->map(function ($item){
            $item->anggota_keluarga = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->where('krama_tamiu_id', $item->id)->get();
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
            if($data->cacah_krama_tamiu->penduduk->gelar_depan != ''){
                $nama = $nama.$data->cacah_krama_tamiu->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$data->cacah_krama_tamiu->penduduk->nama;
            if($data->cacah_krama_tamiu->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$data->cacah_krama_tamiu->penduduk->gelar_belakang;
            }
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_tamiu('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function get_anggota_keluarga($id){
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk')->find($id);
        $anggota_krama_tamiu = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->where('krama_tamiu_id', $krama_tamiu->id)->get();

        //SET NAMA LENGKAP KRAMA MIPIL
        $nama = '';
        if($krama_tamiu->cacah_krama_tamiu->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_tamiu->cacah_krama_tamiu->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$krama_tamiu->cacah_krama_tamiu->penduduk->nama;
        if($krama_tamiu->cacah_krama_tamiu->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_tamiu->cacah_krama_tamiu->penduduk->gelar_belakang;
        }
        $krama_tamiu->cacah_krama_tamiu->penduduk->nama = $nama;

        //GET ANGGOTA MENINGGAL
        $krama_tamiu_lengkap_id =  KramaTamiu::where('nomor_krama_tamiu', $krama_tamiu->nomor_krama_tamiu)->where('tanggal_nonaktif' , NULL)->pluck('id')->toArray();
        $anggota_krama_tamiu_lengkap = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->whereIn('krama_tamiu_id', $krama_tamiu_lengkap_id)->groupBy('cacah_krama_tamiu_id')->get();

        $anggota_krama_tamiu_meninggal = collect();

        foreach($anggota_krama_tamiu_lengkap as $anggota){
            if($anggota->cacah_krama_tamiu->penduduk->tanggal_kematian != NULL){
                $nama = '(Alm)';
                if($anggota->cacah_krama_tamiu->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_tamiu->penduduk->gelar_depan;
                }
                $nama = $nama.' '.$anggota->cacah_krama_tamiu->penduduk->nama;
                if($anggota->cacah_krama_tamiu->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_tamiu->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_tamiu->penduduk->nama = $nama;
                $anggota_krama_tamiu_meninggal->push($anggota);
            }
        }


        //SET NAMA LENGKAP ANGGOTA
        foreach($anggota_krama_tamiu as $anggota){
            $nama = '';
            if($anggota->cacah_krama_tamiu->penduduk->gelar_depan != ''){
                $nama = $nama.$anggota->cacah_krama_tamiu->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$anggota->cacah_krama_tamiu->penduduk->nama;
            if($anggota->cacah_krama_tamiu->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anggota->cacah_krama_tamiu->penduduk->gelar_belakang;
            }
            $anggota->cacah_krama_tamiu->penduduk->nama = $nama;
        }
        return response()->json([
            'krama_tamiu' => $krama_tamiu,
            'anggota_krama_tamiu'=> $anggota_krama_tamiu,
            'anggota_krama_tamiu_meninggal' => $anggota_krama_tamiu_meninggal,
            'nomor_krama_tamiu' => $krama_tamiu->nomor_krama_tamiu
        ]);
    }
    
    public function index(){
        $pekerjaan = Pekerjaan::get();
        $pendidikan = Pendidikan::get();
        return view('pages.banjar.cacah_krama_tamiu.cacah_krama_tamiu', compact('pekerjaan', 'pendidikan'));
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
        if($penduduk){
            $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));
            $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->first();
            $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->first();
            $cacah_tamiu = CacahTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->first();
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

    public function create(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);

        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
        return view('pages.banjar.cacah_krama_tamiu.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'kabupatens', 'banjar_dinas', 'desas'));
    }

    public function store(Request $request)
    {
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu')->find($request->krama_tamiu);
        $cacah_krama = CacahKramaTamiu::find($krama_tamiu->cacah_krama_tamiu_id);
        $penduduk_krama_tamiu = Penduduk::find($cacah_krama->penduduk_id);

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
                'status_hubungan' => 'required',
                'tanggal_registrasi' => 'required',
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
                'status_hubungan.required' => "Status Hubungan wajib dipilih",
                'tanggal_registrasi.required' => "Tanggal registrasi wajib diisi",
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
            $penduduk->desa_id = $penduduk_krama_tamiu->desa_id;
            $penduduk->alamat = $penduduk_krama_tamiu->alamat;
            $penduduk->koordinat_alamat = $penduduk_krama_tamiu->koordinat_alamat;
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
                'status_hubungan' => 'required',
                'tanggal_registrasi' => 'required',
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
                'status_hubungan.required' => "Status Hubungan wajib dipilih",
                'tanggal_registrasi.required' => "Tanggal registrasi wajib diisi",
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
            $penduduk->desa_id = $penduduk_krama_tamiu->desa_id;
            $penduduk->alamat = $penduduk_krama_tamiu->alamat;
            $penduduk->koordinat_alamat = $penduduk_krama_tamiu->koordinat_alamat;
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

        //NOMOR CACAH KRAMA TAMIU
        $kramas = CacahKramaTamiu::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();
        $banjar_adat_id = session()->get('banjar_adat_id');
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $nomor_cacah_krama_tamiu = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.'02'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.$jumlah_penduduk_tanggal_sama;
        }

        //INSERT CACAH KRAMA TAMIU
        $cacah_krama_tamiu = new CacahKramaTamiu();
        $cacah_krama_tamiu->nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu;
        $cacah_krama_tamiu->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_tamiu->banjar_dinas_id = $cacah_krama->banjar_dinas_id;
        $cacah_krama_tamiu->penduduk_id = $penduduk->id;
        $cacah_krama_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_krama_tamiu->asal = $cacah_krama->asal;
        $cacah_krama_tamiu->alamat_asal = $cacah_krama->alamat_asal;
        if($cacah_krama->asal == 'dalam_bali'){
            $cacah_krama_tamiu->banjar_adat_asal_id = $cacah_krama->banjar_adat_asal_id; 
        }else if($cacah_krama->asal == 'luar_bali'){
            $cacah_krama_tamiu->desa_asal_id = $cacah_krama->desa_asal_id;
        }
        $cacah_krama_tamiu->save();

        //GET DATA LAMA
        $krama_tamiu_lama = KramaTamiu::find($request->krama_tamiu);
        $anggota_krama_tamiu_lama = AnggotaKramaTamiu::where('krama_tamiu_id', $krama_tamiu_lama->id)->get();

        //COPY DATA
        $krama_tamiu_baru = new KramaTamiu();
        $krama_tamiu_baru->nomor_krama_tamiu = $krama_tamiu_lama->nomor_krama_tamiu;
        $krama_tamiu_baru->banjar_adat_id = $krama_tamiu_lama->banjar_adat_id;
        $krama_tamiu_baru->cacah_krama_tamiu_id = $krama_tamiu_lama->cacah_krama_tamiu_id;
        $krama_tamiu_baru->status = '1';
        $krama_tamiu_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru';
        $krama_tamiu_baru->tanggal_registrasi = $krama_tamiu_lama->tanggal_registrasi;
        $krama_tamiu_baru->save();

        foreach($anggota_krama_tamiu_lama as $anggota_lama){
            $anggota_krama_tamiu_baru = new AnggotaKramaTamiu();
            $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
            $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $anggota_lama->cacah_krama_tamiu_id;
            $anggota_krama_tamiu_baru->status_hubungan = $anggota_lama->status_hubungan;
            $anggota_krama_tamiu_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
            $anggota_krama_tamiu_baru->status = '1';
            $anggota_krama_tamiu_baru->save();

            //NONAKTIFKAN ANGGOTA LAMA
            $anggota_lama->status = '0';
            $anggota_lama->update();
        }

        //INSERT ANGGOTA BARU
        $anggota_krama_tamiu = new AnggotaKramaTamiu();
        $anggota_krama_tamiu->krama_tamiu_id = $krama_tamiu_baru->id;
        $anggota_krama_tamiu->cacah_krama_tamiu_id = $cacah_krama_tamiu->id;
        $anggota_krama_tamiu->status_hubungan = $request->status_hubungan;
        $anggota_krama_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_krama_tamiu->status = '1';
        $anggota_krama_tamiu->save();

        //NONAKTIFKAN DATA LAMA
        $krama_tamiu_lama->status = '0';
        $krama_tamiu_lama->update();
        return redirect()->route('banjar-cacah-krama-tamiu-home')->with('success', 'Cacah Krama Tamiu berhasil ditambahkan');
    }

    public function edit($id){
        //GET MASTER ANGGOTA
        $cacah_krama_tamiu = CacahKramaTamiu::find($id);
        $anggota_krama = AnggotaKramaTamiu::where('cacah_krama_tamiu_id', $cacah_krama_tamiu->id)->where('status', '1')->first();
        $penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK KRAMA
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk')->find($anggota_krama->krama_tamiu_id);
        $cacah = CacahKramaTamiu::find($krama_tamiu->cacah_krama_tamiu_id);
        $penduduk_krama = Penduduk::find($cacah->penduduk_id);

        //REVERSE TANGGAL LAHIR DAN REGISTRASI CACAH KRAMA MIPIL
        $anggota_krama->tanggal_registrasi = date("d-m-Y", strtotime($anggota_krama->tanggal_registrasi));
        $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));

        //SET NAMA LENGKAP KRAMA
        $nama = '';
        if($krama_tamiu->cacah_krama_tamiu->penduduk->gelar_depan != NULL){
            $nama = $nama.$krama_tamiu->cacah_krama_tamiu->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$krama_tamiu->cacah_krama_tamiu->penduduk->nama;
        if($krama_tamiu->cacah_krama_tamiu->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$krama_tamiu->cacah_krama_tamiu->penduduk->gelar_belakang;
        }
        $krama_tamiu->cacah_krama_tamiu->penduduk->nama = $nama;

        //SET REVERSE TANGGAL REGISTRASI
        $krama_tamiu->tanggal_registrasi = date("d-m-Y", strtotime($krama_tamiu->tanggal_registrasi));

        //GET MASTER LAiNNYA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();

        $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();

        //GET ANGGOTA
        $anggota_krama_tamiu = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->where('krama_tamiu_id', $krama_tamiu->id)->where('cacah_krama_tamiu_id', '!=', $cacah_krama_tamiu->id)->get();
        return view('pages.banjar.cacah_krama_tamiu.edit', compact(
            'penduduk', 'cacah_krama_tamiu', 'anggota_krama',
            'krama_tamiu', 'pekerjaans', 'pendidikans', 
            'anggota_krama_tamiu', 'desas', 'banjar_dinas'));
    }

    public function update($id, Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');

        //GET MASTER CACAH
        $cacah_krama_tamiu = CacahKramaTamiu::find($id);
        $anggota_krama_tamiu = AnggotaKramaTamiu::where('cacah_krama_tamiu_id', $cacah_krama_tamiu->id)->where('status', '1')->first();
        $penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);

        //GET MASTER KRAMA
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu')->find($anggota_krama_tamiu->krama_tamiu_id);
        $cacah = CacahKramaTamiu::find($krama_tamiu->cacah_krama_tamiu_id);
        $penduduk_krama_tamiu = Penduduk::find($krama_tamiu->cacah_krama_tamiu->penduduk_id);

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
        $cacah_krama_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_registrasi));

        //UPDATE ANGGOTA
        $anggota_krama_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_krama_tamiu->status_hubungan = $request->status_hubungan;
        $anggota_krama_tamiu->update();

        return redirect()->route('banjar-cacah-krama-tamiu-home')->with('success', 'Cacah Krama Tamiu berhasil diperbaharui');
    }

    public function destroy($id, Request $request)
    {
        //GET DATA LAMA
        $cacah_krama_tamiu = CacahKramaTamiu::find($id);
        $anggota_krama_tamiu = AnggotaKramaTamiu::where('cacah_krama_tamiu_id', $cacah_krama_tamiu->id)->where('status', '1')->first();
        $krama_tamiu_lama = KramaTamiu::find($anggota_krama_tamiu->krama_tamiu_id);
        $anggota_krama_tamiu_lama = AnggotaKramaTamiu::where('krama_tamiu_id', $krama_tamiu_lama->id)->get();

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //COPY DATA
        $krama_tamiu_baru = new KramaTamiu();
        $krama_tamiu_baru->nomor_krama_tamiu = $krama_tamiu_lama->nomor_krama_tamiu;
        $krama_tamiu_baru->banjar_adat_id = $krama_tamiu_lama->banjar_adat_id;
        $krama_tamiu_baru->cacah_krama_tamiu_id = $krama_tamiu_lama->cacah_krama_tamiu_id;
        $krama_tamiu_baru->status = '1';
        $krama_tamiu_baru->alasan_perubahan = 'Penghapusan Anggota Keluarga';
        $krama_tamiu_baru->tanggal_registrasi = $krama_tamiu_lama->tanggal_registrasi;
        $krama_tamiu_baru->save();

        foreach($anggota_krama_tamiu_lama as $anggota_lama){
            if($anggota_lama->id != $anggota_krama_tamiu->id){
                $anggota_krama_tamiu_baru = new AnggotaKramaTamiu();
                $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
                $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $anggota_lama->cacah_krama_tamiu_id;
                $anggota_krama_tamiu_baru->status_hubungan = $anggota_lama->status_hubungan;
                $anggota_krama_tamiu_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                $anggota_krama_tamiu_baru->status = '1';
                $anggota_krama_tamiu_baru->save();

                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }else{
                //NONAKTIFKAN YANG DIHAPUS
                $anggota_lama->status = '0';
                $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
                $anggota_lama->alasan_keluar = $request->alasan_keluar;
                $anggota_lama->update();
            }
        }

        //NONAKTIFKAN DATA LAMA
        $krama_tamiu_lama->status = '0';
        $krama_tamiu_lama->update();

        //NONAKTIFKAN CACAHNYA
        $cacah_krama_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_krama_tamiu->alasan_keluar = $request->alasan_keluar;
        $cacah_krama_tamiu->status = '0';
        $cacah_krama_tamiu->update();

        return back()->with('success', 'Cacah Krama Tamiu berhasil dikeluarkan');
    }

    public function detail($id){
        $krama = CacahKramaTamiu::find($id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $krama->banjar_adat_id){
            return redirect()->back();
        }

        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);

        //Asal luar bali
        if($krama->asal == 'luar_bali'){
            $desa_asal = DesaDinas::find($krama->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
            return view('pages.banjar.cacah_krama_tamiu.detail', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
        }else if($krama->asal == 'dalam_bali'){
            $banjar_adat_asal = BanjarAdat::find($krama->banjar_adat_asal_id);
            $desa_adat_asal = DesaAdat::find($banjar_adat_asal->desa_adat_id);
            $kecamatan_asal = Kecamatan::find($desa_adat_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            return view('pages.banjar.cacah_krama_tamiu.detail', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat_asal', 'desa_adat_asal', 'kecamatan_asal', 'kabupaten_asal'));
        }
    }

    public function daftar_riwayat($id){
        //GET CACAH
        $cacah_krama_tamiu = CacahKramaTamiu::with('penduduk')->find($id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);
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
        return view('pages.banjar.cacah_krama_tamiu.daftar_riwayat', compact('riwayats', 'penduduk','cacah_krama_tamiu'));
    }

    public function detail_riwayat($id, $id_riwayat){
        //GET CACAH
        $cacah_krama_tamiu = CacahKramaTamiu::with('penduduk')->find($id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $curr_penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);
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
        $krama = $cacah_krama_tamiu;
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = LogPenduduk::with('pekerjaan', 'pendidikan')->find($id_riwayat);
        if($krama->penduduk_id != $penduduk->penduduk_id){
            return redirect()->back();
        }

        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);

        //Asal luar bali
        if($krama->asal == 'luar_bali'){
            $desa_asal = DesaDinas::find($krama->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
            return view('pages.banjar.cacah_krama_tamiu.detail_riwayat', compact('krama', 'penduduk', 'curr_penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
        }else if($krama->asal == 'dalam_bali'){
            $banjar_adat_asal = BanjarAdat::find($krama->banjar_adat_asal_id);
            $desa_adat_asal = DesaAdat::find($banjar_adat_asal->desa_adat_id);
            $kecamatan_asal = Kecamatan::find($desa_adat_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            return view('pages.banjar.cacah_krama_tamiu.detail_riwayat', compact('krama', 'penduduk', 'curr_penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat_asal', 'desa_adat_asal', 'kecamatan_asal', 'kabupaten_asal'));
        }
    }
}
