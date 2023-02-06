<?php

namespace App\Http\Controllers\KramaController\Kelahiran;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AnggotaKeluargaKrama;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
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
use App\Models\Tempekan;
use App\Models\KelahiranAjuan;
use App\Models\Notifikasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class KelahiranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $penduduk_id = Auth::user()->user->penduduk_id;
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        $banjar_adat_id = $cacahKramaMipil->banjar_adat_id;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();

        if ($cacahKramaMipil) {
            /**
             * Cari krama mipil (kepala keluarga) dari id cacah krama klo found return krama mipilnya (keluarga)
             * klo not found, cari di anggota krama mipil, klo masih notfound abort, klo found return krama mipilnya (keluarga)
             */
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
            if (!$kramaMipil) {
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->first();
                }
            }
        }
        /**
         * Get id krama mipil dengan nomer krama mipilnya
         */
        $kramaMipilId = KramaMipil::where('nomor_krama_mipil', $kramaMipil->nomor_krama_mipil)->pluck('id')->toArray();
        $kelahiran_ajuan = KelahiranAjuan::whereIn('krama_mipil_id', $kramaMipilId)
                                    ->with('cacah_krama_mipil.penduduk', 'kelahiran')
                                    ->orderByRaw('FIELD(status, "1", "0", "3", "2")')
                                    ->orderBy('created_at', 'desc')->get();

        $kelahiran = Kelahiran::whereIn('krama_mipil_id', $kramaMipilId)
                                ->where('status', '!=' ,0)->with('cacah_krama_mipil.penduduk', 'kelahiran_ajuan')
                                ->orderBy('tanggal_lahir', 'desc')->get();

        return view('pages.krama.kelahiran.kelahiran', compact('kelahiran_ajuan', 'kelahiran'));
    }

    public function detail_kelahiran($id){
        $kelahiran = Kelahiran::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::with('ayah', 'ibu')->find($cacah_krama_mipil->penduduk_id);

        //SET NAMA AYAH
        $nama = '';
        if($penduduk->ayah->gelar_depan != ''){
            $nama = $nama.$penduduk->ayah->gelar_depan;
        }
        $nama = $nama.' '.$penduduk->ayah->nama;
        if($penduduk->ayah->gelar_belakang != ''){
            $nama = $nama.', '.$penduduk->ayah->gelar_belakang;
        }
        $penduduk->ayah->nama = $nama;

        //SET NAMA IBU
        $nama = '';
        if($penduduk->ibu->gelar_depan != ''){
            $nama = $nama.$penduduk->ibu->gelar_depan;
        }
        $nama = $nama.' '.$penduduk->ibu->nama;
        if($penduduk->ibu->gelar_belakang != ''){
            $nama = $nama.', '.$penduduk->ibu->gelar_belakang;
        }
        $penduduk->ibu->nama = $nama;
        return view('pages.krama.kelahiran.detail_kelahiran', compact('kelahiran', 'cacah_krama_mipil', 'penduduk'));
    }

    public function detail_ajuan($id){
        $kelahiran = KelahiranAjuan::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::withTrashed()->with('ayah', 'ibu')->find($cacah_krama_mipil->penduduk_id);

        //SET NAMA AYAH
        $nama = '';
        if($penduduk->ayah->gelar_depan != ''){
            $nama = $nama.$penduduk->ayah->gelar_depan;
        }
        $nama = $nama.' '.$penduduk->ayah->nama;
        if($penduduk->ayah->gelar_belakang != ''){
            $nama = $nama.', '.$penduduk->ayah->gelar_belakang;
        }
        $penduduk->ayah->nama = $nama;

        //SET NAMA IBU
        $nama = '';
        if($penduduk->ibu->gelar_depan != ''){
            $nama = $nama.$penduduk->ibu->gelar_depan;
        }
        $nama = $nama.' '.$penduduk->ibu->nama;
        if($penduduk->ibu->gelar_belakang != ''){
            $nama = $nama.', '.$penduduk->ibu->gelar_belakang;
        }
        $penduduk->ibu->nama = $nama;

        $jangka_waktu = $kelahiran->created_at->diff(Carbon::now())->format('%a');
        return view('pages.krama.kelahiran.detail_ajuan', compact('kelahiran', 'cacah_krama_mipil', 'penduduk', 'jangka_waktu'));
    }

    public function create_ajuan(){
        $penduduk_id = Auth::user()->user->penduduk_id;
        /**
         * Cari cacah krama mipil dulu, klo not found, abort
         */
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        if ($cacahKramaMipil) {
            /**
             * Cari krama mipil (kepala keluarga) dari id cacah krama klo found return krama mipilnya (keluarga)
             * klo not found, cari di anggota krama mipil, klo masih notfound abort, klo found return krama mipilnya (keluarga)
             */
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)
                                        ->with('cacah_krama_mipil.penduduk', 'anggota.cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat.desa_adat')
                                        ->where('status', 1)->first();
            if ($kramaMipil) {
                $krama_mipil = $kramaMipil;
                $krama_mipil->anggota = Helper::generate_nama_anggota_keluarga_krama_mipil($krama_mipil->anggota);
                $krama_mipil = Helper::generate_nama_krama_mipil($krama_mipil);
            }
            else {
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', '1')->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)
                                        ->with('cacah_krama_mipil.penduduk', 'anggota.cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat.desa_adat')
                                        ->where('status', 1)->first();
                    $krama_mipil = $kramaMipil;
                    $krama_mipil->anggota = Helper::generate_nama_anggota_keluarga_krama_mipil($krama_mipil->anggota);
                    $krama_mipil = Helper::generate_nama_krama_mipil($krama_mipil);
                }
                else {
                    return back();
                }
            }
        }
        else {
            return back();
        }
        
        return view('pages.krama.kelahiran.create_ajuan', compact('krama_mipil'));
    }

    public function create_ajuan_ulang($id){
        $kelahiran = KelahiranAjuan::find($id);
        $penduduk_id = Auth::user()->user->penduduk_id;
        /**
         * Cari cacah krama mipil dulu, klo not found, abort
         */
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        if ($cacahKramaMipil) {
            /**
             * Cari krama mipil (kepala keluarga) dari id cacah krama klo found return krama mipilnya (keluarga)
             * klo not found, cari di anggota krama mipil, klo masih notfound abort, klo found return krama mipilnya (keluarga)
             */
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)
                                        ->with('cacah_krama_mipil.penduduk', 'anggota.cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat.desa_adat')
                                        ->where('status', 1)->first();
            if ($kramaMipil) {
                $krama_mipil = $kramaMipil;
                $krama_mipil->anggota = Helper::generate_nama_anggota_keluarga_krama_mipil($krama_mipil->anggota);
                $krama_mipil = Helper::generate_nama_krama_mipil($krama_mipil);
            }
            else {
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', '1')->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)
                                        ->with('cacah_krama_mipil.penduduk', 'anggota.cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat.desa_adat')
                                        ->where('status', 1)->first();
                    $krama_mipil = $kramaMipil;
                    $krama_mipil->anggota = Helper::generate_nama_anggota_keluarga_krama_mipil($krama_mipil->anggota);
                    $krama_mipil = Helper::generate_nama_krama_mipil($krama_mipil);
                }
                else {
                    return back();
                }
            }
        }
        else {
            return back();
        }
        
        return view('pages.krama.kelahiran.create_ajuan_ulang', compact('krama_mipil', 'kelahiran'));
    }

    public function store_ajuan(Request $request)
    {
        /**
         * Fungsi untuk nge create ajuan kelahiran baru dari user
         * form data:
         * nik
         * nama
         * tempat_lahir
         * tanggal_lahir (2000-12-30)
         * jenis_kelamin (laki-laki, perempuan)
         * golongan_darah (A, B, AB, O, -)
         * alamat
         * ayah_kandung -> int id, di ambil dari tb_penduduk
         * ibu_kandung -> int id, di ambil dari tb_penduduk
         * foto (file)
         * nomor_akta_kelahiran
         * file_akta_kelahiran (file)
         *
         * Return: data kelahiran
         */

        $validator = Validator::make($request->all(), [
            'nik' => 'unique:tb_penduduk,deleted_at,NULL|regex:/^[0-9]*$/|nullable',
            'nomor_akta_kelahiran' => 'unique:tb_kelahiran|nullable|max:21',
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'golongan_darah' => 'required',
            'ayah_kandung' => 'required',
            'ibu_kandung' => 'required'
        ],[
            'nik.regex' => "NIK hanya boleh mengandung angka",
            'nik.unique' => "NIK yang dimasukkan telah terdaftar",
            'nomor_akta_kelahiran.unique' => "No. Akta Kelahiran yang dimasukkan telah terdaftar",
            'nomor_akta_kelahiran.max' => "No. Akta Kelahiran maksimal 21 karakter",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'golongan_darah.required' => "Golongan Darah wajib dipilih",
            'ayah_kandung.required' => "Ayah wajib dipilih",
            'ibu_kandung.required' => "Ibu Wajib Dipilih"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal Lahir
        $tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_lahir > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_lahir' => 'Tanggal lahir tidak boleh melebihi tanggal sekarang']);
        }

        /**
         * Prepare untuk data Krama mipil, desa adat, dll
         */

        $penduduk_id = Auth::user()->user->penduduk_id;
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        $banjar_adat_id = $cacahKramaMipil->banjar_adat_id;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();

        if ($cacahKramaMipil) {
            /**
             * Cari krama mipil (kepala keluarga) dari id cacah krama klo found return krama mipilnya (keluarga)
             * klo not found, cari di anggota krama mipil, klo masih notfound abort, klo found return krama mipilnya (keluarga)
             */
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
            if (!$kramaMipil) {
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', '1')->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->where('status', '1')->first();
                }
            }
        }

        $pendudukAyah = Penduduk::where('id', $request->ayah_kandung)->first();
        $cacahMipilAyah = CacahKramaMipil::where('id', $kramaMipil->cacah_krama_mipil_id)->where('status', '1')->first();
        $desa_adat = DesaAdat::find($banjarAdat->desa_adat_id);
        // $cacahMipilIbu = AnggotaKramaMipil::where('krama_mipil_id', $kramaMipil->id)->where('status_hubungan', "ibu")->whereNotNull('tanggal_nonaktif')->first();
        // if ($cacahMipilIbu) {
        //     $pendudukIbu = Penduduk::where('id', $cacahMipilIbu)
        // }
        // else {
        //     $pendudukIbu = null;
        // }


        /**
         * Create penduduk
         */
        $penduduk = new Penduduk();
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $pendudukAyah->desa_id;
        $penduduk->profesi_id = 1;
        $penduduk->pendidikan_id = 1;
        $penduduk->nama = $request->nama;
        $penduduk->agama = $pendudukAyah->agama;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->alamat = $pendudukAyah->alamat;
        $penduduk->koordinat_alamat = $pendudukAyah->koordinat_alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        $penduduk->save();

        /**
         * generate NICK
         */
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $nomor_cacah_krama_mipil = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'01'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.$jumlah_penduduk_tanggal_sama;
        }

        /**
         * Set nick ke penduduk
         */

        $penduduk->update();


        /**
         * Create cacah mipil baru
         */
        $cacah_krama_mipil = new CacahKramaMipil();
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_mipil->tempekan_id = $cacahMipilAyah->tempekan_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->jenis_kependudukan = $cacahMipilAyah->jenis_kependudukan;
        $cacah_krama_mipil->status = '0';
        if($cacahMipilAyah->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $cacahMipilAyah->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();

        /**
         * Create ajuan baru di tb_kelahiran_ajuan
         */

        $kelahiran = new KelahiranAjuan();
        $kelahiran->nomor_akta_kelahiran = $request->nomor_akta_kelahiran;
        $kelahiran->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        $kelahiran->banjar_adat_id = $banjar_adat_id;
        $kelahiran->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $kelahiran->krama_mipil_id = $kramaMipil->id;
        /**
         * Status ajuan kelahiran
         * 0 ajuan masuk
         * 1 ajuan di proses
         * 2 ajuan di tolak
         * 3 ajuan di acc, masuk ke tb_kelahiran
         */
        $kelahiran->status = '0';
        $convert_nomor_akta_kelahiran = str_replace("/","-",$request->nomor_akta_kelahiran);
        if($request->hasFile('file_akta_kelahiran')){
            $file = $request->file('file_akta_kelahiran');
            $fileLocation = '/file/'.$desa_adat->id.'/kelahiran/'.$convert_nomor_akta_kelahiran.'/lampiran';;
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $kelahiran->file_akta_kelahiran = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $kelahiran->user_id = Auth::user()->id;
        $kelahiran->keterangan = $request->keterangan;
        $kelahiran->save();
        $kelahiran = KelahiranAjuan::where('id', $kelahiran->id)->with('cacah_krama_mipil.penduduk')->first();

        /**
         * create notif baru
         */
        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan(Auth::user()->id, $banjar_adat_id, $kelahiran->id, $kramaMipil->id, 0);
        $userNotif = new User();

        $kramaMipil = KramaMipil::where('id', $kramaMipil->id)->with('cacah_krama_mipil.penduduk')->where('status', 1)->first();

        $kontenNotif = "Terdapat ajuan kelahiran baru oleh Krama ".$kramaMipil->cacah_krama_mipil->penduduk->nama." pada tanggal ".Helper::convert_date_to_locale_id($kelahiran->tanggal_lahir);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                null,
                                "Terdapat ajuan kelahiran baru",
                                $banjar_adat_id,
                                $kelahiran->id,
                                0,
                            );

        return redirect()->route('Kelahiran Home')->with(['success' => 'Data Kelahiran berhasil diajukan', 'is_ajuan' => true]);
    }
}
