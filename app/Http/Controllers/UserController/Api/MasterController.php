<?php

namespace App\Http\Controllers\UserController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendidikan;
use App\Models\Pekerjaan;
use App\Models\Tempekan;
use Auth;

class MasterController extends Controller
{
    public function getPendidikan() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Pendidikan::all(),
            'message' => 'pendidikan'
        ], 200);
    }

    public function getProfesi() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Pekerjaan::all(),
            'message' => 'profesi'
        ], 200);
    }

    public function getKabupaten() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Kabupaten::all(),
            'message' => 'profesi'
        ], 200);
    }

    public function getTempekan() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Tempekan::where('banjar_adat_id', Auth::user()->prajuru_banjar_adat->banjar_adat_id)->get(0),
            'message' => 'tempekan'
        ], 200);
    }


    public function getKecamatan(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Kecamatan::where('kabupaten_id', $req->query('kab'))->get(),
            'message' => 'profesi'
        ], 200);
    }

    public function getDesaAdat(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => DesaAdat::where('kecamatan_id', $req->query('kec'))->get(),
            'message' => 'profesi'
        ], 200);
    }

    public function getBanjarAdat(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => BanjarAdat::where('desa_adat_id', $req->query('desa_adat'))->get(),
            'message' => 'banjar adat'
        ], 200);
    }
}
