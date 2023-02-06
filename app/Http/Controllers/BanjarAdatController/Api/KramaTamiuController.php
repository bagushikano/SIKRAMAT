<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanjarAdat;
use App\Models\KramaTamiu;
use App\Models\AnggotaKramaTamiu;
use Auth;

class KramaTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get() {
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk', 'cacah_krama_tamiu.banjar_dinas', 'banjar_adat')
                                ->where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                ->where('status', 1)->paginate(10);
        if ($krama_tamiu != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama_tamiu,
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

    public function getSingle($krama_id) {
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk', 'banjar_adat')->where('id', $krama_id)->first();
        if ($krama_tamiu != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama_tamiu,
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

    public function detail($krama_id) {
        $detail_tamiu = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->where('krama_tamiu_id', $krama_id)->get();
        if ($detail_tamiu != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $detail_tamiu,
                'message' => 'data krama tamiu sukses'
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
