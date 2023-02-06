<?php

namespace App\Http\Controllers\BanjarAdatController;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKramaTamiu;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaTamiu;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KramaTamiu;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\Tempekan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AnggotaKramaTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($id){
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk')->find($id);
        $cacah_krama_tamiu = CacahKramaTamiu::find($krama_tamiu->cacah_krama_tamiu_id);
        $penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $krama_tamiu->banjar_adat_id){
            return redirect()->back();
        }

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
        $anggota_krama_tamiu = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->where('krama_tamiu_id', $krama_tamiu->id)->get();
        return view('pages.banjar.krama_tamiu.create_anggota_keluarga', compact('krama_tamiu', 'pekerjaans', 'pendidikans', 'provinsis', 'tempekan', 'banjar_dinas', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'kabupatens', 'kecamatans', 'desas', 'anggota_krama_tamiu'));
    }

    public function store($krama_tamiu_id, Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu')->find($krama_tamiu_id);
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
        $krama_tamiu_lama = KramaTamiu::find($krama_tamiu_id);
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
        return redirect()->route('banjar-krama-tamiu-detail', $krama_tamiu_baru->id)->with('success', 'Anggota Keluarga Krama Tamiu berhasil ditambahkan');
    }

    public function edit($id){
        //GET MASTER ANGGOTA
        $anggota_krama = AnggotaKramaTamiu::find($id);
        $cacah_krama_tamiu = CacahKramaTamiu::find($anggota_krama->cacah_krama_tamiu_id);
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
        return view('pages.banjar.krama_tamiu.edit_anggota_keluarga', compact(
            'penduduk', 'cacah_krama_tamiu', 'anggota_krama',
            'krama_tamiu', 'pekerjaans', 'pendidikans', 
            'anggota_krama_tamiu', 'desas', 'banjar_dinas'));
    }

    public function update($id, Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');

        //GET MASTER CACAH
        $anggota_krama_tamiu = AnggotaKramaTamiu::find($id);
        $cacah_krama_tamiu = CacahKramaTamiu::find($anggota_krama_tamiu->cacah_krama_tamiu_id);
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
        $cacah_krama_tamiu->update();

        //UPDATE ANGGOTA
        $anggota_krama_tamiu = AnggotaKramaTamiu::find($id);
        $anggota_krama_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_krama_tamiu->status_hubungan = $request->status_hubungan;
        $anggota_krama_tamiu->update();

        return redirect()->route('banjar-krama-tamiu-detail', $krama_tamiu->id)->with('success', 'Cacah Krama Tamiu berhasil diperbaharui');
    }

    public function destroy($id, Request $request){
        //GET DATA LAMA
        $anggota_krama_tamiu = AnggotaKramaTamiu::find($id);
        $krama_tamiu_lama = KramaTamiu::find($anggota_krama_tamiu->krama_tamiu_id);
        $anggota_krama_tamiu_lama = AnggotaKramaTamiu::where('krama_tamiu_id', $krama_tamiu_lama->id)->get();

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
        $cacah_krama_tamiu = CacahKramaTamiu::find($anggota_krama_tamiu->cacah_krama_tamiu_id);
        $cacah_krama_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_krama_tamiu->alasan_keluar = $request->alasan_keluar;
        $cacah_krama_tamiu->status = '0';
        $cacah_krama_tamiu->update();

        return redirect()->route('banjar-krama-tamiu-detail', $krama_tamiu_baru->id)->with('success', 'Cacah Krama Tamiu berhasil dikeluarkan');
    }
}