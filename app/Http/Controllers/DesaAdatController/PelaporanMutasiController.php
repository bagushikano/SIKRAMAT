<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Helper\Helper;
use Carbon\Carbon;
use App\Models\DesaAdat;
use App\Models\Tempekan;
use App\Models\DesaDinas;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use App\Services\TempekanService;
use App\Services\PekerjaanService;
use App\Services\KramaMipilService;
use App\Services\PendidikanService;
use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\Maperas;
use App\Models\Perceraian;
use App\Models\Perkawinan;
use App\Services\BanjarDinasService;
use App\Services\KelahiranService;
use App\Services\KematianService;
use App\Services\MaperasService;
use App\Services\PerceraianService;
use App\Services\PerkawinanService;
use DateInterval;
use DatePeriod;
use DateTime;

class PelaporanMutasiController extends Controller
{
    public function __construct
    (
        KelahiranService $serviceKelahiran,
        KematianService $serviceKematian,
        PerkawinanService $servicePerkawinan,
        PerceraianService $servicePerceraian,
        MaperasService $serviceMaperas
    )
    {
        $this->middleware('auth');

        $this->serviceKelahiran = $serviceKelahiran;
        $this->serviceKematian = $serviceKematian;
        $this->servicePerkawinan = $servicePerkawinan;
        $this->servicePerceraian = $servicePerceraian;
        $this->serviceMaperas = $serviceMaperas;
    }

    public function index()
    {        
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.pelaporan.mutasi.index', compact('banjar_adat'));
    }

