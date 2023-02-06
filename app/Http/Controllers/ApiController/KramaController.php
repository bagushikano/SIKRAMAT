<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Models\KramaMipil;
use App\Models\CacahKramaMipil;
use App\Models\KramaTamiu;
use App\Models\Tamiu;
use App\Models\Penduduk;

class KramaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getKramaMipil(Request $req) {
        $krama_mipil = KramaMipil::with('anggota.cacah_krama_mipil.penduduk', 'banjar_adat')
                                    ->where('nomor_krama_mipil', $req->query('no_krama_mipil'))
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        if ($krama_mipil != null) {
            if ($krama_mipil->status != 0) {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => $krama_mipil,
                    'message' => 'data krama mipil sukses'
                ], 200);
            }
            else {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => null,
                    'message' => 'krama mipil nonaktif'
                ], 200);
            }
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'not found'
            ], 200);
        }
    }

    public function getCacahKramaMipil(Request $req) {
        $krama_mipil = CacahKramaMipil::with('penduduk', 'banjar_adat', 'tempekan')
                                    ->where('nomor_cacah_krama_mipil', $req->query('no_cacah_krama'))
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        if ($krama_mipil != null) {
            if ($krama_mipil->status != 0) {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => $krama_mipil,
                    'message' => 'data cacah krama mipil sukses'
                ], 200);
            }
            else {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => null,
                    'message' => 'cacah krama mipil nonaktif'
                ], 200);
            }
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'not found'
            ], 200);
        }
    }

    public function searchCacahKramaMipil(Request $req) {
        $penduduk_id = Penduduk::like('nama', $req->query('nama'))->pluck('id')->toArray();
        $krama_mipil = CacahKramaMipil::with('penduduk', 'banjar_adat', 'tempekan')
                                    ->whereIn('penduduk_id', $penduduk_id)
                                    ->where('status' , 1)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        if ($krama_mipil != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama_mipil,
                'message' => 'data cacah krama mipil sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'not found'
            ], 200);
        }
    }

    public function getKramaTamiu(Request $req) {
        $krama_tamiu = KramaTamiu::with('anggota.cacah_krama_tamiu.penduduk', 'banjar_adat')
                                   ->where('nomor_krama_tamiu', $req->query('no_krama_tamiu'))
                                   ->orderBy('created_at', 'desc')
                                   ->first();
       if ($krama_tamiu != null) {
           if ($krama_tamiu->status != 0) {
               return response()->json([
                   'statusCode' => 200,
                   'status' => true,
                   'data' => $krama_tamiu,
                   'message' => 'data krama tamiu sukses'
               ], 200);
           }
           else {
               return response()->json([
                   'statusCode' => 200,
                   'status' => true,
                   'data' => null,
                   'message' => 'krama tamiu nonaktif'
               ], 200);
           }
       }
       else {
           return response()->json([
               'statusCode' => 500,
               'status' => false,
               'data' => null,
               'message' => 'not found'
           ], 200);
       }
   }


   public function returnCacahkrama(Request $req) {
        $data = json_decode($req->data);
        foreach($data as $krama) {
            $cacahKramaMipil = CacahKramaMipil::with('penduduk', 'tempekan')
                                        ->where('nomor_cacah_krama_mipil', $krama->nomor_cacah_krama_mipil)
                                        ->where('status', 1)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
            $krama->cacah_krama_mipil = $cacahKramaMipil;
        }
        if ($data != null) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $data,
                'message' => 'data cacah krama mipil sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'not found'
            ], 200);
        }
    }
}
