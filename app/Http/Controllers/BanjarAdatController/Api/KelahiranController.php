<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\CacahKramaMipil;
use App\Models\KramaMipil;
use App\Models\AnggotaKramaMipil;
use App\Models\Penduduk;
use App\Models\KelahiranAjuan;
use App\Models\Notifikasi;
use App\Models\User;
use App\Helper\Helper;

class KelahiranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get()
    {
        $kelahiran = Kelahiran::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                            ->with('cacah_krama_mipil.penduduk')
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
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data kelahiran fail'
            ], 200);
        }
    }

    public function getAjuan()
    {
        $kelahiran = KelahiranAjuan::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
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
                'statusCode' => 500,
                'status' => true,
                'data' => null,
                'message' => 'data kelahiran fail'
            ], 200);
        }
    }


    public function detailAjuan($id)
    {
        $kelahiran = KelahiranAjuan::where('id', $id)->with('cacah_krama_mipil.penduduk', 'kelahiran')->first();
        $user = User::where('id', $kelahiran->user_id)->with('user.penduduk')->first();
        if ($kelahiran) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kelahiran,
                'user' => $user->user->penduduk,
                'message' => 'data kelahiran sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => true,
                'data' => null,
                'message' => 'data kelahiran fail'
            ], 200);
        }
    }

    public function approveKelahiran($id)
    {
        /**
         * 1. Update ajuan kelahiran jadi 3 (di approve)
         * 2. copy data dari tb_kelahiran_ajuan jadi data tb_kelahiran
         * 3. klo acc, tanggal updated at yg jadi tanggal pengesahan data
         */

        $kelahiranAjuan = KelahiranAjuan::where('id', $id)->first();

        /**
         * update status ajuan kelahiran jadi 3 (di acc)
         */
        $kelahiranAjuan->status = 3;
        $kelahiranAjuan->update();

        /**
         * Copy data tb_kelahiran_ajuan ke tb_kelahiran
         */
        $kelahiran= new Kelahiran();
        $kelahiran->status = 1;
        $kelahiran->nomor_akta_kelahiran = $kelahiranAjuan->nomor_akta_kelahiran;
        $kelahiran->cacah_krama_mipil_id = $kelahiranAjuan->cacah_krama_mipil_id;
        $kelahiran->banjar_adat_id = $kelahiranAjuan->banjar_adat_id;
        $kelahiran->krama_mipil_id = $kelahiranAjuan->krama_mipil_id;
        $kelahiran->file_akta_kelahiran = $kelahiranAjuan->file_akta_kelahiran;
        $kelahiran->keterangan = $kelahiranAjuan->keterangan;
        $kelahiran->save();

        /**
         * isi kelahiran_id di data ajuan kelahiran
         */
         $kelahiranAjuan->kelahiran_id = $kelahiran->id;
         $kelahiranAjuan->update();

        /**
         * Update cacah mipil jadi 1 (aktif)
         */
        $cacah_krama_mipil = CacahKramaMipil::where('id', $kelahiran->cacah_krama_mipil_id)->first();
        $cacah_krama_mipil->status = 1;
        $cacah_krama_mipil->save();

        $pendudukLahir = Penduduk::where('id', $cacah_krama_mipil->penduduk_id)->first();

        /**
         * Update keluarga
         */
        $krama_mipil_lama = KramaMipil::find($kelahiran->krama_mipil_id);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

        /**
         * Copy data dari keluarga lama ke keluarga baru
         */
        $krama_mipil_baru = new KramaMipil();
        $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
        $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
        $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
        $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
        $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
        $krama_mipil_baru->status = '1';
        $krama_mipil_baru->alasan_perubahan = 'Kelahiran Anggota Keluarga Baru';
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

            /**
             * Nonaktif anggota keluarga yang lama
             */
            $anggota_lama->status = '0';
            $anggota_lama->update();
        }

        /**
         * Insert yang baru lahir ke anggota keluarga
         */
        $anggota_krama_mipil = new AnggotaKramaMipil();
        $anggota_krama_mipil->krama_mipil_id = $krama_mipil_baru->id;
        $anggota_krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        $anggota_krama_mipil->status_hubungan = 'anak';
        $anggota_krama_mipil->tanggal_registrasi = $pendudukLahir->tanggal_lahir;
        $anggota_krama_mipil->status = '1';
        $anggota_krama_mipil->save();

        /**
         * nonaktifin keluarga yg lama
         */
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();

        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan($kelahiranAjuan->user_id, $kelahiran->banjar_adat_id, $kelahiranAjuan->id, $kelahiran->krama_mipil_id, 3);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kelahiran telah disahkan oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kelahiran->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kelahiranAjuan->user_id,
                                "Ajuan telah disahkan.",
                                null,
                                $kelahiranAjuan->id,
                                0,
                            );

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $krama_mipil_baru,
            'message' => 'approve kelahiran berhasil'
        ], 200);
    }

    public function tolakKelahiran(Request $req)
    {
        $kelahiran = KelahiranAjuan::where('id', $req->id)->first();
        $kelahiran->status = 2;
        $kelahiran->alasan_tolak_ajuan = $req->alasan;
        $kelahiran->save();

        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan($kelahiran->user_id, $kelahiran->banjar_adat_id, $kelahiran->id, $kelahiran->krama_mipil_id, 2);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kelahiran tidak dapat disahkan dengan alasan: ".$kelahiran->alasan_tolak_ajuan;
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kelahiran->user_id,
                                "Ajuan kelahiran tidak dapat disahkan.",
                                null,
                                $kelahiran->id,
                                0,
                            );

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $kelahiran,
            'message' => 'tolak kelahiran berhasil'
        ], 200);
    }

    public function prosesKelahiran(Request $req)
    {
        $kelahiran = KelahiranAjuan::where('id', $req->id)->first();
        $kelahiran->status = 1;
        $kelahiran->save();

        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan($kelahiran->user_id, $kelahiran->banjar_adat_id, $kelahiran->id, $kelahiran->krama_mipil_id, 1);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kelahiran sedang dalam proses oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kelahiran->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kelahiran->user_id,
                                "Ajuan kelahiran sedang dalam proses.",
                                null,
                                $kelahiran->id,
                                0,
                            );

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $kelahiran,
            'message' => 'proses kelahiran berhasil'
        ], 200);
    }
}
