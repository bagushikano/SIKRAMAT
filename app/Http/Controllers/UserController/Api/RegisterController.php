<?php

namespace App\Http\Controllers\UserController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\User;
use App\Models\AkunKrama;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;

use App\Models\DesaAdat;
use App\Models\Tempekan;
use App\Models\DesaDinas;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\BanjarDinas;

class RegisterController extends Controller
{
    public function registerCheckNik(Request $req) {
        $penduduk = Penduduk::where('nik', $req->nik)->first();
        if ($penduduk != null) {
            if(AkunKrama::where('penduduk_id', $penduduk->id)->count() > 0) {
                return response()->json([
                    'statusCode' => 500,
                    'status' => false,
                    'data' => null,
                    'message' => 'akun sudah ada'
                ], 200);
            }
            else {
                if (CacahKramaMipil::where('penduduk_id', $penduduk->id)->count() > 0 || CacahKramaTamiu::where('penduduk_id', $penduduk->id)->count() > 0) {
                    return response()->json([
                        'statusCode' => 200,
                        'status' => true,
                        'data' => $penduduk,
                        'message' => 'data penduduk ditemukan'
                    ], 200);
                }
                else {
                    return response()->json([
                        'statusCode' => 500,
                        'status' => false,
                        'data' => null,
                        'message' => 'data penduduk tidak ditemukan'
                    ], 200);
                }
            }
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data penduduk tidak ditemukan'
            ], 200);
        }
    }

    public function register(Request $req) {
        if(User::where('email', $req->email)->count() > 0) {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'email sudah digunakan'
            ], 200);
        }

        $user = new User();
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->role = "krama";
        $user->save();

        $akun_krama = new AkunKrama();
        $akun_krama->penduduk_id = $req->penduduk_id;
        $akun_krama->user_id = $user->id;
        $akun_krama->status = "aktif";
        $akun_krama->save();

        if ($akun_krama != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $akun_krama,
                'message' => 'register berhasil'
            ], 200);
        }
        else {
            $user->destroy();
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'register gagal'
            ], 200);
        }
    }
}
