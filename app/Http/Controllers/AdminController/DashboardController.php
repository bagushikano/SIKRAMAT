<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\CacahTamiu;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\KramaMipil;
use App\Models\KramaTamiu;
use App\Models\Maperas;
use App\Models\Perceraian;
use App\Models\Perkawinan;
use App\Models\Tamiu;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        //MASTER
        $curr_month = Carbon::now()->format('m');
        $curr_year = Carbon::now()->year;

        //CACAH KRAMA MIPIL
        $jumlah_cacah_krama_mipil = CacahKramaMipil::where('status', '1')->count();
        $jumlah_cacah_krama_mipil_nambah = CacahKramaMipil::where('status', '1')->whereMonth('created_at', $curr_month)->whereYear('created_at', $curr_year)->count();

        //CACAH KRAMA TAMIU
        $jumlah_cacah_krama_tamiu = CacahKramaTamiu::where('tanggal_keluar', NULL)->count();
        $jumlah_cacah_krama_tamiu_nambah = CacahKramaTamiu::where('tanggal_keluar', NULL)->whereMonth('tanggal_masuk', $curr_month)->whereYear('tanggal_masuk', $curr_year)->count();

        //CACAH TAMIU
        $jumlah_cacah_tamiu = CacahTamiu::where('tanggal_keluar', NULL)->count();
        $jumlah_cacah_tamiu_nambah = CacahTamiu::where('tanggal_keluar', NULL)->whereMonth('tanggal_masuk', $curr_month)->whereYear('tanggal_masuk', $curr_year)->count();

        //KRAMA MIPIL
        $jumlah_krama_mipil = KramaMipil::where('status', '1')->count();
        $jumlah_krama_mipil_nambah = KramaMipil::where('status', '1')->whereMonth('tanggal_registrasi', $curr_month)->whereYear('tanggal_registrasi', $curr_year)->count();
        
        //KRAMA TAMIU
        $jumlah_krama_tamiu = KramaTamiu::where('status', '1')->count();
        $jumlah_krama_tamiu_nambah = KramaTamiu::where('status', '1')->whereMonth('tanggal_registrasi', $curr_month)->whereYear('tanggal_registrasi', $curr_year)->count();

        //TAMIU
        $jumlah_tamiu = Tamiu::where('status', '1')->count();
        $jumlah_tamiu_nambah = Tamiu::where('status', '1')->whereMonth('tanggal_registrasi', $curr_month)->whereYear('tanggal_registrasi', $curr_year)->count();

        //KELAHIRAN
        $jumlah_kelahiran = Kelahiran::where('status', '1')->count();
        $jumlah_kelahiran_nambah = Kelahiran::where('status', '1')->whereMonth('tanggal_lahir', $curr_month)->whereYear('tanggal_lahir', $curr_year)->count();

        //KEMATIAN
        $jumlah_kematian = Kematian::where('status', '1')->count();
        $jumlah_kematian_nambah = Kematian::where('status', '1')->whereMonth('tanggal_kematian', $curr_month)->whereYear('tanggal_kematian', $curr_year)->count();

        //PERKAWINAN
        $jumlah_perkawinan = Perkawinan::where('status_perkawinan', '3')->count();
        $jumlah_perkawinan_nambah = Perkawinan::where('status_perkawinan', '3')->whereMonth('tanggal_perkawinan', $curr_month)->whereYear('tanggal_perkawinan', $curr_year)->count();

        //PERCERAIAN
        $jumlah_perceraian = Perceraian::where('status_perceraian', '3')->count();
        $jumlah_perceraian_nambah = Perceraian::where('status_perceraian', '3')->whereMonth('tanggal_perceraian', $curr_month)->whereYear('tanggal_perceraian', $curr_year)->count();

        //MAPERAS
        $jumlah_maperas = Maperas::where('status_maperas', '3')->count();
        $jumlah_maperas_nambah = Maperas::where('status_maperas', '3')->whereMonth('tanggal_maperas', $curr_month)->whereYear('tanggal_maperas', $curr_year)->count();

        //RETURN DASHBOARD
        return view('pages.admin.dashboard', compact(
            'jumlah_cacah_krama_mipil', 'jumlah_cacah_krama_mipil_nambah',
            'jumlah_cacah_krama_tamiu', 'jumlah_cacah_krama_tamiu_nambah',
            'jumlah_cacah_tamiu', 'jumlah_cacah_tamiu_nambah',
            'jumlah_krama_mipil', 'jumlah_krama_mipil_nambah',
            'jumlah_krama_tamiu', 'jumlah_krama_tamiu_nambah',
            'jumlah_tamiu', 'jumlah_tamiu_nambah',
            'jumlah_kelahiran', 'jumlah_kelahiran_nambah',
            'jumlah_kematian', 'jumlah_kematian_nambah',
            'jumlah_perkawinan', 'jumlah_perkawinan_nambah',
            'jumlah_perceraian', 'jumlah_perceraian_nambah',
            'jumlah_maperas', 'jumlah_maperas_nambah'
        ));
    }
}