<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SuperAdmin;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\Pendidikan;
use App\Models\Pekerjaan;
use App\Models\Provinsi;
use Illuminate\Support\Facades\Validator;

class PendudukController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get($nik){
        $penduduk = Penduduk::where('nik', $nik)->first();
        if($penduduk){
            $status = true;
            $desa = DesaDinas::find($penduduk->desa_id);
            $kecamatan = Kecamatan::where('id', $desa->kecamatan_id)->first();
            $kabupaten = Kabupaten::where('id', $kecamatan->kabupaten_id)->first();
            $provinsi = Provinsi::where('id', $kabupaten->provinsi_id)->first();
            
            //Data Master
            $provinsis = Provinsi::get();
            $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
            $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
            $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();
            $pekerjaans = Pekerjaan::get();
            $pendidikans = Pendidikan::get();
            return response()->json([
                'status' => $status,
                'penduduk' => $penduduk,
                'desa' => $desa,
                'kecamatan' => $kecamatan,
                'kabupaten' => $kabupaten,
                'provinsi' => $provinsi,
                'desas' => $desas,
                'kecamatans' => $kecamatans,
                'kabupatens' => $kabupatens,
                'provinsis' => $provinsis,
                'pendidikans' => $pendidikans,
                'pekerjaans' => $pekerjaans
            ]);
        }else{
            $status = false;
            return response()->json([
                'status' => $status
            ]);
        }
    }
}
