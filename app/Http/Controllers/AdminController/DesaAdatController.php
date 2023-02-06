<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DesaAdat;
use Illuminate\Support\Facades\Validator;

class DesaAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get($id){
        $desa_adats = DesaAdat::where('kecamatan_id', $id)->get();
        return response()->json([
            'desa_adats' => $desa_adats
        ]);
    }

    public function get_banjar($id){
        $banjar_adats = BanjarAdat::where('desa_adat_id', $id)->get();
        return response()->json([
            'banjar_adats' => $banjar_adats
        ]);
    }
}
