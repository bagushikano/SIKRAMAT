<?php

namespace App\Http\Controllers\NotifikasiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FcmToken;
use App\Models\Notifikasi;
use App\Models\KramaMipil;
use App\Models\CacahKramaMipil;
use App\Models\Maperas;
use App\Models\Perceraian;
use App\Models\Perkawinan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class NotifikasiController extends Controller
{
    public function storeFirebaseToken(Request $request)
    {
          if (!$request->firebase_token) {
              return response()->json([
                  'statusCode' => 200,
                  'status' => false,
                  'message' => "Gagal memperbarui token"
              ]);
          }

          $matchingUser = User::find(Auth::user()->id);

          if ($matchingUser) {
              $fcmToken = new FcmToken();
              $fcmToken->user_id = $matchingUser->id;
              $fcmToken->token = $request->firebase_token;
              $fcmToken->save();


              return response()->json([
                  'statusCode' => 200,
                  'status' => true,
                  'message' => "Berhasil memperbarui token"
              ]);
          }
          return response()->json([
              'statusCode' => 200,
              'status' => false,
              'message' => "Gagal memperbarui token"
          ]);
    }

    public function getNotifikasi($role)
    {
        if ($role == 'kelihan_adat' || $role == 'pangliman_banjar' || $role == 'penyarikan_banjar' || $role == 'patengen_banjar') {
            $notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('is_read', 0)->orderBy('created_at', 'DESC')->get();
            $jumlah_notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('is_read', 0)->count();
        }
        else if ($role == 'admin_banjar_adat') {
            $notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->admin_banjar_adat->banjar_adat_id)->where('is_read', 0)->orderBy('created_at', 'DESC')->get();
            $jumlah_notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->admin_banjar_adat->banjar_adat_id)->where('is_read', 0)->count();
        }
        else if ($role == 'krama') {
            $notifikasi = Notifikasi::where('user_id', Auth::user()->id)->where('is_read', 0)->orderBy('created_at', 'DESC')->get();
            $jumlah_notifikasi = $notifikasi->count();
        }
        if ($notifikasi->isNotEmpty()) {
            $notifikasi->map(function ($item){
                //GET TANGGAL SEKARANG
                $converted_created_at = Carbon::parse($item->created_at)->locale('id');
                $converted_created_at->settings(['formatFunction' => 'translatedFormat']);
                $converted_created_at = $converted_created_at->format('j F Y');
                $item->converted_created_at = $converted_created_at;
                return $item;
            });
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $notifikasi,
                'jumlah_notifikasi' => $jumlah_notifikasi,
                'message' => 'data notifikasi sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data notifikasi gagal'
            ], 200);
        }
    }

    public function markAllAsRead($role)
    {
        $status = false;
        if ($role == 'kelihan_adat' || $role == 'pangliman_banjar' || $role == 'penyarikan_banjar' || $role == 'patengen_banjar') {
            $notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('is_read', 0)->update([
                'is_read' => 1
            ]);
            $status = true;
        }
        else if ($role == 'admin_banjar_adat') {
            $notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->admin_banjar_adat->banjar_adat_id)->where('is_read', 0)->update([
                'is_read' => 1
            ]);
            $status = true;
        }
        else if ($role == 'krama') {
            $notifikasi = Notifikasi::where('user_id', Auth::user()->id)->where('is_read', 0)->update([
                'is_read' => 1
            ]);
            $status = true;
        }
        if ($status) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => null,
                'message' => 'read notifikasi sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'read notifikasi gagal'
            ], 200);
        }
    }

    public function readNotifikasi($id){
        $notifikasi = Notifikasi::find($id);
        $notifikasi->is_read = '1';
        $notifikasi->update();

        /**
         * if notif pendataan
         */
        if($notifikasi->jenis == '0'){
            if($notifikasi->sub_jenis == 'kematian'){
                /**
                 * if yang mati adalah seorang krama (kepala keluarga)
                 */
                return redirect()->route('banjar-krama-mipil-home');
            }
            else if($notifikasi->sub_jenis == 'perkawinan'){
                $perkawinan = Perkawinan::find($notifikasi->data_id);
                if($perkawinan->status_perkawinan == '2'){
                    /**
                     * if pendataan ditolak oleh pihak pradana, pihak purusa return ke halaman utama perkawinan
                     */
                    return redirect()->route('banjar-perkawinan-home');
                }
                else if($perkawinan->status_perkawinan == '0'){
                    /**
                     * if pendataan baru dibuat oleh pihak purusa, pihak pradana return ke detail untuk konfirmasi/tolak
                     */
                    return redirect()->route('banjar-perkawinan-keluar-detail', $perkawinan->id);
                }
                else if($perkawinan->status_perkawinan == '1'){
                    /**
                     * if pendataan dikonfirmasi oleh pihak pradana, pihak purusa return ke detail untuk mengesahkan
                     */
                    return redirect()->route('banjar-perkawinan-masuk-detail', $perkawinan->id);
                }
                else{
                    /**
                     * if pendataan sah
                     */
                    return redirect()->route('banjar-perkawinan-detail', $perkawinan->id);
                }

            }
            else if($notifikasi->sub_jenis == 'perceraian'){
                $perceraian = Perceraian::find($notifikasi->data_id);
                if($perceraian->status_perceraian == '2'){
                    /**
                     * if pendataan ditolak oleh pihak pradana, pihak purusa return ke halaman utama perkawinan
                     */
                    return redirect()->route('banjar-perceraian-home');
                }else{
                    return redirect()->route('banjar-perceraian-detail', $perceraian->id);
                }
            }else if($notifikasi->sub_jenis == 'maperas'){
                $maperas = Maperas::find($notifikasi->data_id);
                if($maperas->status_maperas == '2'){
                    /**
                     * if pendataan ditolak oleh pihak asal, pihak meras return ke halaman utama perkawinan
                     */
                    return redirect()->route('banjar-maperas-home');
                }
                else if($maperas->status_maperas == '0'){
                    /**
                     * if pendataan baru dibuat oleh pihak baru, pihak lama return ke detail untuk konfirmasi/tolak
                     */
                    return redirect()->route('banjar-maperas-keluar-detail', $maperas->id);
                }
                else if($maperas->status_maperas == '1'){
                    /**
                     * if pendataan dikonfirmasi oleh pihak asal, pihak baru return ke detail untuk mengesahkan
                     */
                    return redirect()->route('banjar-maperas-masuk-detail', $maperas->id);
                }
                else{
                    /**
                     * if pendataan sah
                     */
                    return redirect()->route('banjar-maperas-detail', $maperas->id);
                }
            }
        }

        /**
         * if notif pengajuan
         */
        if($notifikasi->jenis == '1'){
            $user = Auth::user();
            if($notifikasi->user_id == $user->id && $notifikasi->banjar_adat_id == NULL){
                if($notifikasi->sub_jenis == 'kelahiran'){
                    return redirect()->route('Kelahiran Detail Ajuan', $notifikasi->data_id);
                }
                if($notifikasi->sub_jenis == 'kematian'){
                    return redirect()->route('Kematian Detail Ajuan', $notifikasi->data_id);
                }
            }else{
                if($notifikasi->sub_jenis == 'kelahiran'){
                    return redirect()->route('banjar-ajuan-kelahiran-detail', $notifikasi->data_id);
                } 
                if($notifikasi->sub_jenis == 'kematian'){
                    return redirect()->route('banjar-ajuan-kematian-detail', $notifikasi->data_id);
                } 
            }
        }
        
        
    }
}
