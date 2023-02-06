<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DesaDinas;
use Illuminate\Support\Facades\Validator;

class DesaDinasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $desas = DesaDinas::with('kecamatan')->get();
        return view('pages.admin.m_desa_dinas.m_desa_dinas', compact('desas'));
    }

    public function get($id){
        $desas = DesaDinas::where('kecamatan_id', $id)->get();
        return response()->json([
            $desas
        ]);
    }

    public function show($id)
    {
        $kecamatan = Kecamatan::with('kabupaten.provinsi')->find($id);
        return response()->json(['success' => 'Berhasil', 'kecamatan' => $kecamatan]);
    }
}
