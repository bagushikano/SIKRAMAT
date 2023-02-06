<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluargaKrama;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\KramaMipil;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelahiran;
use App\Models\KeluargaKrama;
use App\Models\KramaTamiu;
use App\Models\Tamiu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class KelahiranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $kelahirans = Kelahiran::with('cacah_krama_mipil.penduduk', 'krama_mipil.cacah_krama_mipil')->whereIn('banjar_adat_id', $arr_banjar_id)->get();
        return view('pages.desa.kelahiran.kelahiran', compact('kelahirans'));
    }

    public function get_krama_mipil(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')->whereIn('banjar_adat_id', $arr_banjar_adat_id)->get();
        $hasil = view('pages.desa.kelahiran.list_krama_mipil', ['krama_mipil' => $krama_mipil])->render();
        return response()->json(['hasil' => $hasil]);
    }

    public function get_anggota_krama_mipil($id){
        $krama_mipil = KramaMipil::find($id);
        $anggota_keluargas = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->get();
        $hasil = view('pages.desa.kelahiran.list_anggota_keluarga', ['anggota_keluargas' => $anggota_keluargas, 'krama_mipil' => $krama_mipil])->render();
        return response()->json(['hasil' => $hasil]);
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
        return response()->json([
            'krama_mipil' => $krama_mipil,
            'anggota_krama_mipil'=> $anggota_krama_mipil
        ]);
    }

    public function create(){
        $desa_adat_id = session()->get('desa_adat_id');

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.kelahiran.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'banjar_adat', 'banjar_dinas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
            'nomor_akta_kelahiran' => 'required|unique:tb_kelahiran',
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'golongan_darah' => 'required',
            'alamat' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'jenis_kependudukan' => 'required',
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas',
            'krama_mipil' => 'required',
            'ayah_kandung' => 'required',
            'ibu_kandung' => 'required'
        ],[
            'nik.regex' => "NIK hanya boleh mengandung angka",
            'nik.required' => "NIK wajib diisi",
            'nik.unique' => "NIK yang dimasukkan telah terdaftar",
            'nomor_akta_kelahiran.required' => "Nomor Akta Kelahiran wajib diisi",
            'nomor_akta_kelahiran.unique' => "No. Akta Kelahiran yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'golongan_darah.required' => "Golongan Darah wajib dipilih",
            'alamat.required' => "Alamat wajib diisi",
            'provinsi.required' => "Provinsi wajib dipilih",
            'kabupaten.required' => "Kabupaten wajib dipilih",
            'kecamatan.required' => "Kecamatan wajib dipilih",
            'desa.required' => "Desa/Kelurahan wajib dipilih",
            'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'ayah_kandung.required' => "Ayah Kandung wajib dipilih",
            'ibu_kandung.required' => "Ibu Kandung Wajib Dipilih"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $penduduk = new Penduduk();
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $request->desa;
        $penduduk->profesi_id = 1;
        $penduduk->pendidikan_id = 1;
        $penduduk->nama = $request->nama;
        $penduduk->agama = $request->agama;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->alamat = $request->alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        $penduduk->save();

        //NOMOR CACAH KRAMA MIPIL & NOMOR INDUK CACAH KRAMA
        $kramas = CacahKramaMipil::where('banjar_adat_id', $request->banjar_adat_id)->pluck('penduduk_id')->toArray();
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
        $penduduk->nomor_induk_cacah_krama = $nomor_induk_cacah_krama;

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

        //MASUKKAN KE KELUARGA KRAMA MIPIL
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($request->krama_mipil);
        $anggota_krama_mipil = new AnggotaKramaMipil();
        $anggota_krama_mipil->krama_mipil_id = $krama_mipil->id;
        $anggota_krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        if($request->ayah_kandung == $krama_mipil->cacah_krama_mipil->penduduk->id){
            $anggota_krama_mipil->status_hubungan = 'anak';
        }else{
            $anggota_krama_mipil->status_hubungan = 'famili_lain';
        }
        $anggota_krama_mipil->save();

        //SET HISTORY KELAHIRAN
        $kelahiran = new Kelahiran();
        $kelahiran->nomor_akta_kelahiran = $request->nomor_akta_kelahiran;
        $kelahiran->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        $kelahiran->banjar_adat_id = $request->banjar_adat_id;
        $kelahiran->krama_mipil_id = $krama_mipil->id;
        $kelahiran->save();

        return redirect()->route('desa-kelahiran-home')->with('success', 'Kelahiran berhasil ditambahkan');
    }

    public function edit($id){
        $kelahiran = Kelahiran::find($id);
        $krama_mipil = KramaMipil::find($kelahiran->krama_mipil_id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
        $anggota_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->orderBy('status_hubungan', 'ASC')->get();
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();

        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);

        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();

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
        return view('pages.desa.kelahiran.edit', compact('provinsis', 'kabupatens', 'kecamatans', 'desas', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'krama_mipil', 'cacah_krama_mipil', 'banjar_adat', 'banjar_dinas', 'kelahiran', 'penduduk', 'anggota_krama_mipil'));
    }

    public function update($id, Request $request){
        //GET ALL DATA KELAHIRAN
        $kelahiran = Kelahiran::find($id);
        $krama_mipil = KramaMipil::find($kelahiran->krama_mipil_id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
            'nik' => [
                Rule::unique('tb_penduduk')->ignore($penduduk->id),
            ],
            'nomor_akta_kelahiran' => 'required|unique:tb_kelahiran',
            'nomor_akta_kelahiran' => [
                Rule::unique('tb_kelahiran')->ignore($kelahiran->id),
            ],
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'golongan_darah' => 'required',
            'alamat' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'jenis_kependudukan' => 'required',
            'banjar_adat_id' => 'required',
            'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas',
            'krama_mipil' => 'required',
            'ayah_kandung' => 'required',
            'ibu_kandung' => 'required'
        ],[
            'nik.regex' => "NIK hanya boleh mengandung angka",
            'nik.required' => "NIK wajib diisi",
            'nik.unique' => "NIK yang dimasukkan telah terdaftar",
            'nomor_akta_kelahiran.required' => "Nomor Akta Kelahiran wajib diisi",
            'nomor_akta_kelahiran.unique' => "No. Akta Kelahiran yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'golongan_darah.required' => "Golongan Darah wajib dipilih",
            'alamat.required' => "Alamat wajib diisi",
            'provinsi.required' => "Provinsi wajib dipilih",
            'kabupaten.required' => "Kabupaten wajib dipilih",
            'kecamatan.required' => "Kecamatan wajib dipilih",
            'desa.required' => "Desa/Kelurahan wajib dipilih",
            'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'ayah_kandung.required' => "Ayah Kandung wajib dipilih",
            'ibu_kandung.required' => "Ibu Kandung Wajib Dipilih"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //UPDATE DATA PENDUDUK
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $request->desa;
        $penduduk->profesi_id = 1;
        $penduduk->pendidikan_id = 1;
        $penduduk->nama = $request->nama;
        $penduduk->agama = $request->agama;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->alamat = $request->alamat;
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

        //UPDATE DATA CACAH KRAMA
        $cacah_krama_mipil->banjar_adat_id = $request->banjar_adat_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->jenis_kependudukan = $request->jenis_kependudukan;
        if($request->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $request->banjar_dinas_id;
        }else{
            $cacah_krama_mipil->banjar_dinas_id = '';
        }
        $cacah_krama_mipil->update();

        //PERUBAHAN KELUARGA KRAMA
        if($krama_mipil->id != $request->krama_mipil){
            //GET KRAMA MIPIL LAMA
            $old_anggota_krama_mipil = AnggotaKeluargaKrama::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->get();
            $old_anggota_krama_mipil->delete();

            //MASUKKAN KE KELUARGA KRAMA MIPIL
            $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($request->krama_mipil);
            $anggota_krama_mipil = new AnggotaKramaMipil();
            $anggota_krama_mipil->krama_mipil_id = $krama_mipil->id;
            $anggota_krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            if($request->ayah_kandung == $krama_mipil->cacah_krama_mipil->penduduk->id){
                $anggota_krama_mipil->status_hubungan = 'anak';
            }else{
                $anggota_krama_mipil->status_hubungan = 'famili_lain';
            }
            $anggota_krama_mipil->save();
        }

        //SET HISTORY KELAHIRAN
        $kelahiran = new Kelahiran();
        $kelahiran->nomor_akta_kelahiran = $request->nomor_akta_kelahiran;
        $kelahiran->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        $kelahiran->banjar_adat_id = $request->banjar_adat_id;
        $kelahiran->krama_mipil_id = $krama_mipil->id;
        $kelahiran->update();

        return redirect()->route('desa-kelahiran-home')->with('success', 'Kelahiran berhasil diperbaharui');
    }

    public function detail($id){
        $kelahiran = Kelahiran::find($id);
        $krama_mipil = KramaMipil::find($kelahiran->krama_mipil_id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
        $anggota_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->orderBy('status_hubungan', 'ASC')->get();
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();

        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);

        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();

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
        return view('pages.desa.kelahiran.detail', compact('provinsis', 'kabupatens', 'kecamatans', 'desas', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'krama_mipil', 'cacah_krama_mipil', 'banjar_adat', 'banjar_dinas', 'kelahiran', 'penduduk', 'anggota_krama_mipil'));
    }
}