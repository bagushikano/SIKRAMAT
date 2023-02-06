<?php

namespace App\Http\Controllers\UserController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\AkunKrama;
Use App\Models\Penduduk;
use App\Models\CacahKramaTamiu;
use App\Models\CacahKramaMipil;

class PendudukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getPenduduk() {
        $penduduk = Penduduk::where('id', Auth::user()->user->penduduk_id)->first();
        if ($penduduk) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $penduduk,
                'message' => 'data penduduk sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $penduduk,
                'message' => 'data penduduk fail'
            ], 200);
        }
    }

    public function editPenduduk(Request $req) {


        if ($penduduk) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $penduduk,
                'message' => 'data penduduk sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $penduduk,
                'message' => 'data penduduk fail'
            ], 200);
        }
    }

    public function changePassword(Request $req) {
        if(Hash::check($req->old_password, Auth::user()->password)) {
            $user = tap(Auth::user())->update([
                'password' => Hash::make($req->password),
            ]);
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Auth::user(),
                'message' => 'ganti password sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => Auth::user(),
                'message' => 'ganti password gagal'
            ], 200);
        }
    }

}
