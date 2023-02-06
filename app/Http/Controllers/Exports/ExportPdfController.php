<?php

namespace App\Http\Controllers\Exports;

use Carbon\Carbon;
use App\Models\Tamiu;
use App\Helper\Helper;
use App\Models\Maperas;
use App\Models\DesaAdat;
use App\Models\Kematian;
use PHPUnit\TextUI\Help;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelahiran;
use App\Models\BanjarAdat;
use App\Models\KramaMipil;
use App\Models\KramaTamiu;
use App\Models\Perceraian;
use App\Models\Perkawinan;
use Illuminate\Http\Request;
use App\Services\TamiuService;
use App\Services\MaperasService;
use App\Services\KematianService;
use App\Services\KelahiranService;
use Barryvdh\DomPDF\Facade as PDF;
use App\Services\CacahTamiuService;
use App\Services\KramaMipilService;
use App\Services\KramaTamiuService;
use App\Services\PerceraianService;
use App\Services\PerkawinanService;
use App\Http\Controllers\Controller;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\CacahTamiu;
use App\Services\CacahKramaMipilService;
use App\Services\CacahKramaTamiuService;

class ExportPdfController extends Controller
{
    public function __construct
    (
      KramaMipilService $serviceKramaMipil,
      KramaTamiuService $serviceKramaTamiu,
      TamiuService $serviceTamiu,
      CacahKramaMipilService $serviceCacahKramaMipil,
      CacahTamiuService $serviceCacahTamiu,
      CacahKramaTamiuService $serviceCacahKramaTamiu,
      KelahiranService $serviceKelahiran,
      KematianService $serviceKematian,
      PerkawinanService $servicePerkawinan,
      PerceraianService $servicePerceraian,
      MaperasService $serviceMaperas
    )
    {
        $this->middleware('auth');
        $this->serviceKramaMipil = $serviceKramaMipil;
        $this->serviceKramaTamiu = $serviceKramaTamiu;
        $this->serviceTamiu = $serviceTamiu;
        $this->serviceCacahKramaMipil = $serviceCacahKramaMipil;
        $this->serviceCacahKramaTamiu = $serviceCacahKramaTamiu;
        $this->serviceCacahTamiu = $serviceCacahTamiu;
        $this->serviceKelahiran = $serviceKelahiran;
        $this->serviceKematian = $serviceKematian;
        $this->servicePerkawinan = $servicePerkawinan;
        $this->servicePerceraian = $servicePerceraian;
        $this->serviceMaperas = $serviceMaperas;
    }

    public function lapKramaMipil(Request $request)
    {
        if ($request->banjar_adat_mipil) {
            if (!$request->banjar_adat_mipil) {
                return redirect()->back()->with('failed', 'Banjar adat wajib dipilih');
            }
        } else{
            if (!$request->tempekan) {
                return redirect()->back()->with('failed', 'Tempekan wajib dipilih');
            }
        }
        if (!$request->pekerjaan_mipil) {
            return redirect()->back()->with('failed', 'Pekerjaan krama mipil wajib dipilih');
        }
        if (!$request->pendidikan_mipil) {
            return redirect()->back()->with('failed', 'Pendidikan krama mipil wajib dipilih');
        }
        if (!$request->goldar_mipil) {
            return redirect()->back()->with('failed', 'Golongan darah krama mipil wajib dipilih');
        }
        if (!$request->status_mipil) {
            return redirect()->back()->with('failed', 'Status krama mipil wajib dipilih');
        }

        $banjar_adat_id = session()->get('banjar_adat_id');

        $data = [
            'krama_mipil' => $this->serviceKramaMipil->getAllData($banjar_adat_id, $request)
        ];

        if ($request->banjar_adat_mipil) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Krama Mipil.pdf');
    }

