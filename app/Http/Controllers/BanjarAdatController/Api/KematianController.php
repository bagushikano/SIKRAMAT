<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Kematian;
use App\Models\KematianAjuan;
use App\Models\CacahKramaMipil;
use App\Models\AnggotaKramaMipil;
use App\Models\KramaMipil;
use App\Models\Penduduk;
use App\Models\User;
use App\Models\Notifikasi;
use App\Helper\Helper;

class KematianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get()
    {
        $kematian = Kematian::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                            ->with('cacah_krama_mipil.penduduk')
                            ->orderBy('created_at', 'desc')
                            ->get();
        if ($kematian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data kematian fail'
            ], 500);
        }
    }

    public function detail($id)
    {
        $kematian = Kematian::where('id', $id)->with('cacah_krama_mipil.penduduk')->first();
        if ($kematian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => true,
                'data' => null,
                'message' => 'data kematian fail'
            ], 500);
        }
    }

    public function getAjuan()
    {
        $kematian = KematianAjuan::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                            ->with('cacah_krama_mipil.penduduk')
                            ->orderBy('created_at', 'desc')
                            ->get();
        if ($kematian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian fail'
            ], 500);
        }
    }

    public function detailAjuan($id)
    {
        $kematian = KematianAjuan::where('id', $id)->with('cacah_krama_mipil.penduduk', 'kematian')->first();
        $user = User::where('id', $kematian->user_id)->with('user.penduduk')->first();
        if ($kematian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'user' => $user->user->penduduk,
                'message' => 'data kematian sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian fail'
            ], 500);
        }
    }

    public function approveKematian($id)
    {
        /**
         * 1. Update ajuan kematian jadi 3 (di approve)
         * 2. copy data dari tb_kematian_ajuan jadi data tb_kematian
         * 3. klo acc, tanggal updated at yg jadi tanggal pengesahan data
         */
        $kematianAjuan = KematianAjuan::where('id', $id)->first();

        /**
         * update status kematian jadi 3 (approve)
         */
        $kematianAjuan->status = 3;
        $kematianAjuan->update();

        /**
         * Copy data tb_kelahiran_ajuan ke tb_kelahiran
         */
        $kematian = new Kematian();
        $kematian->nomor_akta_kematian = $kematianAjuan->nomor_akta_kematian;
        $kematian->nomor_suket_kematian = $kematianAjuan->nomor_suket_kematian;
        $kematian->cacah_krama_mipil_id = $kematianAjuan->cacah_krama_mipil_id;
        $kematian->banjar_adat_id = $kematianAjuan->banjar_adat_id;
        $kematian->tanggal_kematian = $kematianAjuan->tanggal_kematian;
        $kematian->penyebab_kematian = $kematianAjuan->penyebab_kematian;
        $kematian->keterangan = $kematianAjuan->keterangan;
        $kematian->status = 1;
        $kematian->file_akta_kematian = $kematianAjuan->file_akta_kematian;
        $kematian->file_suket_kematian = $kematianAjuan->file_suket_kematian;
        $kematian->save();

        /**
         * isi kelahiran_id di data ajuan kelahiran
         */
        $kematianAjuan->kematian_id = $kematian->id;
        $kematianAjuan->update();

         /**
          * cari yg meninggal
          */
         $cacah_krama_mipil = CacahKramaMipil::find($kematian->cacah_krama_mipil_id);
         $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

         /**
          * nonaktif cacah + penduduk
          */
         $cacah_krama_mipil->status = '0';
         $cacah_krama_mipil->update();
         $penduduk->tanggal_kematian = $kematian->tanggal_kematian;
         $penduduk->update();

         /**
          * Keluarin yang meninggal dari anggota keluarga jika anggota keluarga
          */
         $anggota_keluarga = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->where('status', '1')->first();
         if($anggota_keluarga){
             /**
              * cari kk yg lama
              */
             $krama_mipil_lama = KramaMipil::find($anggota_keluarga->krama_mipil_id);
             $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

             /**
              * copy data dari kk lama ke kk baru
              */
             $krama_mipil_baru = new KramaMipil();
             $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
             $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
             $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
             $krama_mipil_baru->status = '1';
             $krama_mipil_baru->alasan_perubahan = 'Kematian Anggota Keluarga';
             $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
             $krama_mipil_baru->save();

             foreach($anggota_krama_mipil_lama as $anggota_lama){
                 if($anggota_lama->id != $anggota_keluarga->id){
                     $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                     $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                     $anggota_krama_mipil_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                     $anggota_krama_mipil_baru->status_hubungan = $anggota_lama->status_hubungan;
                     $anggota_krama_mipil_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                     $anggota_krama_mipil_baru->status = '1';
                     $anggota_krama_mipil_baru->save();
                 }
                 /**
                  * Nonaktifin anggota keluarga yang lama yg lama
                  */
                 $anggota_lama->status = '0';
                 $anggota_lama->update();
             }

             /**
              * nonaktifin krama mipil yg lama
              */
             $krama_mipil_lama->status = '0';
             $krama_mipil_lama->update();
         }

        $notifikasi = new Notifikasi();
        $userAjuan = User::where('id', $kematianAjuan->user_id)->with('user.penduduk')->first();

        $notifikasi->notif_kematian_ajuan($kematianAjuan->user_id, $kematian->banjar_adat_id, $kematianAjuan->id, $userAjuan->user->penduduk->nama, 3);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kematian telah disahkan oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kematian->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kematianAjuan->user_id,
                                "Ajuan telah disahkan.",
                                null,
                                $kematianAjuan->id,
                                1,
                            );

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $kematian,
            'message' => 'approve kematian berhasil'
        ], 200);
    }

    public function tolakKematian(Request $req)
    {
        $kematian = KematianAjuan::where('id', $req->id)->first();
        $kematian->status = 2;
        $kematian->alasan_tolak_ajuan = $req->alasan;
        $kematian->save();

        $notifikasi = new Notifikasi();
        $userAjuan = User::where('id', $kematian->user_id)->with('user.penduduk')->first();

        $notifikasi->notif_kematian_ajuan($kematian->user_id, $kematian->banjar_adat_id, $kematian->id, $userAjuan->user->penduduk->nama, 2);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kematian tidak dapat disahkan dengan alasan: ".$kematian->alasan_tolak_ajuan;
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kematian->user_id,
                                "Ajuan kematian tidak dapat disahkan.",
                                null,
                                $kematian->id,
                                1,
                            );

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $kematian,
            'message' => 'tolak kematian berhasil'
        ], 200);
    }

    public function prosesKematian(Request $req)
    {
        $kematian = KematianAjuan::where('id', $req->id)->first();
        $kematian->status = 1;
        $kematian->save();

        $notifikasi = new Notifikasi();
        $userAjuan = User::where('id', $kematian->user_id)->with('user.penduduk')->first();
        $notifikasi->notif_kematian_ajuan($kematian->user_id, $kematian->banjar_adat_id, $kematian->id, $userAjuan->user->penduduk->nama, 1);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kematian sedang dalam proses oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kematian->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kematian->user_id,
                                "Ajuan kematian sedang dalam proses.",
                                null,
                                $kematian->id,
                                1,
                            );

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $kematian,
            'message' => 'proses kematian berhasil'
        ], 200);
    }
}
