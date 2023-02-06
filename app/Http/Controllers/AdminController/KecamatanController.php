<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\Validator;

class KecamatanController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
        $kecamatans = Kecamatan::with('kabupaten')->get();
        return view('pages.admin.m_kecamatan.m_kecamatan', compact('kecamatans'));
    }

    public function get($id){
        $kecamatans = Kecamatan::where('kabupaten_id', $id)->get();
        return response()->json([
            $kecamatans
        ]);
    }

    public function show($id)
    {
        $kecamatan = Kecamatan::with('kabupaten.provinsi')->find($id);
        return response()->json(['success' => 'Berhasil', 'kecamatan' => $kecamatan]);
    }
}