    public function lapKelahiran(Request $request)
    {
        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();

        # Validasi Tanggal Lahir
        if ($request->tgl_lahir_awal != NULL && $request->tgl_lahir_akhir != NULL) {
            $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_awal));
            $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_akhir));

            if ($tgl_lahir_awal > $today) {
                return redirect()->back()->with('failed', 'Rentang awal tanggal lahir tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_lahir_akhir > $today) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal lahir tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_lahir_akhir < $tgl_lahir_awal) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal lahir tidak dapat lebih kecil dari rentang awal tanggal lahir');
            }
        } else {
            if ($request->tgl_lahir_awal) {
                $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_awal));
                if ($tgl_lahir_awal > $today) {
                    return redirect()->back()->with('failed', 'Rentang awal tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
    
            if ($request->tgl_lahir_akhir) {
                $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_akhir));
                if ($tgl_lahir_akhir > $today) {
                    return redirect()->back()->with('failed', 'Rentang akhir tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
        }

        # Get Banjar Data ID
        $banjar_adat_id = $request->banjar_adat_kelahiran;

        # Get range tanggal
        if ($request->tgl_lahir_awal) {
            $start = $month = strtotime($request->tgl_lahir_awal);
        }else{
            $start = $month = strtotime(Kelahiran::whereIn('banjar_adat_id', $banjar_adat_id)->min('tanggal_lahir'));
        }

        if($request->tgl_lahir_akhir){
            $end = strtotime($request->tgl_lahir_akhir);
        }else{
            $end = strtotime(Kelahiran::whereIn('banjar_adat_id', $banjar_adat_id)->max('tanggal_lahir'));
        }

        if($end < $start){
            return redirect()->back()->with('failed', 'Tanggal lahir tidak valid');
        }
        
        # Get Grafik Bulan Lahir
        $start    = new DateTime(date('Y-m-d', $start));
        $start->modify('first day of this month');
        $end      = new DateTime(date('Y-m-d', $end));
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $daftar_bulan[] = Carbon::parse($dt->format('Y-m'))->locale('id')->translatedFormat('F Y');
        }

        $grafik_bulan_laki = $this->serviceKelahiran->GetGrafikBulanLahir($banjar_adat_id, $request, 'laki-laki');
        $grafik_bulan_perempuan = $this->serviceKelahiran->GetGrafikBulanLahir($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Banjar
        foreach($banjar_adat_id as $banjar) {
            $daftar_banjar[] = BanjarAdat::find($banjar)->nama_banjar_adat;
        }

        $grafik_banjar_laki = $this->serviceKelahiran->GetGrafikBanjar($banjar_adat_id, $request, 'laki-laki');
        $grafik_banjar_perempuan = $this->serviceKelahiran->GetGrafikBanjar($banjar_adat_id, $request, 'perempuan');

        # Get Data Kelahiran
        $kelahiran = $this->serviceKelahiran->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'kelahiran' => $kelahiran,
            'daftar_bulan' => $daftar_bulan,
            'grafik_bulan_laki' => $grafik_bulan_laki,
            'grafik_bulan_perempuan' => $grafik_bulan_perempuan,
            'daftar_banjar' => $daftar_banjar,
            'grafik_banjar_laki' => $grafik_banjar_laki,
            'grafik_banjar_perempuan' => $grafik_banjar_perempuan
        ];

        return view('pages.desa.pelaporan.mutasi.laporan-kelahiran', compact('data'));
    }

    public function lapKematian(Request $request)
    {
        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();

        # Validasi Tanggal Mati
        if ($request->tgl_kematian_awal != NULL && $request->tgl_kematian_akhir != NULL) {
            $tgl_kematian_awal = date("Y-m-d", strtotime($request->tgl_kematian_awal));
            $tgl_kematian_akhir = date("Y-m-d", strtotime($request->tgl_kematian_akhir));

            if ($tgl_kematian_awal > $today) {
                return redirect()->back()->with(['failed' => 'Rentang awal tanggal kematian tidak dapat melebihi tanggal hari ini', 'tab' => 'kematian']);
            }

            if ($tgl_kematian_akhir > $today) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal kematian tidak dapat melebihi tanggal hari ini', 'tab' => 'kematian']);
            }

            if ($tgl_kematian_akhir < $tgl_kematian_awal) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal kematian tidak dapat lebih kecil dari rentang awal tanggal kematian', 'tab' => 'kematian']);
            }
        } else {
            if ($request->tgl_kematian_awal) {
                $tgl_kematian_awal = date("Y-m-d", strtotime($request->tgl_kematian_awal));
                if ($tgl_kematian_awal > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang awal tanggal kematian tidak dapat melebihi tanggal hari ini', 'tab' => 'kematian']);
                }
            }
    
            if ($request->tgl_kematian_akhir) {
                $tgl_kematian_akhir = date("Y-m-d", strtotime($request->tgl_kematian_akhir));
                if ($tgl_kematian_akhir > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang akhir tanggal kematian tidak dapat melebihi tanggal hari ini', 'tab' => 'kematian']);
                }
            }
        }

        # Get Banjar Data ID
        $banjar_adat_id = $request->banjar_adat_kematian;

        # Get range tanggal
        if ($request->tgl_kematian_awal) {
            $start = $month = strtotime($request->tgl_kematian_awal);
        }else{
            $start = $month = strtotime(Kematian::whereIn('banjar_adat_id', $banjar_adat_id)->min('tanggal_kematian'));
        }

        if($request->tgl_kematian_akhir){
            $end = strtotime($request->tgl_kematian_akhir);
        }else{
            $end = strtotime(Kematian::whereIn('banjar_adat_id', $banjar_adat_id)->max('tanggal_kematian'));
        }

        if($end < $start){
            return redirect()->back()->with(['failed' => 'Tanggal kematian tidak valid', 'tab' => 'kematian']);
        }
        
        # Get Grafik Bulan Mati
        $start    = new DateTime(date('Y-m-d', $start));
        $start->modify('first day of this month');
        $end      = new DateTime(date('Y-m-d', $end));
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $daftar_bulan[] = Carbon::parse($dt->format('Y-m'))->locale('id')->translatedFormat('F Y');
        }

        $grafik_bulan_laki = $this->serviceKematian->GetGrafikBulanMati($banjar_adat_id, $request, 'laki-laki');
        $grafik_bulan_perempuan = $this->serviceKematian->GetGrafikBulanMati($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Banjar
        foreach($banjar_adat_id as $banjar) {
            $daftar_banjar[] = BanjarAdat::find($banjar)->nama_banjar_adat;
        }

        $grafik_banjar_laki = $this->serviceKematian->GetGrafikBanjar($banjar_adat_id, $request, 'laki-laki');
        $grafik_banjar_perempuan = $this->serviceKematian->GetGrafikBanjar($banjar_adat_id, $request, 'perempuan');

        # Get Data Kematian
        $kematian = $this->serviceKematian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'kematian' => $kematian,
            'daftar_bulan' => $daftar_bulan,
            'grafik_bulan_laki' => $grafik_bulan_laki,
            'grafik_bulan_perempuan' => $grafik_bulan_perempuan,
            'daftar_banjar' => $daftar_banjar,
            'grafik_banjar_laki' => $grafik_banjar_laki,
            'grafik_banjar_perempuan' => $grafik_banjar_perempuan
        ];
        return view('pages.desa.pelaporan.mutasi.laporan-kematian', compact('data'));
    }

    public function lapPerkawinan(Request $request)
    {
        if (!$request->jenis_perkawinan) {
            return redirect()->back()->with(['failed' => 'Jenis perkawinan wajib dipilih', 'tab' => 'perkawinan']);
        }

        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();

        # Validasi Tanggal Mati
        if ($request->tgl_perkawinan_awal != NULL && $request->tgl_perkawinan_akhir != NULL) {
            $tgl_perkawinan_awal = date("Y-m-d", strtotime($request->tgl_perkawinan_awal));
            $tgl_perkawinan_akhir = date("Y-m-d", strtotime($request->tgl_perkawinan_akhir));

            if ($tgl_perkawinan_awal > $today) {
                return redirect()->back()->with(['failed' => 'Rentang awal tanggal perkawinan tidak dapat melebihi tanggal hari ini', 'tab' => 'perkawinan']);
            }

            if ($tgl_perkawinan_akhir > $today) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal perkawinan tidak dapat melebihi tanggal hari ini', 'tab' => 'perkawinan']);
            }

            if ($tgl_perkawinan_akhir < $tgl_perkawinan_awal) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal perkawinan tidak dapat lebih kecil dari rentang awal tanggal perkawinan', 'tab' => 'perkawinan']);
            }
        } else {
            if ($request->tgl_perkawinan_awal) {
                $tgl_perkawinan_awal = date("Y-m-d", strtotime($request->tgl_perkawinan_awal));
                if ($tgl_perkawinan_awal > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang awal tanggal perkawinan tidak dapat melebihi tanggal hari ini', 'tab' => 'perkawinan']);
                }
            }
    
            if ($request->tgl_perkawinan_akhir) {
                $tgl_perkawinan_akhir = date("Y-m-d", strtotime($request->tgl_perkawinan_akhir));
                if ($tgl_perkawinan_akhir > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang akhir tanggal perkawinan tidak dapat melebihi tanggal hari ini', 'tab' => 'perkawinan']);
                }
            }
        }

        # Get Banjar Data ID
        $banjar_adat_id = $request->banjar_adat_perkawinan;

        # Get range tanggal
        if ($request->tgl_perkawinan_awal) {
            $start = $month = strtotime($request->tgl_perkawinan_awal);
        }else{
            $start = $month = strtotime(Perkawinan::where(function ($query) use ($banjar_adat_id) {
                $query->whereIn('banjar_adat_purusa_id', $banjar_adat_id)
                    ->orWhereIn('banjar_adat_pradana_id', $banjar_adat_id);
            })->min('tanggal_perkawinan'));
        }

        if($request->tgl_perkawinan_akhir){
            $end = strtotime($request->tgl_perkawinan_akhir);
        }else{
            $end = strtotime(Perkawinan::where(function ($query) use ($banjar_adat_id) {
                $query->whereIn('banjar_adat_purusa_id', $banjar_adat_id)
                    ->orWhereIn('banjar_adat_pradana_id', $banjar_adat_id);
            })->max('tanggal_perkawinan'));
        }

        if($end < $start){
            return redirect()->back()->with(['failed' => 'Tanggal perkawinan tidak valid', 'tab' => 'perkawinan']);
        }
        
        # Get Grafik Bulan Kawin
        $start    = new DateTime(date('Y-m-d', $start));
        $start->modify('first day of this month');
        $end      = new DateTime(date('Y-m-d', $end));
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $daftar_bulan[] = Carbon::parse($dt->format('Y-m'))->locale('id')->translatedFormat('F Y');
        }

        $grafik_bulan = $this->servicePerkawinan->GetGrafikBulanKawin($banjar_adat_id, $request);

        # Get Grafik Jenis Kawin
        foreach($request->jenis_perkawinan as $jenis_perkawinan){
            $daftar_jenis[] = ucwords(str_replace('_', ' ', $jenis_perkawinan));
        }

        $grafik_jenis = $this->servicePerkawinan->GetGrafikJenisKawin($banjar_adat_id, $request);

        foreach($banjar_adat_id as $banjar) {
            $daftar_banjar[] = BanjarAdat::find($banjar)->nama_banjar_adat;
        }

        $grafik_banjar = $this->servicePerkawinan->GetGrafikBanjar($banjar_adat_id, $request);
        
        $perkawinan = $this->servicePerkawinan->getAllData($banjar_adat_id, $request)->get()->filter(function ($item) use ($request) {
            $banjar_adat_id = $request->banjar_adat_perkawinan;
            if($item->jenis_perkawinan == 'satu_banjar_adat'){
                $item->jenis = 'Satu Banjar Adat';
                return $item;
            }else if($item->jenis_perkawinan == 'beda_banjar_adat'){
                if(in_array($item->banjar_adat_purusa_id, $banjar_adat_id)){
                    $item->jenis = 'Beda Banjar Adat (Masuk)';
                    return $item;
                }else if(in_array($item->banjar_adat_pradana_id, $banjar_adat_id)){
                    if($item->status_perkawinan == '0' || $item->status_perkawinan == '1' || $item->status_perkawinan == '3'){
                        $item->jenis = 'Beda Banjar Adat (Keluar)';
                        return $item;
                    }
                }
            }else if($item->jenis_perkawinan == 'campuran_masuk'){
                $item->jenis = 'Campuran Masuk';
                return $item;
            }else if($item->jenis_perkawinan == 'campuran_keluar'){
                $item->jenis = 'Campuran Keluar';
                return $item;
            }
        });


        $data = [
            'perkawinan' => $perkawinan,
            'daftar_bulan' => $daftar_bulan,
            'grafik_bulan' => $grafik_bulan,
            'daftar_jenis' => $daftar_jenis,
            'grafik_jenis' => $grafik_jenis,
            'daftar_banjar' => $daftar_banjar,
            'grafik_banjar' => $grafik_banjar
        ];
        
        return view('pages.desa.pelaporan.mutasi.laporan-perkawinan', compact('data'));
    }

    public function lapPerceraian(Request $request)
    {
        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();

        # Validasi Tanggal Cerai
        if ($request->tgl_perceraian_awal != NULL && $request->tgl_perceraian_akhir != NULL) {
            $tgl_perceraian_awal = date("Y-m-d", strtotime($request->tgl_perceraian_awal));
            $tgl_perceraian_akhir = date("Y-m-d", strtotime($request->tgl_perceraian_akhir));

            if ($tgl_perceraian_awal > $today) {
                return redirect()->back()->with(['failed' => 'Rentang awal tanggal perceraian tidak dapat melebihi tanggal hari ini', 'tab' => 'perceraian']);
            }

            if ($tgl_perceraian_akhir > $today) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal perceraian tidak dapat melebihi tanggal hari ini', 'tab' => 'perceraian']);
            }

            if ($tgl_perceraian_akhir < $tgl_perceraian_awal) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal perceraian tidak dapat lebih kecil dari rentang awal tanggal perceraian', 'tab' => 'perceraian']);
            }
        } else {
            if ($request->tgl_perceraian_awal) {
                $tgl_perceraian_awal = date("Y-m-d", strtotime($request->tgl_perceraian_awal));
                if ($tgl_perceraian_awal > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang awal tanggal perceraian tidak dapat melebihi tanggal hari ini', 'tab' => 'perceraian']);
                }
            }
    
            if ($request->tgl_perceraian_akhir) {
                $tgl_perceraian_akhir = date("Y-m-d", strtotime($request->tgl_perceraian_akhir));
                if ($tgl_perceraian_akhir > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang akhir tanggal perceraian tidak dapat melebihi tanggal hari ini', 'tab' => 'perceraian']);
                }
            }
        }

        # Get Banjar Data ID
        $banjar_adat_id = $request->banjar_adat_perceraian;

        # Get range tanggal
        if ($request->tgl_perceraian_awal) {
            $start = $month = strtotime($request->tgl_perceraian_awal);
        }else{
            $start = $month = strtotime(Perceraian::whereIn('banjar_adat_purusa_id', $banjar_adat_id)->min('tanggal_perceraian'));
        }

        if($request->tgl_perceraian_akhir){
            $end = strtotime($request->tgl_perceraian_akhir);
        }else{
            $end = strtotime(Perceraian::whereIn('banjar_adat_purusa_id', $banjar_adat_id)->max('tanggal_perceraian'));
        }

        if($end < $start){
            return redirect()->back()->with(['failed' => 'Tanggal perceraian tidak valid', 'tab' => 'perceraian']);
        }
        
        # Get Grafik Bulan Cerai
        $start    = new DateTime(date('Y-m-d', $start));
        $start->modify('first day of this month');
        $end      = new DateTime(date('Y-m-d', $end));
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $daftar_bulan[] = Carbon::parse($dt->format('Y-m'))->locale('id')->translatedFormat('F Y');
        }

        $grafik_bulan = $this->servicePerceraian->GetGrafikBulanCerai($banjar_adat_id, $request);

        foreach($banjar_adat_id as $banjar) {
            $daftar_banjar[] = BanjarAdat::find($banjar)->nama_banjar_adat;
        }

        $grafik_banjar = $this->servicePerceraian->GetGrafikBanjar($banjar_adat_id, $request);

        $perceraian = $this->servicePerceraian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'perceraian' => $perceraian,
            'daftar_bulan' => $daftar_bulan,
            'grafik_bulan' => $grafik_bulan,
            'daftar_banjar' => $daftar_banjar,
            'grafik_banjar' => $grafik_banjar
        ];
        return view('pages.desa.pelaporan.mutasi.laporan-perceraian', compact('data'));
    }

    public function lapMaperas(Request $request)
    {
        if (!$request->jenis_maperas) {
            return redirect()->back()->with(['failed' => 'Jenis maperas wajib dipilih', 'tab' => 'maperas']);
        }

        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();

        # Validasi Tanggal Mati
        if ($request->tgl_maperas_awal != NULL && $request->tgl_maperas_akhir != NULL) {
            $tgl_maperas_awal = date("Y-m-d", strtotime($request->tgl_maperas_awal));
            $tgl_maperas_akhir = date("Y-m-d", strtotime($request->tgl_maperas_akhir));

            if ($tgl_maperas_awal > $today) {
                return redirect()->back()->with(['failed' => 'Rentang awal tanggal maperas tidak dapat melebihi tanggal hari ini', 'tab' => 'maperas']);
            }

            if ($tgl_maperas_akhir > $today) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal maperas tidak dapat melebihi tanggal hari ini', 'tab' => 'maperas']);
            }

            if ($tgl_maperas_akhir < $tgl_maperas_awal) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal maperas tidak dapat lebih kecil dari rentang awal tanggal maperas', 'tab' => 'maperas']);
            }
        } else {
            if ($request->tgl_maperas_awal) {
                $tgl_maperas_awal = date("Y-m-d", strtotime($request->tgl_maperas_awal));
                if ($tgl_maperas_awal > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang awal tanggal maperas tidak dapat melebihi tanggal hari ini', 'tab' => 'maperas']);
                }
            }
    
            if ($request->tgl_maperas_akhir) {
                $tgl_maperas_akhir = date("Y-m-d", strtotime($request->tgl_maperas_akhir));
                if ($tgl_maperas_akhir > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang akhir tanggal maperas tidak dapat melebihi tanggal hari ini', 'tab' => 'maperas']);
                }
            }
        }

        # Get Banjar Data ID
        $banjar_adat_id = $request->banjar_adat_maperas;

        # Get range tanggal
        if ($request->tgl_maperas_awal) {
            $start = $month = strtotime($request->tgl_maperas_awal);
        }else{
            $start = $month = strtotime(Maperas::where('status_maperas', '3')->where(function ($query) use ($banjar_adat_id) {
                $query->whereIn('banjar_adat_lama_id', $banjar_adat_id)
                    ->orWhereIn('banjar_adat_baru_id', $banjar_adat_id);
            })->min('tanggal_maperas'));
        }

        if($request->tgl_maperas_akhir){
            $end = strtotime($request->tgl_maperas_akhir);
        }else{
            $end = strtotime(Maperas::where('status_maperas', '3')->where(function ($query) use ($banjar_adat_id) {
                $query->whereIn('banjar_adat_lama_id', $banjar_adat_id)
                    ->orWhereIn('banjar_adat_baru_id', $banjar_adat_id);
            })->max('tanggal_maperas'));
        }

        if($end < $start){
            return redirect()->back()->with(['failed' => 'Tanggal maperas tidak valid', 'tab' => 'maperas']);
        }
        
        # Get Grafik Bulan Maperas
        $start    = new DateTime(date('Y-m-d', $start));
        $start->modify('first day of this month');
        $end      = new DateTime(date('Y-m-d', $end));
        $end->modify('first day of next month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $daftar_bulan[] = Carbon::parse($dt->format('Y-m'))->locale('id')->translatedFormat('F Y');
        }

        $grafik_bulan = $this->serviceMaperas->GetGrafikBulanMaperas($banjar_adat_id, $request);

        # Get Grafik Jenis Maperas
        foreach($request->jenis_maperas as $jenis_maperas){
            $daftar_jenis[] = ucwords(str_replace('_', ' ', $jenis_maperas));
        }

        $grafik_jenis = $this->serviceMaperas->GetGrafikJenisMaperas($banjar_adat_id, $request);

        foreach($banjar_adat_id as $banjar) {
            $daftar_banjar[] = BanjarAdat::find($banjar)->nama_banjar_adat;
        }

        $grafik_banjar = $this->serviceMaperas->GetGrafikBanjar($banjar_adat_id, $request);

        $maperas = $this->serviceMaperas->getAllData($banjar_adat_id, $request)->get()->filter(function ($item) use ($banjar_adat_id) {
            if($item->jenis_maperas == 'satu_banjar_adat'){
                $item->jenis = 'Satu Banjar Adat';
                return $item;
            }else if($item->jenis_maperas == 'beda_banjar_adat'){
                if(in_array($item->banjar_adat_baru_id, $banjar_adat_id)){
                    $item->jenis = 'Beda Banjar Adat (Masuk)';
                    return $item;
                }else if(in_array($item->banjar_adat_lama_id, $banjar_adat_id)){
                    if($item->status_maperas == '0' || $item->status_maperas == '1' || $item->status_maperas == '3'){
                        $item->jenis = 'Beda Banjar Adat (Keluar)';
                        return $item;
                    }
                }
            }else if($item->jenis_maperas == 'campuran_masuk'){
                $item->jenis = 'Campuran Masuk';
                return $item;
            }else if($item->jenis_maperas == 'campuran_keluar'){
                $item->jenis = 'Campuran Keluar';
                return $item;
            }

        });

        $data = [
            'maperas' => $maperas,
            'daftar_bulan' => $daftar_bulan,
            'grafik_bulan' => $grafik_bulan,
            'daftar_jenis' => $daftar_jenis,
            'grafik_jenis' => $grafik_jenis,
            'daftar_banjar' => $daftar_banjar,
            'grafik_banjar' => $grafik_banjar
        ];
        
        return view('pages.desa.pelaporan.mutasi.laporan-maperas', compact('data'));
    }
}