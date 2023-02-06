<?php

namespace App\Http\Controllers\UserController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\Penduduk;

class CacahKramaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getCacahMipilTamiu() {
        $penduduk = Penduduk::where('id', Auth::user()->user->penduduk_id)->with('pendidikan', 'pekerjaan')->first();
        if ($penduduk) {
            $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)
                                                ->where('status', 1)
                                                ->whereNull("tanggal_nonaktif")
                                                ->with("banjar_adat.desa_adat", "tempekan")
                                                ->first();
            $cacahKramaTamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)
                                                ->whereNull('tanggal_keluar')
                                                ->with("banjar_adat.desa_adat")
                                                ->first();
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'penduduk' => $penduduk,
                'cacahKramaMipil' => $cacahKramaMipil,
                'cacahKramaTamiu' => $cacahKramaTamiu,
                'message' => 'data penduduk sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data penduduk fail'
            ], 200);
        }
    }


    public function getCacahMipil() {
        $penduduk = Penduduk::where('id', Auth::user()->user->penduduk_id)->first();
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)
                                                ->where('status', 1)
                                                ->whereNull("tanggal_nonaktif")
                                                ->with("banjar_adat.desa_adat", "tempekan")
                                                ->first();
        if ($cacahKramaMipil != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $cacahKramaMipil,
                'message' => 'data krama mipil sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama mipil fail'
            ], 200);
        }
    }

    public function getCacahTamiu() {
        $penduduk_id = Auth::user()->user->id;
        $cacahKramaTamiu = CacahKramaTamiu::where('penduduk_id', $penduduk_id)->first();
        if ($cacahKramaTamiu != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $cacahKramaTamiu,
                'message' => 'data krama tamiu sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama tamiu fail'
            ], 200);
        }
    }
}