    public function lapKramaTamiu(Request $request)
    {
        if ($request->banjar_adat_tamiu) {
            if (!$request->banjar_adat_tamiu) {
                return redirect()->back()->with('failed', 'Banjar adat wajib dipilih');
            }
        }
        if (!$request->banjar_dinas_tamiu) {
            return redirect()->back()->with('failed', 'Banjar dinas krama tamiu wajib dipilih');
        }
        if (!$request->pekerjaan_tamiu) {
            return redirect()->back()->with('failed', 'Pekerjaan krama tamiu wajib dipilih');
        }
        if (!$request->pendidikan_tamiu) {
            return redirect()->back()->with('failed', 'Pendidikan krama tamiu wajib dipilih');
        }
        if (!$request->goldar_tamiu) {
            return redirect()->back()->with('failed', 'Golongan darah krama tamiu wajib dipilih');
        }
        if (!$request->status_tamiu) {
            return redirect()->back()->with('failed', 'Status krama tamiu wajib dipilih');
        }


        $banjar_adat_id = session()->get('banjar_adat_id');

        $data = [
            'krama_tamiu' => $this->serviceKramaTamiu->getAllData($banjar_adat_id, $request)
        ];

        if ($request->banjar_adat_tamiu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }
        return $pdf->download('Laporan Data Krama Tamiu.pdf');
    }

    public function lapTamiu(Request $request)
    {
        if ($request->banjar_adat_tamu) {
            if (!$request->banjar_adat_tamu) {
                return redirect()->back()->with('failed', 'Banjar adat wajib dipilih');
            }
        }
        if (!$request->banjar_dinas_tamu) {
            return redirect()->back()->with('failed', 'Banjar dinas tamiu wajib dipilih');
        }
        if (!$request->pekerjaan_tamu) {
            return redirect()->back()->with('failed', 'Pekerjaan tamiu wajib dipilih');
        }
        if (!$request->pendidikan_tamu) {
            return redirect()->back()->with('failed', 'Pendidikan tamiu wajib dipilih');
        }
        if (!$request->goldar_tamu) {
            return redirect()->back()->with('failed', 'Golongan darah tamiu wajib dipilih');
        }
        if (!$request->status_tamu) {
            return redirect()->back()->with('failed', 'Status tamiu wajib dipilih');
        }


        $banjar_adat_id = session()->get('banjar_adat_id');

        $data = [
            'tamiu' => $this->serviceTamiu->getAllData($banjar_adat_id, $request)
        ];

        if ($request->banjar_adat_tamu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }
        return $pdf->download('Laporan Data Tamiu.pdf');
    }

    public function lapCacahKramaMipil(Request $request)
    {
        if ($request->banjar_adat_mipil) {
            if (!$request->banjar_adat_mipil) {
                return redirect()->back()->with('failed', 'Banjar adat wajib dipilih');
            }
        } else {
            if (!$request->tempekan) {
                return redirect()->back()->with('failed', 'Tempekan wajib dipilih');
            }
        }
        if (!$request->pekerjaan_mipil) {
            return redirect()->back()->with('failed', 'Pekerjaan cacah krama mipil wajib dipilih');
        }
        if (!$request->pendidikan_mipil) {
            return redirect()->back()->with('failed', 'Pendidikan cacah krama mipil wajib dipilih');
        }
        if (!$request->goldar_mipil) {
            return redirect()->back()->with('failed', 'Golongan darah cacah krama mipil wajib dipilih');
        }
        if (!$request->status_mipil) {
            return redirect()->back()->with('failed', 'Status cacah krama mipil wajib dipilih');
        }

        $banjar_adat_id = session()->get('banjar_adat_id');

        $data = [
            'krama_mipil' => $this->serviceCacahKramaMipil->getAllData($banjar_adat_id, $request)
        ];

        if ($request->banjar_adat_mipil) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-cacah-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-cacah-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Cacah Krama Mipil.pdf');
    }

