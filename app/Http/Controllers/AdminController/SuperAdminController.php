<?php

namespace App\Http\Controllers\AdminController;

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

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = SuperAdmin::with('user', 'penduduk')->get();
        return view('pages.admin.m_akun_super_admin.m_akun_super_admin', compact('users'));
    }

    public function create()
    {
        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        return view('pages.admin.m_akun_super_admin.create', compact('pekerjaans', 'pendidikans', 'provinsis'));
    }

    public function get_penduduk($nik){
        $penduduk = Penduduk::where('nik', $nik)->first();
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::where('id', $desa->kecamatan_id)->first();
        $kabupaten = Kabupaten::where('id', $kecamatan->kabupaten_id)->first();
        $provinsi = Provinsi::where('id', $kabupaten->provinsi_id)->first();
        return response()->json([
            'penduduk' => $penduduk,
            'desa' => $desa,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
            'provinsi' => $provinsi
        ]);
    }

    public function show($id)
    {
        $kecamatan = Kecamatan::with('kabupaten.provinsi')->find($id);
        return response()->json(['success' => 'Berhasil', 'kecamatan' => $kecamatan]);
    }
}
