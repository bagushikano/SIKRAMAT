<?php

namespace App\Http\Controllers\BanjarAdatController;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKramaMipil;
use App\Models\AnggotaTamiu;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\CacahTamiu;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KramaMipil;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Tamiu;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\Tempekan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AnggotaTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($id){
        $tamiu = Tamiu::with('cacah_tamiu.penduduk')->find($id);
        $cacah_tamiu = CacahTamiu::find($tamiu->cacah_tamiu_id);
        $penduduk = Penduduk::find($cacah_tamiu->penduduk_id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $cacah_tamiu->banjar_adat_id){
            return redirect()->back();
        }

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

        //GET MASTER ALAMAT TAMIU
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
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();

        //GET ANGGOTA
        $anggota_tamiu = AnggotaTamiu::with('cacah_tamiu.penduduk')->where('tamiu_id', $tamiu->id)->get();
        return view('pages.banjar.tamiu.create_anggota_keluarga', compact('tamiu', 'pekerjaans', 'pendidikans', 'provinsis', 'banjar_dinas', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'kabupatens', 'kecamatans', 'desas', 'anggota_tamiu'));
    }

    public function store($tamiu_id, Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $tamiu = Tamiu::with('cacah_tamiu')->find($tamiu_id);
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
        $tamiu_lama = Tamiu::find($tamiu_id);
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
        
        return redirect()->route('banjar-tamiu-detail', $tamiu_baru->id)->with('success', 'Anggota Keluarga Tamiu berhasil ditambahkan');
    }

    public function edit($id){
        //GET MASTER ANGGOTA
        $anggota_krama = AnggotaTamiu::find($id);
        $cacah_tamiu = CacahTamiu::find($anggota_krama->cacah_tamiu_id);
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
        return view('pages.banjar.tamiu.edit_anggota_keluarga', compact(
            'penduduk', 'cacah_tamiu', 'anggota_krama',
            'tamiu', 'pekerjaans', 'pendidikans', 
            'anggota_tamiu', 'desas', 'banjar_dinas'));
    }

    public function update($id, Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');

        //GET MASTER CACAH
        $anggota_tamiu = AnggotaTamiu::find($id);
        $cacah_tamiu = CacahTamiu::find($anggota_tamiu->cacah_tamiu_id);
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
        $anggota_tamiu = AnggotaTamiu::find($id);
        $anggota_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $anggota_tamiu->status_hubungan = $request->status_hubungan;
        $anggota_tamiu->update();

        return redirect()->route('banjar-tamiu-detail', $tamiu->id)->with('success', 'Cacah Tamiu berhasil diperbaharui');
    }

    public function destroy($id, Request $request){
        //GET DATA LAMA
        $anggota_tamiu = AnggotaTamiu::find($id);
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
        $cacah_tamiu = CacahTamiu::find($anggota_tamiu->cacah_tamiu_id);
        $cacah_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_tamiu->alasan_keluar = $request->alasan_keluar;
        $cacah_tamiu->status = '0';
        $cacah_tamiu->update();
        return redirect()->route('banjar-tamiu-detail', $tamiu_baru->id)->with('success', 'Anggota Keluarga Tamiu berhasil dihapus');
    }
}