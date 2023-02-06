<?php

namespace App\Http\Controllers\UserController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnggotaKeluargaKrama;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\KeluargaKrama;
use App\Models\Kematian;
use App\Models\KramaMipil;
use App\Models\Penduduk;
use App\Models\KematianAjuan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Models\Notifikasi;
use App\Models\User;
use App\Helper\Helper;

class KematianController extends Controller
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
         * get semua id krama mipil yang nomor krama mipilnya dari krama mipil di atas
         */
        $kramaMipilId = KramaMipil::where('nomor_krama_mipil', $kramaMipil->nomor_krama_mipil)->pluck('id')->toArray();
        /**
         * setelah dapet semua id krama mipil, baru get semua anggota dari semua id krama mipil yg cocok
         * (tujuan di buat gini, karena di tb_kematian nggak ada id krama mipil mana asal yg meninggal, jadi untuk nyari yang meninggal ini waktu di
         * krama mipil mana, harus nge get semua id krama mipil tempat waktu yg meninggal ini)
         */
        $anggotaKramaMipilId = AnggotaKramaMipil::whereIn('krama_mipil_id', $kramaMipilId)->pluck('cacah_krama_mipil_id')->toArray();

        // $arrayIdCacah = $anggotaKramaMipil->pluck('cacah_krama_mipil_id')->toArray();
        // array_push($arrayIdCacah, $kramaMipil->cacah_krama_mipil_id);

        $kematian = Kematian::whereIn('cacah_krama_mipil_id', $anggotaKramaMipilId)
                                ->where('status' ,1)
                                ->with('cacah_krama_mipil.penduduk', 'kematian_ajuan')
                                ->orderBy('created_at', 'desc')->get();
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
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian fail'
            ], 200);
        }
    }

    public function detail($id)
    {
        $kematian = Kematian::where('id', $id)->with('cacah_krama_mipil.penduduk', 'kematian_ajuan')->first();
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
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian fail'
            ], 200);
        }
    }

    public function getAjuan()
    {
        $penduduk_id = Auth::user()->user->penduduk_id;
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        $banjar_adat_id = $cacahKramaMipil->banjar_adat_id;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();

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

        $kramaMipilId = KramaMipil::where('nomor_krama_mipil', $kramaMipil->nomor_krama_mipil)->pluck('id')->toArray();
        $anggotaKramaMipilId = AnggotaKramaMipil::whereIn('krama_mipil_id', $kramaMipilId)->pluck('cacah_krama_mipil_id')->toArray();

        // $arrayIdCacah = $anggotaKramaMipil->pluck('cacah_krama_mipil_id')->toArray();
        // array_push($arrayIdCacah, $kramaMipil->cacah_krama_mipil_id);

        $kematian = KematianAjuan::whereIn('cacah_krama_mipil_id', $anggotaKramaMipilId)
                                ->with('cacah_krama_mipil.penduduk', 'kematian')
                                ->orderBy('created_at', 'desc')->get();
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
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian fail'
            ], 200);
        }
    }

    public function detailAjuan($id)
    {
        $kematian = KematianAjuan::where('id', $id)->with('cacah_krama_mipil.penduduk', 'kematian')->first();
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
                'statusCode' => 200,
                'status' => true,
                'data' => $kematian,
                'message' => 'data kematian fail'
            ], 200);
        }
    }

    public function getListCacahMipil()
    {
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
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
            if ($kramaMipil) {
                /**
                 * if krama mipil, return cacah anggota krama mipilnya
                 */
                $anggotaCacahArray = AnggotaKramaMipil::where('krama_mipil_id', $kramaMipil->id)->where('status', 1)->pluck('cacah_krama_mipil_id')->toArray();
                $kramas = CacahKramaMipil::with('penduduk')->whereIn('id', $anggotaCacahArray)->get();
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => $kramas,
                    'message' => 'data cacah krama mipil sukses'
                ], 200);
            }
            else {
                /**
                 * Klo bukan krama mipilnya, return cacah krama mipil dari krama mipilnya + anggota
                 * dari krama mipil yang nggak login
                 */
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->where('status', 1)->first();
                    $anggotaCacahArray = AnggotaKramaMipil::where('id', $kramaMipil->id)->where('status', 1)->pluck('id')->toArray();
                    $kramas = CacahKramaMipil::with('penduduk')->where('id', $kramaMipil->cacah_krama_mipil_id)->where('status', 1)->get();
                    /** Krama 2 di pake buat nyimpen collection yang isi anggota keluarga kramanya tanpa cacah user yg login */
                    $krama2 = CacahKramaMipil::with('penduduk')->whereIn('penduduk_id', $anggotaCacahArray)
                                            ->whereNotIn('id', [$cacahKramaMipil->id])->get();
                    $finalKrama = $kramas->merge($krama2);
                    return response()->json([
                        'statusCode' => 200,
                        'status' => true,
                        'data' => $finalKrama,
                        'message' => 'anggota krama mipil'
                    ], 200);
                }
                else {
                    return response()->json([
                        'statusCode' => 500,
                        'status' => false,
                        'data' => null,
                        'message' => 'data anggota krama mipil not found'
                    ], 200);
                }
            }
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data cacah krama mipil not found'
            ], 200);
        }
    }

    public function storeKematian(Request $request)
    {
        /**
         * Fungsi untuk nge create pengajuan kematian baru
         * form data:
         * nomor_akta_kematian
         * nomor_suket_kematian
         * cacah_krama_mipil (id)
         * tanggal_kematian (2000-12-30)
         * penyebab_kematian
         * file_akta_kematian
         * file_suket_kematian
         *
         * Return: data kematian
         */
        $penduduk_id = Auth::user()->user->penduduk_id;
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->with('penduduk')->first();
        $banjar_adat_id = $cacahKramaMipil->banjar_adat_id;
        $banjarAdat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjarAdat->desa_adat_id);

        $kematian = new KematianAjuan();
        $kematian->nomor_akta_kematian = $request->nomor_akta_kematian;
        $kematian->nomor_suket_kematian = $request->nomor_suket_kematian;
        $kematian->cacah_krama_mipil_id = $request->cacah_krama_mipil;
        $kematian->banjar_adat_id = $banjar_adat_id;
        $kematian->tanggal_kematian = $request->tanggal_kematian;
        $kematian->penyebab_kematian = $request->penyebab_kematian;
        $kematian->keterangan = $request->keterangan;
        $kematian->status = '0';
        $kematian->save();
        if($request->hasFile('file_akta_kematian')){
            $file = $request->file('file_akta_kematian');
            $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_akta_kematian';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $kematian->file_akta_kematian = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->hasFile('file_suket_kematian')){
            $file = $request->file('file_suket_kematian');
            $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_suket_kematian';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $kematian->file_suket_kematian = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        $kematian->user_id = Auth::user()->id;
        $kematian->update();
        $kematian = KematianAjuan::where('id', $kematian->id)->with('cacah_krama_mipil.penduduk')->first();

        $notifikasi = new Notifikasi();
        $notifikasi->notif_kematian_ajuan(Auth::user()->id, $banjar_adat_id, $kematian->id, $cacahKramaMipil->penduduk->nama, 0);
        error_log('ngirim notif gan');
        $userNotif = new User();

        error_log('ngirim notif gan 2');

        $kontenNotif = "Terdapat ajuan kematian baru oleh Krama ".$cacahKramaMipil->penduduk->nama." pada tanggal ".Helper::convert_date_to_locale_id($kematian->tanggal_kematian);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                null,
                                "Terdapat ajuan kematian baru",
                                $banjar_adat_id,
                                $kematian->id,
                                1,
                            );


        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $kematian,
            'message' => 'pengajuan kematian berhasil'
        ], 200);
    }
}
