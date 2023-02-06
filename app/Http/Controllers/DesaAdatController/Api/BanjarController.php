<?php

namespace App\Http\Controllers\DesaAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use Auth;

class BanjarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getDinas() {
        $desa_adat_id = Auth::user()->admin_desa_adat->desa_adat_id;
        $banjarDinass = BanjarDinas::where('desa_adat_id', $desa_adat_id)->paginate(20);
        if ($banjarDinass != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $banjarDinass,
                'message' => 'data banjar dinas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data banjar dinas fail'
            ], 200);
        }
    }

    public function getAdat() {
        $desa_adat_id = Auth::user()->admin_desa_adat->desa_adat_id;
        $banjarAdats = BanjarAdat::where('desa_adat_id', $desa_adat_id)->paginate(20);
        if ($banjarAdats != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $banjarAdats,
                'message' => 'data banjar adat sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data banjar adat fail'
            ], 200);
        }
    }
}
