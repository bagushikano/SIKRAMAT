<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

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
use App\Models\Tempekan;
use Auth;

class CacahKramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get(Request $req) {
        $kramas = CacahKramaMipil::with('penduduk')
                                        ->whereHas('penduduk', function($q) use($req) {
                                            $q->like('nama', $req->query('nama'));
                                        })
                                        ->whereNull('tanggal_nonaktif')
                                        ->where('status', 1)
                                        ->where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                        ->paginate(10);
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
        $krama = CacahKramaMipil::with('penduduk', 'banjar_adat',
                                            'penduduk.pekerjaan',
                                            'penduduk.pendidikan',
                                            'banjar_dinas',
                                            'tempekan')->where('id', $krama_id)->first();
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

    public function getByTempekan(Request $req) {
        $kramas = CacahKramaMipil::with('penduduk')
                                        ->where('tempekan_id', $req->query('tempekan'))
                                        ->whereNull('tanggal_nonaktif')
                                        ->where('status', 1)
                                        ->where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                        ->get();
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

    public function getTempekanWithKrama(Request $req) {
        $tempekans = Tempekan::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                ->get()
                                ->map(function($item){
                                    $item->jumlah_krama =  CacahKramaMipil::where('tempekan_id', $item->id)
                                                                            ->whereNull('tanggal_nonaktif')
                                                                            ->where('status', 1)
                                                                            ->where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                                                            ->count();
                                    return $item;
                                });
        if ($tempekans != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $tempekans,
                'message' => 'data tempekan sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data tempekan fail'
            ], 200);
        }
    }

    public function getAllKrama(Request $req) {
         $kramas = CacahKramaMipil::with('penduduk')
                                        ->whereHas('penduduk', function($q) use($req) {
                                            $q->like('nama', $req->query('nama'));
                                        })
                                        ->whereNull('tanggal_nonaktif')
                                        ->where('status', 1)
                                        ->where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)
                                        ->get();
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
}
