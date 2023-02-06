<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PelaporanMutasiController extends Controller
{
    public function index(Request $req)
    {
        $data = [
            'banjar_adat_id' => $req->query('banjar_adat_id'),
            'desa_adat_id' => $req->query('desa_adat_id'),
        ];
        return view('pages.banjar.pelaporan.mobile.mutasi', compact('data'));
    }
}
