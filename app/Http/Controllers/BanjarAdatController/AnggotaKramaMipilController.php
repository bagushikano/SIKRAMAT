<?php

namespace App\Http\Controllers\BanjarAdatController;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KramaMipil;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\Tempekan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AnggotaKramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($id){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

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

        //GET MASTER ALAMAT KRAMA MIPIL
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);

        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();

        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();

        //GET ANGGOTA
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->get();
        return view('pages.banjar.krama_mipil.create_anggota_keluarga', compact('krama_mipil', 'pekerjaans', 'pendidikans', 'provinsis', 'tempekan', 'banjar_dinas', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'kabupatens', 'kecamatans', 'desas', 'anggota_krama_mipil'));
    }

    public function store($krama_mipil_id, Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil')->find($krama_mipil_id);
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
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_krama_mipil->status = '1';
        $cacah_krama_mipil->jenis_kependudukan = $krama_mipil->cacah_krama_mipil->jenis_kependudukan;
        if($krama_mipil->cacah_krama_mipil->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $krama_mipil->cacah_krama_mipil->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();

        //GET DATA LAMA
        $krama_mipil_lama = KramaMipil::find($krama_mipil_id);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

        //COPY DATA
        $krama_mipil_baru = new KramaMipil();
        $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
        $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
        $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
        $krama_mipil_baru->status = '1';
        $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
        $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
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
        return redirect()->route('banjar-krama-mipil-detail', $krama_mipil_baru->id)->with('success', 'Cacah Krama Mipil berhasil ditambahkan');
    }

    public function edit($id){
        //GET MASTER ANGGOTA
        $anggota_krama = AnggotaKramaMipil::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($anggota_krama->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

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
        return view('pages.banjar.krama_mipil.edit_anggota_keluarga', compact(
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
        $anggota_krama_mipil = AnggotaKramaMipil::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($anggota_krama_mipil->cacah_krama_mipil_id);
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
        $anggota_krama_mipil = AnggotaKramaMipil::find($id);
        $anggota_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_krama_mipil->status_hubungan = $request->status_hubungan;
        $anggota_krama_mipil->update();

        return redirect()->route('banjar-krama-mipil-detail', $krama_mipil->id)->with('success', 'Cacah Krama Mipil berhasil diperbaharui');
    }

    public function destroy($id, Request $request){
        //GET DATA LAMA
        $anggota_krama_mipil = AnggotaKramaMipil::find($id);
        $krama_mipil_lama = KramaMipil::find($anggota_krama_mipil->krama_mipil_id);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

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
                $anggota_lama->alasan_keluar = $request->alasan_keluar;
                $anggota_lama->update();
            }
        }

        //NONAKTIFKAN DATA LAMA
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();

        //NONAKTIFKAN CACAHNYA
        $cacah_krama_mipil = CacahKramaMipil::find($anggota_krama_mipil->cacah_krama_mipil_id);
        $cacah_krama_mipil->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_krama_mipil->alasan_keluar = $request->alasan_keluar;
        $cacah_krama_mipil->status = '0';
        $cacah_krama_mipil->update();

        return redirect()->route('banjar-krama-mipil-detail', $krama_mipil_baru->id)->with('success', 'Cacah Krama Mipil berhasil dikeluarkan');
    }
}