<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluargaKrama;
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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CacahKramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $kramas = CacahKramaMipil::with('penduduk')->whereIn('banjar_adat_id', $arr_banjar_id)->get();
        return view('pages.desa.cacah_krama_mipil.cacah_krama_mipil', compact('kramas'));
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

    public function create(){
        $desa_adat_id = session()->get('desa_adat_id');

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_krama_mipil.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'banjar_adat', 'banjar_dinas'));
    }

    public function store(Request $request)
    {
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $kramas = CacahKramaMipil::where('banjar_adat_id', $request->banjar_adat_id)->pluck('penduduk_id')->toArray();

        //TAMBAH PENDUDUK BARU
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
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'desa' => 'required',
                'jenis_kependudukan' => 'required',
                'banjar_adat_id' => 'required',
                'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas'
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
                'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
                'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
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
            $penduduk->ayah_kandung_id = $request->ayah_kandung;
            $penduduk->ibu_kandung_id = $request->ibu_kandung;
            $penduduk->save();
        }

        $validator = Validator::make($request->all(), [
            'jenis_kependudukan' => 'required',
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas'
        ],[
            'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //NOMOR CACAH KRAMA MIPIL & NOMOR INDUK CACAH KRAMA
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($request->banjar_adat_id);
        $nomor_cacah_krama_mipil = $banjar_adat->kode_banjar_adat;
        $nomor_induk_cacah_krama = $nomor_cacah_krama_mipil.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'01'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'00'.$jumlah_penduduk_tanggal_sama;
            $nomor_induk_cacah_krama = $nomor_induk_cacah_krama.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'0'.$jumlah_penduduk_tanggal_sama;
            $nomor_induk_cacah_krama = $nomor_induk_cacah_krama.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.$jumlah_penduduk_tanggal_sama;
            $nomor_induk_cacah_krama = $nomor_induk_cacah_krama.$jumlah_penduduk_tanggal_sama;
        }

        //SET NOMOR INDUK KRAMA KE PENDUDUK
        if($penduduk->nomor_induk_cacah_krama == ''){
            $penduduk->nomor_induk_cacah_krama = $nomor_induk_cacah_krama;
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
        $penduduk->nama_alias = $request->nama_alias;
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
        $penduduk->update();
        
        //INSERT CACAH KRAMA MIPIL
        $cacah_krama_mipil = new CacahKramaMipil();
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $request->banjar_adat_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->jenis_kependudukan = $request->jenis_kependudukan;
        if($request->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $request->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();
        return redirect()->route('desa-cacah-krama-mipil-home')->with('success', 'Cacah Krama Mipil berhasil ditambahkan');
    }

    public function edit($id){
        $desa_adat_id = session()->get('desa_adat_id');
        $krama = CacahKramaMipil::find($id);
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
        return view('pages.desa.cacah_krama_mipil.edit', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'pekerjaans', 'pendidikans', 'provinsis', 'kabupatens', 'kecamatans', 'desas', 'banjar_adat', 'banjar_dinas'));
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
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
            'jenis_kependudukan' => 'required',
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas'
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
            'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $krama = CacahKramaMipil::find($id);
        $penduduk = Penduduk::find($krama->penduduk_id);

        $penduduk->desa_id = $request->desa;
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
        $krama->jenis_kependudukan = $request->jenis_kependudukan;
        if($request->jenis_kependudukan == 'adat_&_dinas'){
            $krama->banjar_dinas_id = $request->banjar_dinas_id;
        }
        $krama->update();
        return redirect()->route('desa-cacah-krama-mipil-home')->with('success', 'Cacah Krama Mipil berhasil diperbaharui');
    }

    public function destroy($id)
    {
        $krama = CacahKramaMipil::find($id);
        $krama->delete();
        return back()->with('success', 'Cacah Krama Mipil berhasil dihapus');
    }

    public function detail($id){
        $desa_adat_id = session()->get('desa_adat_id');
        $krama = CacahKramaMipil::find($id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.cacah_krama_mipil.detail', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas'));
    }
}
