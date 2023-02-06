<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Penduduk;
use App\Models\PrajuruBanjarAdat;
use App\Models\KramaMipil;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get() {
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->where('id', Auth::user()->prajuru_banjar_adat->krama_mipil->id)->first();
        $prajuru = PrajuruBanjarAdat::where('user_id', Auth::user()->id)->first();
        if ($krama_mipil != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama_mipil,
                'jabatan' => $prajuru->jabatan,
                'message' => 'data prajuru sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data prajuru fail'
            ], 200);
        }
    }
}
