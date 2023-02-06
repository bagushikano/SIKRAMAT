<?php

namespace App\Http\Controllers\UserController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\DesaAdat;
use App\Models\BanjarAdat;
use App\Models\Provinsi;
use App\Models\DesaDinas;


class PublicController extends Controller
{
    public function getPekerjaan() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Pekerjaan::get(),
            'message' => 'pekerjaan berhasil'
        ], 200);
    }

    public function getPendidikan() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Pendidikan::get(),
            'message' => 'pendidikan berhasil'
        ], 200);
    }

    public function getKabupaten() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Kabupaten::where('provinsi_id', 51)->get(),
            'message' => 'kabupaten'
        ], 200);
    }

    public function getKecamatan(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Kecamatan::where('kabupaten_id', $req->query('kab'))->get(),
            'message' => 'kecamatan'
        ], 200);
    }

    public function getDesaAdat(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => DesaAdat::where('kecamatan_id', $req->query('kec'))->get(),
            'message' => 'desa adat'
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

    public function getDesaDinas(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => DesaDinas::where("kecamatan_id", $req->query("kec"))->get(),
            'message' => 'desa_dinas'
        ], 200);
    }


    public function getKabupatenProvinsi(Request $req) {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Kabupaten::where("provinsi_id", $req->query("prov"))->get(),
            'message' => 'kabupaten'
        ], 200);
    }

    public function getProvinsi() {
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => Provinsi::get(),
            'message' => 'provinsi'
        ], 200);
    }
}
