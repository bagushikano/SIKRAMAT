<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanjarAdat;
use App\Models\KramaMipil;
use App\Models\AnggotaKramaMipil;
use Auth;

class KramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get() {
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')
                                ->where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                ->where('status', 1)->paginate(100);
        if ($krama_mipil != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama_mipil,
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

    public function getSingle($krama_id) {
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')->where('id', $krama_id)->first();
        if ($krama_mipil != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama_mipil,
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

    public function detail($krama_id) {
        $detail_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_id)->get();
        if ($detail_mipil != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $detail_mipil,
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
}
