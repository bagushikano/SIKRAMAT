<?php

namespace App\Http\Controllers\AdminController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Admin;
use App\Models\BanjarDinas;
use App\Models\BanjarAdat;
use App\Models\KramaMipil;
use App\Models\KramaTamiu;
use App\Models\Keluarga;
use App\Models\DesaAdat;
use App\Models\CacahKramaTamiu;
use App\Models\CacahKramaMipil;
use App\Models\CacahTamiu;
use App\Models\Tamiu;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\Perceraian;
use App\Models\Perkawinan;
use App\Models\Maperas;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function test(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => auth()->user(),
            'test_data' => $req->data,
            'message' => 'test middleware'
        ], 200);
    }

    public function dashboardData() {
        $kramaMipil = KramaMipil::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $kramaTamiu = KramaTamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $cacahMipil = CacahKramaMipil::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $cacahTamiu = CacahKramaTamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->whereNull('tanggal_keluar')->count();
        $banjarAdat = BanjarAdat::where('id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $cacahTamiu2 = CacahTamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $tamiu = Tamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->whereNull('tanggal_nonaktif')->count();



        $banjar_adat_id = $banjarAdat->id;


        $kelahiran = Kelahiran::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $kematian = Kematian::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();

        $perkawinan = Perkawinan::where('status_perkawinan', 3)
                                    ->where(function ($query) use ($banjar_adat_id) {
                                        $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                                            ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
                                    })->count();


        $perceraian = Perceraian::where('status_perceraian', 3)
                                    ->where(function ($query) use ($banjar_adat_id) {
                                        $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                                            ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
                                    })
                                    ->count();

        $maperas = Maperas::where('status_maperas', 3)
                                    ->where(function ($query) use ($banjar_adat_id) {
                                        $query->where('banjar_adat_lama_id', $banjar_adat_id)
                                            ->orWhere('banjar_adat_baru_id', $banjar_adat_id);
                                    })
                                    ->count();

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'kramaMipil' => $kramaMipil,
            'kramaTamiu' => $kramaTamiu,
            'cacahMipil' => $cacahMipil,
            'cacahTamiu' => $cacahTamiu,
            'banjarAdat' => $banjarAdat,
            'desaAdat' => $desaAdat,
            'kelahiran' => $kelahiran,
            'kematian' => $kematian,
            'perkawinan' => $perkawinan,
            'perceraian' => $perceraian,
            'maperas' => $maperas,
            'cacahTamiu2' => $cacahTamiu2,
            'tamiu' => $tamiu,
            'message' => 'dashboard berhasil'
        ], 200);
    }

    public function dashboardKramaAdminData() {
        $kramaMipil = KramaMipil::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $kramaTamiu = KramaTamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $cacahMipil = CacahKramaMipil::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $cacahKramaTamiu = CacahKramaTamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->whereNull('tanggal_keluar')->count();
        $cacahTamiu = CacahTamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->where('status', 1)->count();
        $tamiu = Tamiu::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->whereNull('tanggal_nonaktif')->count();


        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'kramaMipil' => $kramaMipil,
            'kramaTamiu' => $kramaTamiu,
            'cacahMipil' => $cacahMipil,
            'cacahKramaTamiu' => $cacahKramaTamiu,
            'cacahTamiu' => $cacahTamiu,
            'tamiu' => $tamiu,
            'message' => 'dashboard berhasil'
        ], 200);
    }
}
