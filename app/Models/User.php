<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Fcm;


class User extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes, HasApiTokens, Notifiable;
    protected $table = 'tb_user';
    protected $guard = 'user';

    public $timestamps = false;

    protected $fillable = [
        'email',
        'password',
    ];

    public function admin_desa_adat()
    {
        return $this->hasOne(AdminDesaAdat::class, 'user_id');
    }

    public function admin_banjar_adat()
    {
        return $this->hasOne(AdminBanjarAdat::class, 'user_id');
    }

    public function prajuru_banjar_adat()
    {
        return $this->hasOne(PrajuruBanjarAdat::class, 'user_id');
    }

    public function user()
    {
        return $this->hasOne(AkunKrama::class, 'user_id');
    }

    public function sendNotifAjuan($konten, $idUser, $title, $idBanjar, $idData, $type) {
        error_log('ngirim notif gan 3');

        $recipients = [];
        $userType = null;
        /**
         * user type
         * 0 = krama
         * 1 = prajuru
         */
        if ($idUser != null) {
            $userType = 0;
            $recipients = FcmToken::where('user_id', $idUser)->pluck('token')->toArray();
        } else {
            $userType = 1;
            $prajuruBanjarId = PrajuruBanjarAdat::where('banjar_adat_id', $idBanjar)->pluck('user_id')->toArray();
            error_log(implode(',', $prajuruBanjarId));
            $userIds = User::whereIn('role', ['kelihan_adat', 'pangliman_banjar', 'penyarikan_banjar', 'patengen_banjar'])
                            ->whereIn('id', $prajuruBanjarId)
                            ->pluck('id')
                            ->toArray();

            $recipients = FcmToken::whereIn('user_id', $userIds)->pluck('token')->toArray();
        }

        error_log('ngirim notif gan 4');
        error_log(implode(',', $recipients));

        /**
         * Type
         * 0 = kelahiran
         * 1 = Kematian
         */

        fcm()
            ->to($recipients)
            ->priority('normal')
            ->timeToLive(0)
            ->data([
                'title' => $title,
                'content' => $konten,
                'type' => $type,
                'user_type' => $userType,
                'data_id' => $idData,
            ])
            ->send();
    }

    public function sendNotifPendataan($konten, $idUser, $title, $idBanjar, $idData, $type) {
        error_log('ngirim notif pendataan gan');

        $recipients = [];
        $userType = null;
        /**
         * user type
         * 0 = krama
         * 1 = prajuru
         */
        $userType = 1;

        $prajuruBanjarId = PrajuruBanjarAdat::where('banjar_adat_id', $idBanjar)->pluck('user_id')->toArray();
        error_log(implode(',', $prajuruBanjarId));
        $userIds = User::whereIn('role', ['kelihan_adat', 'pangliman_banjar', 'penyarikan_banjar', 'patengen_banjar'])
                        ->whereIn('id', $prajuruBanjarId)
                        ->pluck('id')
                        ->toArray();
        $recipients = FcmToken::whereIn('user_id', $userIds)->pluck('token')->toArray();

        error_log('ngirim notif pendataan gan');

        /**
         * Type
         * 0 = kelahiran
         * 1 = Kematian
         * 2 = Perkawinan
         * 3 = Maperas
         * 4 = perceraian
         */

        fcm()
            ->to($recipients)
            ->priority('normal')
            ->timeToLive(0)
            ->data([
                'title' => $title,
                'content' => $konten,
                'type' => $type,
                'user_type' => $userType,
                'data_id' => $idData,
            ])
            ->enableResponseLog()
            ->send();
    }

    public function count_notifikasi_banjar(){
        if(Auth::user()->role == 'admin_banjar_adat'){
            $jumlah_notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->admin_banjar_adat->banjar_adat_id)->where('is_read', 0)->count();
        }else if(Auth::user()->role == 'kelihan_adat' || Auth::user()->role == 'pangliman_banjar' || Auth::user()->role == 'penyarikan_banjar' || Auth::user()->role == 'patengen_banjar'){
            $jumlah_notifikasi = Notifikasi::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('is_read', 0)->count();
        }
        return $jumlah_notifikasi;
    }

    public function count_notifikasi_krama(){
        if(Auth::user()->role == 'krama' || Auth::user()->role == 'kelihan_adat' || Auth::user()->role == 'pangliman_banjar' || Auth::user()->role == 'penyarikan_banjar' || Auth::user()->role == 'patengen_banjar' || Auth::user()->role == 'bendesa' || Auth::user()->role == 'pangliman' || Auth::user()->role == 'penyarikan' || Auth::user()->role == 'patengen'){
            $jumlah_notifikasi = Notifikasi::where('user_id', Auth::user()->id)->where('is_read', 0)->count();
        }
        return $jumlah_notifikasi;
    }
}
