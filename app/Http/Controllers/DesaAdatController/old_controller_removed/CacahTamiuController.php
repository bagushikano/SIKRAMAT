<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
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
use App\Models\WNA;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CacahTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_id = BanjarDinas::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $tamiu_wni = CacahTamiu::with('penduduk', 'banjar_dinas')->where('wna_id', NULL)->whereIn('banjar_dinas_id', $arr_banjar_id)->get();
        $tamiu_wna = CacahTamiu::with('wna', 'banjar_dinas')->where('penduduk_id', NULL)->whereIn('banjar_dinas_id', $arr_banjar_id)->get();
        return view('pages.desa.cacah_tamiu.cacah_tamiu', compact('tamiu_wni', 'tamiu_wna'));
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
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        if($penduduk){
            $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));
            $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->first();
            $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->whereIn('banjar_adat_id', $banjar_adat_id)->first();
            $cacah_tamiu = CacahTamiu::where('penduduk_id', $penduduk->id)->whereIn('banjar_adat_id', $banjar_adat_id)->first();
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

    public function get_wna($nomor_paspor){
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $wna = WNA::where('nomor_paspor', $nomor_paspor)->first();
        if($wna){
            $wna->tanggal_lahir = date("d-m-Y", strtotime($wna->tanggal_lahir));
            $cacah_krama = CacahTamiu::where('wna_id', $wna->id)->whereIn('banjar_adat_id', $banjar_adat_id)->first();
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
        $desa_adat_id = session()->get('desa_adat_id');

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_tamiu.create_tamiu_wni', compact('pekerjaans', 'pendidikans', 'provinsis', 'banjar_adat', 'banjar_dinas'));
    }

    public function create_tamiu_wna(){
        $desa_adat_id = session()->get('desa_adat_id');

        $negaras = Negara::get();
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_tamiu.create_tamiu_wna', compact('negaras', 'banjar_adat', 'banjar_dinas'));
    }

    public function store_tamiu_wni(Request $request)
    {
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $kramas = CacahTamiu::where('banjar_adat_id', $request->banjar_adat_id)->pluck('penduduk_id')->toArray();

        //TAMBAH PENDUDUK BARU
        if($penduduk == NULL){
            $validator = Validator::make($request->all(), [
                'nik' => 'required|unique:tb_penduduk|max:16|regex:/^[0-9]*$/',
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'tanggal_masuk' => 'required',
                'jenis_kelamin' => 'required',
                'pekerjaan' => 'required',
                'pendidikan' => 'required',
                'golongan_darah' => 'required',
                'status_perkawinan' => 'required',
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'desa' => 'required',
                'banjar_adat_id' => 'required',
                'banjar_dinas_id' => 'required'
            ],[
                'nik.regex' => "NIK hanya boleh mengandung angka",
                'nik.regex' => "NIK yang dimasukkan telah terdaftar",
                'nik.max' => "NIK harus terdiri dari 16 angka!",
                'nama.required' => "Nama wajib diisi",
                'nama.regex' => "Nama hanya boleh mengandung huruf",
                'tempat_lahir.required' => "Tempat Lahir wajib diisi",
                'tempat_masuk.required' => "Tanggal Masuk wajib diisi",
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
                'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
                'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
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
            $penduduk->nama_alias = $request->nama_alias;
            $penduduk->gelar_belakang = $request->gelar_belakang;
            $penduduk->agama = $request->agama;
            $penduduk->tempat_lahir = $request->tempat_lahir;
            $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $penduduk->jenis_kelamin = $request->jenis_kelamin;
            $penduduk->golongan_darah = $request->golongan_darah;
            $penduduk->status_perkawinan = $request->status_perkawinan;
            $penduduk->telepon = $request->telepon;
            $penduduk->alamat = $request->alamat;
            $penduduk->ayah_kandung_id = $request->ayah_kandung;
            $penduduk->ibu_kandung_id = $request->ibu_kandung;
            $penduduk->save();
        }

        $validator = Validator::make($request->all(), [
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required'
        ],[
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            dd($validator);
            return back()->withInput()->withErrors($validator);
        }

        //NOMOR CACAH TAMIU
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($request->banjar_adat_id);
        $nomor_cacah_tamiu = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_tamiu = $nomor_cacah_tamiu.'03'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.$jumlah_penduduk_tanggal_sama;
        }

        //SET FOTO PENDUDUK
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
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $request->desa;
        $penduduk->profesi_id = $request->pekerjaan;
        $penduduk->pendidikan_id = $request->pendidikan;
        $penduduk->gelar_depan = $request->gelar_depan;
        $penduduk->nama = $request->nama;
        $penduduk->gelar_belakang = $request->gelar_belakang;
        $penduduk->agama = $request->agama;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->telepon = $request->telepon;
        $penduduk->alamat = $request->alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        $penduduk->update();
        
        //INSERT CACAH TAMIU
        $cacah_tamiu = new CacahTamiu();
        $cacah_tamiu->nomor_cacah_tamiu = $nomor_cacah_tamiu;
        $cacah_tamiu->banjar_adat_id = $request->banjar_adat_id;
        $cacah_tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $cacah_tamiu->penduduk_id = $penduduk->id;
        $cacah_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        if($request->tanggal_keluar != ''){
            $cacah_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        }
        $cacah_tamiu->save();
        return redirect()->route('desa-cacah-tamiu-home')->with('success', 'Cacah Tamiu berhasil ditambahkan');
    }

    public function store_tamiu_wna(Request $request)
    {
        $wna = WNA::where('nomor_paspor', $request->nomor_paspor)->first();
        $kramas = CacahTamiu::where('banjar_adat_id', $request->banjar_adat_id)->pluck('wna_id')->toArray();

        //TAMBAH PENDUDUK BARU
        if($wna == NULL){
            $validator = Validator::make($request->all(), [
                'nomor_paspor' => 'required',
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'tanggal_masuk' => 'required',
                'jenis_kelamin' => 'required',
                'alamat' => 'required',
                'negara' => 'required',
                'banjar_adat_id' => 'required',
                'banjar_dinas_id' => 'required'
            ],[
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
                'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
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
            $wna->negara_id = $request->negara;
            $wna->save();
        }

        $validator = Validator::make($request->all(), [
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required'
        ],[
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            dd($validator);
            return back()->withInput()->withErrors($validator);
        }

        //NOMOR KRAMA TAMIU & NOMOR INDUK KRAMA
        $jumlah_penduduk_tanggal_sama = WNA::where('tanggal_lahir', $wna->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($request->banjar_adat_id);
        $nomor_cacah_tamiu = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_tamiu = $nomor_cacah_tamiu.'03'.Carbon::parse($wna->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_tamiu = $nomor_cacah_tamiu.$jumlah_penduduk_tanggal_sama;
        }

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
        
        //INSERT KRAMA tamiu
        $cacah_tamiu = new CacahTamiu();
        $cacah_tamiu->nomor_cacah_tamiu = $nomor_cacah_tamiu;
        $cacah_tamiu->banjar_adat_id = $request->banjar_adat_id;
        $cacah_tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $cacah_tamiu->wna_id = $wna->id;
        $cacah_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        if($request->tanggal_keluar != ''){
            $cacah_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        }
        $cacah_tamiu->save();
        return redirect()->route('desa-cacah-tamiu-home')->with(['success' => 'Cacah Tamiu berhasil ditambahkan', 'tamiu' => 'wna']);
    }

    public function edit_tamiu_wni($id){
        $desa_adat_id = session()->get('desa_adat_id');

        $krama = CacahTamiu::find($id);
        $penduduk = Penduduk::find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_tamiu.edit_tamiu_wni', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'pekerjaans', 'pendidikans', 'provinsis', 'kabupatens', 'kecamatans', 'desas', 'banjar_adat', 'banjar_dinas'));
    }

    public function edit_tamiu_wna($id){
        $desa_adat_id = session()->get('desa_adat_id');

        $krama = CacahTamiu::find($id);
        $wna = WNA::find($krama->wna_id);
        $negara = Negara::find($wna->negara_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_tamiu.edit_tamiu_wna', compact('krama', 'wna', 'negara', 'banjar_adat', 'banjar_dinas'));
    }

    public function update_tamiu_wni($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'pekerjaan' => 'required',
            'pendidikan' => 'required',
            'golongan_darah' => 'required',
            'alamat' => 'required',
            'status_perkawinan' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required'
        ],[
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
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $krama = CacahTamiu::find($id);
        $penduduk = Penduduk::find($krama->penduduk_id);

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
        $penduduk->telepon = $request->telepon;
        $penduduk->status_perkawinan = $request->status_perkawinan;
        $penduduk->alamat = $request->alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        //SET FOTO PENDUDUK
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

        $krama->banjar_adat_id = $request->banjar_adat_id;
        $krama->banjar_dinas_id = $request->banjar_dinas_id;
        $krama->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        if($request->tanggal_keluar != ''){
            $krama->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        }
        $krama->update();
        return redirect()->route('desa-cacah-tamiu-home')->with('success', 'Cacah Tamiu berhasil diperbaharui');
    }

    public function update_tamiu_wna($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_paspor' => 'required',
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'tanggal_masuk' => 'required',
            'jenis_kelamin' => 'required',
            'alamat' => 'required',
            'negara' => 'required',
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required'
        ],[
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
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }
        
        $tamiu = CacahTamiu::find($id);
        $wna = WNA::find($tamiu->wna_id);

        $wna->nama = $request->nama;
        $wna->tempat_lahir = $request->tempat_lahir;
        $wna->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $wna->jenis_kelamin = $request->jenis_kelamin;
        $wna->alamat = $request->alamat;
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

        $tamiu->banjar_adat_id = $request->banjar_adat_id;
        $tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $tamiu->wna_id = $wna->id;
        $tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        if($request->tanggal_keluar != ''){
            $tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        }
        $tamiu->update();
        return redirect()->route('desa-cacah-tamiu-home')->with(['success' => 'Cacah Tamiu berhasil diperbaharui', 'tamiu' => 'wna']);
    }

    public function delete_tamiu_wni($id)
    {
        $tamiu = CacahTamiu::find($id);
        $tamiu->delete();
        return back()->with('success', 'Cacah Tamiu berhasil dihapus');
    }

    public function delete_tamiu_wna($id)
    {
        $tamiu = CacahTamiu::find($id);
        $tamiu->delete();
        return back()->with(['success' => 'Cacah Tamiu berhasil dihapus', 'tamiu' => 'wna']);
    }

    public function detail_wni($id){
        $desa_adat_id = session()->get('desa_adat_id');
        $krama = CacahTamiu::find($id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_tamiu.detail_cacah_tamiu_wni', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas'));
    }

    public function detail_wna($id){
        $desa_adat_id = session()->get('desa_adat_id');

        $krama = CacahTamiu::find($id);
        $wna = WNA::find($krama->wna_id);
        $negara = Negara::find($wna->negara_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_tamiu.detail_cacah_tamiu_wna', compact('krama', 'wna', 'negara', 'banjar_adat', 'banjar_dinas'));
    }
}
