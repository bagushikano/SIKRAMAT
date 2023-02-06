<?php

namespace App\Http\Controllers\DesaAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnggotaKeluargaKrama;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\Auth;

class CacahKramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get() {
        $desa_adat_id = Auth::user()->admin_desa_adat->desa_adat_id;
        $arr_banjar_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $kramas = CacahKramaMipil::with('penduduk')->whereIn('banjar_adat_id', $arr_banjar_id)->paginate(10);
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
        $krama = CacahKramaMipil::with('penduduk', 'banjar_adat', 'penduduk.pekerjaan', 'penduduk.pendidikan', 'banjar_dinas', 'tempekan')->where('id', $krama_id)->first();
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