    public function lapCacahKramaTamiu(Request $request)
    {
        if ($request->banjar_adat_tamiu) {
            if (!$request->banjar_adat_tamiu) {
                return redirect()->back()->with('failed', 'Banjar adat wajib dipilih');
            }
        }
        if (!$request->pekerjaan_tamiu) {
            return redirect()->back()->with('failed', 'Pekerjaan cacah krama tamiu wajib dipilih');
        }
        if (!$request->pendidikan_tamiu) {
            return redirect()->back()->with('failed', 'Pendidikan cacah krama tamiu wajib dipilih');
        }
        if (!$request->goldar_tamiu) {
            return redirect()->back()->with('failed', 'Golongan darah cacah krama tamiu wajib dipilih');
        }
        if (!$request->status_tamiu) {
            return redirect()->back()->with('failed', 'Status cacah krama tamiu wajib dipilih');
        }


        $banjar_adat_id = session()->get('banjar_adat_id');

        $data = [
            'krama_tamiu' => $this->serviceCacahKramaTamiu->getAllData($banjar_adat_id, $request)
        ];

        if ($request->banjar_adat_tamiu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-cacah-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-cacah-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Cacah Krama Tamiu.pdf');
    }

    public function lapCacahTamiu(Request $request)
    {
        if ($request->banjar_adat_tamu) {
            if (!$request->banjar_adat_tamu) {
                return redirect()->back()->with('failed', 'Banjar adat wajib dipilih');
            }
        }
        if (!$request->pekerjaan_tamu) {
            return redirect()->back()->with('failed', 'Pekerjaan tamiu wajib dipilih');
        }
        if (!$request->pendidikan_tamu) {
            return redirect()->back()->with('failed', 'Pendidikan tamiu wajib dipilih');
        }
        if (!$request->goldar_tamu) {
            return redirect()->back()->with('failed', 'Golongan darah tamiu wajib dipilih');
        }
        if (!$request->status_tamu) {
            return redirect()->back()->with('failed', 'Status tamiu wajib dipilih');
        }


        $banjar_adat_id = session()->get('banjar_adat_id');

        $data = [
            'tamiu' => $this->serviceCacahTamiu->getAllData($banjar_adat_id, $request)
        ];

        if ($request->banjar_adat_tamu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-cacah-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-cacah-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Cacah Tamiu.pdf');
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
                return redirect()->back()->with('failed', 'Rentang akhir tanggal lahir tidak dapat melebihi rentang awal tanggal lahir');
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
        $banjar_adat_id = session()->get('banjar_adat_id');

        # Get Data Kelahiran
        $kelahiran = $this->serviceKelahiran->getAllData($banjar_adat_id, $request)->get();
        // foreach($kelahiran as $item){
        //     $item->tanggal_lahir = Helper::convert_date_to_locale_id($item->tanggal_lahir);
        // }

        $data = [
            'kelahiran' => $kelahiran
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-kelahiran', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Kelahiran.pdf');
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
        $banjar_adat_id = session()->get('banjar_adat_id');

        $kematian = $this->serviceKematian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'kematian' => $kematian,
        ];
        $pdf = PDF::loadView('vendor.export.pdf.laporan-kematian', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Kematian.pdf');
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
        $banjar_adat_id = session()->get('banjar_adat_id');

        # Get range tanggal
        if ($request->tgl_perkawinan_awal) {
            $start = $month = strtotime($request->tgl_perkawinan_awal);
        }else{
            $start = $month = strtotime(Perkawinan::where(function ($query) use ($banjar_adat_id) {
                $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                    ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
            })->min('tanggal_perkawinan'));
        }

        if($request->tgl_perkawinan_akhir){
            $end = strtotime($request->tgl_perkawinan_akhir);
        }else{
            $end = strtotime(Perkawinan::where(function ($query) use ($banjar_adat_id) {
                $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                    ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
            })->max('tanggal_perkawinan'));
        }

        $perkawinan = $this->servicePerkawinan->getAllData($banjar_adat_id, $request)->get()->filter(function ($item) {
            $banjar_adat_id = session()->get('banjar_adat_id');
            if($item->jenis_perkawinan == 'satu_banjar_adat'){
                $item->jenis = 'Satu Banjar Adat';
                return $item;
            }else if($item->jenis_perkawinan == 'beda_banjar_adat'){
                if($item->banjar_adat_purusa_id == $banjar_adat_id){
                    $item->jenis = 'Beda Banjar Adat (Masuk)';
                    return $item;
                }else if($item->banjar_adat_pradana_id == $banjar_adat_id){
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
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-perkawinan', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Perkawinan.pdf');
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
        $banjar_adat_id = session()->get('banjar_adat_id');

        $perceraian = $this->servicePerceraian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'perceraian' => $perceraian,
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-perceraian', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Perceraian.pdf');
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
        $banjar_adat_id = session()->get('banjar_adat_id');

        # Get range tanggal
        if ($request->tgl_maperas_awal) {
            $start = $month = strtotime($request->tgl_maperas_awal);
        }else{
            $start = $month = strtotime(Maperas::where('status_maperas', '3')->where(function ($query) use ($banjar_adat_id) {
                $query->where('banjar_adat_lama_id', $banjar_adat_id)
                    ->orWhere('banjar_adat_baru_id', $banjar_adat_id);
            })->min('tanggal_maperas'));
        }

        if($request->tgl_maperas_akhir){
            $end = strtotime($request->tgl_maperas_akhir);
        }else{
            $end = strtotime(Maperas::where('status_maperas', '3')->where(function ($query) use ($banjar_adat_id) {
                $query->where('banjar_adat_lama_id', $banjar_adat_id)
                    ->orWhere('banjar_adat_baru_id', $banjar_adat_id);
            })->max('tanggal_maperas'));
        }

        if($end < $start){
            return redirect()->back()->with(['failed' => 'Tanggal maperas tidak valid', 'tab' => 'maperas']);
        }


        $maperas = $this->serviceMaperas->getAllData($banjar_adat_id, $request)->get()->filter(function ($item) {
            $banjar_adat_id = session()->get('banjar_adat_id');
            if($item->jenis_maperas == 'satu_banjar_adat'){
                $item->jenis = 'Satu Banjar Adat';
                return $item;
            }else if($item->jenis_maperas == 'beda_banjar_adat'){
                if($item->banjar_adat_baru_id == $banjar_adat_id){
                    $item->jenis = 'Beda Banjar Adat (Masuk)';
                    return $item;
                }else if($item->banjar_adat_baru_id == $banjar_adat_id){
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
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-maperas', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Maperas.pdf');
    }

    # Laporan Super Admin
    public function lapKramaSuperAdmin(Request $request)
    {
        # Validasi
        if (!$request->kabupaten) {
            return redirect()->back()->with('failed', 'Kabupaten wajib dipilih');
        }

        if (!$request->kecamatan) {
            return redirect()->back()->with('failed', 'Kecamatan wajib dipilih');
        }

        if (!$request->desa_adat) {
            return redirect()->back()->with('failed', 'Desa Adat wajib dipilih');
        }

        # define variabel total
        $total_krama_mipil = 0;
        $total_krama_mipil_laki = 0;
        $total_krama_mipil_perempuan = 0;

        $total_krama_tamiu = 0;
        $total_krama_tamiu_laki = 0;
        $total_krama_tamiu_perempuan = 0;

        $total_tamiu = 0;
        $total_tamiu_laki = 0;
        $total_tamiu_perempuan = 0;

        $kecamatan = Kecamatan::find($request->kecamatan);
        $kabupaten = Kabupaten::find($request->kabupaten);

        # count
        foreach($request->desa_adat as $item)
        {
            $desa_adat = DesaAdat::find($item);

            # Get Arr Banjar Adat
            $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();

            # Get Krama Mipil
            $krama_mipil = KramaMipil::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->count();
            $total_krama_mipil = $total_krama_mipil + $krama_mipil;

            $krama_mipil_laki = KramaMipil::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_krama_mipil_laki = $total_krama_mipil_laki + $krama_mipil_laki;

            $krama_mipil_perempuan = KramaMipil::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_krama_mipil_perempuan = $total_krama_mipil_perempuan + $krama_mipil_perempuan;

            # Get Krama Tamiu
            $krama_tamiu = KramaTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->count();
            $total_krama_tamiu = $total_krama_tamiu + $krama_tamiu;

            $krama_tamiu_laki = KramaTamiu::with('cacah_krama_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_krama_tamiu_laki = $total_krama_tamiu_laki + $krama_tamiu_laki;

            $krama_tamiu_perempuan = KramaTamiu::with('cacah_krama_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_krama_tamiu_perempuan = $total_krama_tamiu_perempuan + $krama_tamiu_perempuan;

            # Get Tamiu
            $tamiu = Tamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->count();
            $total_tamiu = $total_tamiu + $tamiu;

            $tamiu_laki = Tamiu::with('cacah_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_tamiu_laki = $total_tamiu_laki + $tamiu_laki;

            $tamiu_perempuan = Tamiu::with('cacah_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_tamiu_perempuan = $total_tamiu_perempuan + $tamiu_perempuan;

            # Assign jumlah ke Desa Adat
            $desa_adat->jumlah_krama_mipil = $krama_mipil;
            $desa_adat->jumlah_krama_mipil_laki = $krama_mipil_laki;
            $desa_adat->jumlah_krama_mipil_perempuan = $krama_mipil_perempuan;

            $desa_adat->jumlah_krama_tamiu = $krama_tamiu;
            $desa_adat->jumlah_krama_tamiu_laki = $krama_tamiu_laki;
            $desa_adat->jumlah_krama_tamiu_perempuan = $krama_tamiu_perempuan;

            $desa_adat->jumlah_tamiu = $tamiu;
            $desa_adat->jumlah_tamiu_laki = $tamiu_laki;
            $desa_adat->jumlah_tamiu_perempuan = $tamiu_perempuan;

            #Assign all data
            $data_desa_adat[] = $desa_adat;
        }

        $data = [
            'desa_adat' => $data_desa_adat,
            'total_krama_mipil' => $total_krama_mipil,
            'total_krama_tamiu' => $total_krama_tamiu,
            'total_tamiu' => $total_tamiu,
            'total_krama_mipil_laki' => $total_krama_mipil_laki,
            'total_krama_tamiu_laki' => $total_krama_tamiu_laki,
            'total_tamiu_laki' => $total_tamiu_laki,
            'total_krama_mipil_perempuan' => $total_krama_mipil_perempuan,
            'total_krama_tamiu_perempuan' => $total_krama_tamiu_perempuan,
            'total_tamiu_perempuan' => $total_tamiu_perempuan,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten
        ];

        $pdf = PDF::loadView('vendor.export.pdf.super_admin.laporan-krama', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Krama.pdf');
    }

    public function lapCacahKramaSuperAdmin(Request $request)
    {
        # Validasi
        if (!$request->kabupaten_cacah) {
            return redirect()->back()->with(['failed' => 'Kabupaten wajib dipilih', 'tab' => 'cacah']);
        }

        if (!$request->kecamatan_cacah) {
            return redirect()->back()->with(['failed' => 'Kecamatan wajib dipilih', 'tab' => 'cacah']);
        }

        if (!$request->desa_adat_cacah) {
            return redirect()->back()->with(['failed' => 'Desa Adat wajib dipilih', 'tab' => 'cacah']);
        }

        # define variabel total
        $total_cacah_krama_mipil = 0;
        $total_cacah_krama_mipil_laki = 0;
        $total_cacah_krama_mipil_perempuan = 0;

        $total_cacah_krama_tamiu = 0;
        $total_cacah_krama_tamiu_laki = 0;
        $total_cacah_krama_tamiu_perempuan = 0;

        $total_cacah_tamiu = 0;
        $total_cacah_tamiu_laki = 0;
        $total_cacah_tamiu_perempuan = 0;

        $kecamatan = Kecamatan::find($request->kecamatan_cacah);
        $kabupaten = Kabupaten::find($request->kabupaten_cacah);

        # count
        foreach($request->desa_adat_cacah as $item)
        {
            $desa_adat = DesaAdat::find($item);

            # Get Arr Banjar Adat
            $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();
            $arr_kk_krama_mipil = KramaMipil::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->pluck('cacah_krama_mipil_id');
            $arr_kk_krama_tamiu = KramaTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->pluck('cacah_krama_tamiu_id');
            $arr_kk_tamiu = Tamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->pluck('cacah_tamiu_id');

            # Get CacahKrama Mipil
            $cacah_krama_mipil = CacahKramaMipil::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_mipil)->count();
            $total_cacah_krama_mipil = $total_cacah_krama_mipil + $cacah_krama_mipil;

            $cacah_krama_mipil_laki = CacahKramaMipil::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_mipil)
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_cacah_krama_mipil_laki = $total_cacah_krama_mipil_laki + $cacah_krama_mipil_laki;

            $cacah_krama_mipil_perempuan = CacahKramaMipil::with('penduduk')->whereNotIn('id', $arr_kk_krama_mipil)
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_cacah_krama_mipil_perempuan = $total_cacah_krama_mipil_perempuan + $cacah_krama_mipil_perempuan;

            # Get CacahKrama Tamiu
            $cacah_krama_tamiu = CacahKramaTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_tamiu)->count();
            $total_cacah_krama_tamiu = $total_cacah_krama_tamiu + $cacah_krama_tamiu;

            $cacah_krama_tamiu_laki = CacahKramaTamiu::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_tamiu)
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_cacah_krama_tamiu_laki = $total_cacah_krama_tamiu_laki + $cacah_krama_tamiu_laki;

            $cacah_krama_tamiu_perempuan = CacahKramaTamiu::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_tamiu)
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_cacah_krama_tamiu_perempuan = $total_cacah_krama_tamiu_perempuan + $cacah_krama_tamiu_perempuan;

            # Get Tamiu
            $cacah_tamiu = CacahTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu)->count();
            $total_cacah_tamiu = $total_cacah_tamiu + $cacah_tamiu;

            $cacah_tamiu_laki = CacahTamiu::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu)
            ->whereHas('penduduk', function ($query) use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'laki-laki');
            })
            ->orWhereHas('wna', function ($query)use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'laki-laki');
            })
            ->count();
            $total_cacah_tamiu_laki = $total_cacah_tamiu_laki + $cacah_tamiu_laki;

            $cacah_tamiu_perempuan = CacahTamiu::with('cacah_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu)
            ->whereHas('penduduk', function ($query) use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'perempuan');
            })
            ->orWhereHas('wna', function ($query) use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_cacah_tamiu_perempuan = $total_cacah_tamiu_perempuan + $cacah_tamiu_perempuan;

            # Assign jumlah ke Desa Adat
            $desa_adat->jumlah_cacah_krama_mipil = $cacah_krama_mipil;
            $desa_adat->jumlah_cacah_krama_mipil_laki = $cacah_krama_mipil_laki;
            $desa_adat->jumlah_cacah_krama_mipil_perempuan = $cacah_krama_mipil_perempuan;

            $desa_adat->jumlah_cacah_krama_tamiu = $cacah_krama_tamiu;
            $desa_adat->jumlah_cacah_krama_tamiu_laki = $cacah_krama_tamiu_laki;
            $desa_adat->jumlah_cacah_krama_tamiu_perempuan = $cacah_krama_tamiu_perempuan;

            $desa_adat->jumlah_cacah_tamiu = $cacah_tamiu;
            $desa_adat->jumlah_cacah_tamiu_laki = $cacah_tamiu_laki;
            $desa_adat->jumlah_cacah_tamiu_perempuan = $cacah_tamiu_perempuan;

            #Assign all data
            $data_desa_adat[] = $desa_adat;
        }

        $data = [
            'desa_adat' => $data_desa_adat,
            'total_cacah_krama_mipil' => $total_cacah_krama_mipil,
            'total_cacah_krama_tamiu' => $total_cacah_krama_tamiu,
            'total_cacah_tamiu' => $total_cacah_tamiu,
            'total_cacah_krama_mipil_laki' => $total_cacah_krama_mipil_laki,
            'total_cacah_krama_tamiu_laki' => $total_cacah_krama_tamiu_laki,
            'total_cacah_tamiu_laki' => $total_cacah_tamiu_laki,
            'total_cacah_krama_mipil_perempuan' => $total_cacah_krama_mipil_perempuan,
            'total_cacah_krama_tamiu_perempuan' => $total_cacah_krama_tamiu_perempuan,
            'total_cacah_tamiu_perempuan' => $total_cacah_tamiu_perempuan,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
        ];

        $pdf = PDF::loadView('vendor.export.pdf.super_admin.laporan-cacah-krama', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Cacah Krama.pdf');
    }

    public function lapMutasiSuperAdmin(Request $request)
    {
        # Validasi
        if (!$request->kabupaten_mutasi) {
            return redirect()->back()->with(['failed' => 'Kabupaten wajib dipilih', 'tab' => 'mutasi']);
        }

        if (!$request->kecamatan_mutasi) {
            return redirect()->back()->with(['failed' => 'Kecamatan wajib dipilih', 'tab' => 'mutasi']);
        }

        if (!$request->desa_adat_mutasi) {
            return redirect()->back()->with(['failed' => 'Desa Adat wajib dipilih', 'tab' => 'mutasi']);
        }

        $today = Carbon::now()->toDateString();
        if ($request->tgl_mutasi_awal != NULL && $request->tgl_mutasi_akhir != NULL) {
            $tgl_mutasi_awal = date("Y-m-d", strtotime($request->tgl_mutasi_awal));
            $tgl_mutasi_akhir = date("Y-m-d", strtotime($request->tgl_mutasi_akhir));

            if ($tgl_mutasi_awal > $today) {
                return redirect()->back()->with(['failed' => 'Rentang awal tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
            }

            if ($tgl_mutasi_akhir > $today) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
            }

            if ($tgl_mutasi_akhir < $tgl_mutasi_awal) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal mutasi tidak dapat lebih kecil dari rentang awal tanggal mutasi', 'tab' => 'mutasi']);
            }
        } else {
            if ($request->tgl_mutasi_awal) {
                $tgl_mutasi_awal = date("Y-m-d", strtotime($request->tgl_mutasi_awal));
                if ($tgl_mutasi_awal > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang awal tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
                }
            }

            if ($request->tgl_mutasi_akhir) {
                $tgl_mutasi_akhir = date("Y-m-d", strtotime($request->tgl_mutasi_akhir));
                if ($tgl_mutasi_akhir > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang akhir tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
                }
            }
        }

        # define
        $total_kelahiran = 0;
        $total_kelahiran_laki = 0;
        $total_kelahiran_perempuan = 0;

        $total_kematian = 0;
        $total_kematian_laki = 0;
        $total_kematian_perempuan = 0;

        $total_perkawinan = 0;
        $total_perceraian = 0;
        $total_maperas = 0;

        $kecamatan = Kecamatan::find($request->kecamatan_mutasi);
        $kabupaten = Kabupaten::find($request->kabupaten_mutasi);

        # count
        foreach($request->desa_adat_mutasi as $item)
        {
            $desa_adat = DesaAdat::find($item);

            # Get Arr Banjar Adat
            $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();

            # Get Kelahiran
            $kelahiran = Kelahiran::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1');
            if(isset($request->tgl_mutasi_awal)){
                $kelahiran->where('tanggal_lahir', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kelahiran->where('tanggal_lahir', '<=', $tgl_mutasi_akhir);
            }
            $kelahiran = $kelahiran->count();
            $total_kelahiran = $total_kelahiran + $kelahiran;

            $kelahiran_laki = Kelahiran::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kelahiran_laki->where('tanggal_lahir', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kelahiran_laki->where('tanggal_lahir', '<=', $tgl_mutasi_akhir);
            }
            $kelahiran_laki = $kelahiran_laki->count();
            $total_kelahiran_laki = $total_kelahiran_laki + $kelahiran_laki;

            $kelahiran_perempuan = Kelahiran::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kelahiran_perempuan->where('tanggal_lahir', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kelahiran_perempuan->where('tanggal_lahir', '<=', $tgl_mutasi_akhir);
            }
            $kelahiran_perempuan = $kelahiran_perempuan->count();
            $total_kelahiran_perempuan = $total_kelahiran_perempuan + $kelahiran_perempuan;

            # Get Kematian
            $kematian = Kematian::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1');
            if(isset($request->tgl_mutasi_awal)){
                $kematian->where('tanggal_kematian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kematian->where('tanggal_kematian', '<=', $tgl_mutasi_akhir);
            }
            $kematian = $kematian->count();
            $total_kematian = $total_kematian + $kematian;

            $kematian_laki = Kematian::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kematian_laki->where('tanggal_kematian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kematian_laki->where('tanggal_kematian', '<=', $tgl_mutasi_akhir);
            }
            $kematian_laki = $kematian_laki->count();
            $total_kematian_laki = $total_kematian_laki + $kematian_laki;

            $kematian_perempuan = Kematian::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kematian_perempuan->where('tanggal_kematian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kematian_perempuan->where('tanggal_kematian', '<=', $tgl_mutasi_akhir);
            }
            $kematian_perempuan = $kematian_perempuan->count();
            $total_kematian_perempuan = $total_kematian_perempuan + $kematian_perempuan;

            # Get Perkawinan
            $perkawinan = Perkawinan::where(function ($query) use ($arr_banjar_adat_id) {
                $query->whereIn('banjar_adat_purusa_id', $arr_banjar_adat_id)
                    ->orWhereIn('banjar_adat_pradana_id', $arr_banjar_adat_id);
            })->where('status_perkawinan', '3');
            if(isset($request->tgl_mutasi_awal)){
                $perkawinan->where('tanggal_perkawinan', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $perkawinan->where('tanggal_perkawinan', '<=', $tgl_mutasi_akhir);
            }
            $perkawinan = $perkawinan->count();
            $total_perkawinan = $total_perkawinan + $perkawinan;

            # Get Perceraian
            $perceraian = Perceraian::where(function ($query) use ($arr_banjar_adat_id) {
                $query->whereIn('banjar_adat_purusa_id', $arr_banjar_adat_id)
                    ->orWhereIn('banjar_adat_pradana_id', $arr_banjar_adat_id);
            })->where('status_perceraian', '3');
            if(isset($request->tgl_mutasi_awal)){
                $perceraian->where('tanggal_perceraian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $perceraian->where('tanggal_perceraian', '<=', $tgl_mutasi_akhir);
            }
            $perceraian = $perceraian->count();
            $total_perceraian = $total_perceraian + $perceraian;

            # Get Maperas
            $maperas = Maperas::where(function ($query) use ($arr_banjar_adat_id) {
                $query->whereIn('banjar_adat_lama_id', $arr_banjar_adat_id)
                    ->orWhereIn('banjar_adat_baru_id', $arr_banjar_adat_id);
            })->where('status_maperas', '3');
            if(isset($request->tgl_mutasi_awal)){
                $maperas->where('tanggal_maperas', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $maperas->where('tanggal_maperas', '<=', $tgl_mutasi_akhir);
            }
            $maperas = $maperas->count();
            $total_maperas = $total_maperas + $maperas;

            # Assign jumlah ke Desa Adat
            $desa_adat->jumlah_kelahiran = $kelahiran;
            $desa_adat->jumlah_kelahiran_laki = $kelahiran_laki;
            $desa_adat->jumlah_kelahiran_perempuan = $kelahiran_perempuan;

            $desa_adat->jumlah_kematian = $kematian;
            $desa_adat->jumlah_kematian_laki = $kematian_laki;
            $desa_adat->jumlah_kematian_perempuan = $kematian_perempuan;

            $desa_adat->jumlah_perkawinan = $perkawinan;
            $desa_adat->jumlah_perceraian = $perceraian;
            $desa_adat->jumlah_maperas = $maperas;

            #Assign all data
            $data_desa_adat[] = $desa_adat;
        }


        $data = [
            'desa_adat' => $data_desa_adat,
            'total_kelahiran' => $total_kelahiran,
            'total_kelahiran_laki' => $total_kelahiran_laki,
            'total_kelahiran_perempuan' => $total_kelahiran_perempuan,
            'total_kematian' => $total_kematian,
            'total_kematian_laki' => $total_kematian_laki,
            'total_kematian_perempuan' => $total_kematian_perempuan,
            'total_perkawinan' => $total_perkawinan,
            'total_perceraian' => $total_perceraian,
            'total_maperas' => $total_maperas,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
        ];

        $pdf = PDF::loadView('vendor.export.pdf.super_admin.laporan-mutasi', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Mutasi.pdf');
    }

    #Laporan Desa Adat
    public function lapKelahiranDesaAdat(Request $request)
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

        # Get Data Kelahiran
        $kelahiran = $this->serviceKelahiran->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'kelahiran' => $kelahiran,
        ];

        $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-kelahiran', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Kelahiran.pdf');
    }

    public function lapKematianDesaAdat(Request $request)
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

        # Get Data Kematian
        $kematian = $this->serviceKematian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'kematian' => $kematian,
        ];
        $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-kematian', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Kematian.pdf');
    }

    public function lapPerkawinanDesaAdat(Request $request)
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
            'perkawinan' => $perkawinan
        ];

        $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-perkawinan', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Perkawinan.pdf');
    }

    public function lapPerceraianDesaAdat(Request $request)
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

        $perceraian = $this->servicePerceraian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'perceraian' => $perceraian
        ];

        $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-perceraian', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Perceraian.pdf');
    }

    public function lapMaperasDesaAdat(Request $request)
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
            'maperas' => $maperas
        ];

        $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-maperas', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Maperas.pdf');
    }
}
