<?php

namespace App\Http\Controllers\UserController\Api;

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
use Carbon\Carbon;
use App\Helper\Helper;
use App\Models\User;

class KelahiranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get()
    {
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
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->where('status', 1)->first();
                }
            }
        }
        /**
         * Get id krama mipil dengan nomer krama mipilnya
         */
        $kramaMipilId = KramaMipil::where('nomor_krama_mipil', $kramaMipil->nomor_krama_mipil)->pluck('id')->toArray();
        $kelahiran = Kelahiran::whereIn('krama_mipil_id', $kramaMipilId)
                                    ->where('status', 1)->with('cacah_krama_mipil.penduduk', 'kelahiran_ajuan')
                                    ->orderBy('created_at', 'desc')->get();
        if ($kelahiran) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran fail'
            ], 200);
        }
    }

    public function detail($id)
    {
        $kelahiran = Kelahiran::where('id', $id)->with('cacah_krama_mipil.penduduk', 'kelahiran_ajuan')->first();
        if ($kelahiran) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran fail'
            ], 200);
        }
    }

    public function getAjuan()
    {
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
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->where('status', 1)->first();

                }
            }
        }
        /**
         * Get id krama mipil dengan nomer krama mipilnya
         */
        $kramaMipilId = KramaMipil::where('nomor_krama_mipil', $kramaMipil->nomor_krama_mipil)->pluck('id')->toArray();
        $kelahiran = KelahiranAjuan::whereIn('krama_mipil_id', $kramaMipilId)
                                    ->with('cacah_krama_mipil.penduduk', 'kelahiran')
                                    ->orderBy('created_at', 'desc')->get();
        if ($kelahiran) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran fail'
            ], 200);
        }
    }

    public function detailAjuan($id)
    {
        $kelahiran = KelahiranAjuan::where('id', $id)->with('cacah_krama_mipil.penduduk', 'kelahiran')->first();
        if ($kelahiran) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'message' => 'data kelahiran fail'
            ], 200);
        }
    }

    public function storePengajuan(Request $request)
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
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->where('status', 1)->first();
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
        $penduduk->tanggal_lahir = $request->tanggal_lahir;
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->alamat = $pendudukAyah->alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        if($request->hasFile('foto')){
            $file = $request->file('foto');
            $filename = uniqid().'.png';
            $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
            $path = $fileLocation."/".$filename;
            $penduduk->foto = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $penduduk->save();

        /**
         * generate NICK
         */
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
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
        $kelahiran->tanggal_lahir = $request->tanggal_lahir;
        $kelahiran->keterangan = $request->keterangan;
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
        $kelahiran->save();
        $kelahiran = KelahiranAjuan::where('id', $kelahiran->id)->with('cacah_krama_mipil.penduduk')->first();

        /**
         * create notif baru
         */
        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan(Auth::user()->id, $banjar_adat_id, $kelahiran->id, $kramaMipil->id, 0);
        error_log('ngirim notif gan');
        $userNotif = new User();

        $kramaMipil = KramaMipil::where('id', $kramaMipil->id)->with('cacah_krama_mipil.penduduk')->where('status', 1)->first();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Terdapat ajuan kelahiran baru oleh Krama ".$kramaMipil->cacah_krama_mipil->penduduk->nama." pada tanggal ".Helper::convert_date_to_locale_id($kelahiran->tanggal_lahir);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                null,
                                "Terdapat ajuan kelahiran baru",
                                $banjar_adat_id,
                                $kelahiran->id,
                                0,
                            );


        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $kelahiran,
            'message' => 'pengajuan kelahiran berhasil'
        ], 200);
    }
}
