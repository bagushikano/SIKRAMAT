<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnggotaKeluargaKrama;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaTamiu;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\Auth;

class CacahKramaTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get() {
        $kramas = CacahKramaTamiu::with('penduduk')->where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                        ->where('status', 1)->whereNull('tanggal_keluar')->paginate(10);
        if ($kramas != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
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
        $krama = CacahKramaTamiu::with('penduduk', 'banjar_adat', 'penduduk.pekerjaan', 'penduduk.pendidikan', 'banjar_dinas')->where('id', $krama_id)->first();
        if ($krama != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama,
                'message' => 'data cacah krama mipil sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data cacah krama mipil fail'
            ], 200);
        }
    }
}
