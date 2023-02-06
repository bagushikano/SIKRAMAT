<?php

namespace App\Http\Controllers\UserController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function changeFoto(Request $req) {
        $penduduk = Penduduk::where('id', Auth::user()->user->penduduk_id)->first();

        if($req->hasFile('foto')){
            $file = $req->file('foto');
            $filename = uniqid().'.png';
            $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
            $path = $fileLocation."/".$filename;
            $penduduk->foto = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        $penduduk->save();

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $penduduk->foto,
            'message' => 'update data foto berhasil'
        ], 200);
    }

    public function editProfile(Request $req) {
        $penduduk = Penduduk::where('id', Auth::user()->user->penduduk_id)->first();
        $penduduk->nama = $req->nama;
        $penduduk->gelar_depan = $req->gelar_depan;
        $penduduk->gelar_belakang = $req->gelar_belakang;
        $penduduk->nama_alias = $req->nama_alias;
        $penduduk->telepon = $req->telepon;
        $penduduk->alamat = $req->alamat;
        $penduduk->koordinat_alamat = $req->koordinat_alamat;
        $penduduk->save();

        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => $penduduk,
            'message' => 'update profile berhasil'
        ], 200);
    }
}
