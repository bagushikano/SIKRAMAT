<?php

namespace App\Http\Controllers\AuthController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Admin;
use App\Models\AdminDesaAdat;
use App\Models\DesaAdat;
use App\Models\Kabupaten;
use App\Models\DesaDinas;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::guard()->attempt(['email' => $request->email, 'password' => $request->password])){
            $token = auth()->user()->createToken('API Token')->accessToken;
            if (auth()->user()->email_verified_at == null) {
                return response()->json([
                    'statusCode' => 403,
                    'status' => false,
                    'data' => null,
                    'token' => null,
                    'message' => 'email not verified'
                ], 200);
            } else {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => auth()->user()->role,
                    'token' => $token,
                    'message' => 'Login berhasil'
                ], 200);
            }

        } else {
            return response()->json([
                'statusCode' => 403,
                'status' => false,
                'data' => null,
                'token' => null,
                'message' => 'Login gagal'
            ], 200);
        }
    }

    public function changePassword(Request $req)
    {
        if (Hash::check($req->old_password, Auth::user()->password)) {
            $user = tap(Auth::user())->update([
                'password' => Hash::make($req->password),
            ]);

            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'message' => 'Berhasil mengubah password',
                'data' => Auth::user()
            ], 200);
        }

        return response()->json([
            'statusCode' => 200,
            'status' => false,
            'message' => 'Gagal mengubah password. Password lama tidak sesuai',
            'data' => Auth::user()
        ], 200);
    }

    public function createToken() {

    }

    // public function profile() {
    //     $desa_adat_id = Auth::user()->admin_desa_adat->desa_adat_id;
    // }
}
