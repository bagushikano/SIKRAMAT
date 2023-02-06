<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CacahKramaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getCacahMipil(Request $req) {
        $cacah_krama_mipil = KramaMipil::with('anggota.cacah_krama_mipil.penduduk', 'banjar_adat')
                                    ->where('nomor_krama_mipil', $req->query('no_krama_mipil'))
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        if ($cacah_krama_mipil != null) {
            if ($cacah_krama_mipil->status != 0) {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => $cacah_krama_mipil,
                    'message' => 'data cacah krama mipil sukses'
                ], 200);
            }
            else {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => null,
                    'message' => 'krama cacah mipil nonaktif'
                ], 200);
            }
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'not found'
            ], 200);
        }
    }
}
