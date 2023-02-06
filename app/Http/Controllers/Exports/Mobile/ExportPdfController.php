<?php

namespace App\Http\Controllers\Exports\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        // $this->middleware('auth');
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

    public function lapKramaMipil(Request $request, $banjar_adat)
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

        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $data = [
            'krama_mipil' => $this->serviceKramaMipil->getAllData($banjar_adat_id, $request),
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        if ($request->banjar_adat_mipil) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Krama Mipil.pdf');
    }

    public function lapKramaTamiu(Request $request, $banjar_adat)
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


        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $data = [
            'krama_tamiu' => $this->serviceKramaTamiu->getAllData($banjar_adat_id, $request),
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        if ($request->banjar_adat_tamiu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }
        return $pdf->download('Laporan Data Krama Tamiu.pdf');
    }

    public function lapTamiu(Request $request, $banjar_adat)
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


        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $data = [
            'tamiu' => $this->serviceTamiu->getAllData($banjar_adat_id, $request),
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        if ($request->banjar_adat_tamu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }
        return $pdf->download('Laporan Data Tamiu.pdf');
    }

    public function lapCacahKramaMipil(Request $request, $banjar_adat)
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

        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $data = [
            'krama_mipil' => $this->serviceCacahKramaMipil->getAllData($banjar_adat_id, $request),
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        if ($request->banjar_adat_mipil) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-cacah-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-cacah-krama-mipil', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Cacah Krama Mipil.pdf');
    }

    public function lapCacahKramaTamiu(Request $request, $banjar_adat)
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


        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $data = [
            'krama_tamiu' => $this->serviceCacahKramaTamiu->getAllData($banjar_adat_id, $request),
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        if ($request->banjar_adat_tamiu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-cacah-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-cacah-krama-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Cacah Krama Tamiu.pdf');
    }

    public function lapCacahTamiu(Request $request, $banjar_adat)
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


        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $data = [
            'tamiu' => $this->serviceCacahTamiu->getAllData($banjar_adat_id, $request),
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        if ($request->banjar_adat_tamu) {
            $pdf = PDF::loadView('vendor.export.pdf.desa_adat.laporan-cacah-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        } else {
            $pdf = PDF::loadView('vendor.export.pdf.laporan-cacah-tamiu', ["data" => $data])->setPaper('letter', 'portrait');
        }

        return $pdf->download('Laporan Data Cacah Tamiu.pdf');
    }

    public function lapKelahiran(Request $request, $banjar_adat)
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
        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        # Get Data Kelahiran
        $kelahiran = $this->serviceKelahiran->getAllData($banjar_adat_id, $request)->get();
        // foreach($kelahiran as $item){
        //     $item->tanggal_lahir = Helper::convert_date_to_locale_id($item->tanggal_lahir);
        // }

        $data = [
            'kelahiran' => $kelahiran,
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-kelahiran', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Kelahiran.pdf');
    }

    public function lapKematian(Request $request, $banjar_adat)
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
        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $kematian = $this->serviceKematian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'kematian' => $kematian,
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];
        $pdf = PDF::loadView('vendor.export.pdf.laporan-kematian', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Kematian.pdf');
    }

    public function lapPerkawinan(Request $request, $banjar_adat)
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
        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

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

        $perkawinan = $this->servicePerkawinan->getAllData($banjar_adat_id, $request)->get()->filter(function ($item) use ($banjar_adat) {
            $banjar_adat_id = $banjar_adat;
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
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-perkawinan', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Perkawinan.pdf');
    }

    public function lapPerceraian(Request $request, $banjar_adat)
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
        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();

        $perceraian = $this->servicePerceraian->getAllData($banjar_adat_id, $request)->get();

        $data = [
            'perceraian' => $perceraian,
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-perceraian', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Perceraian.pdf');
    }

    public function lapMaperas(Request $request, $banjar_adat)
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
        $banjar_adat_id = $banjar_adat;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();
        $desaAdat = DesaAdat::where('id', $banjarAdat->desa_adat_id)->first();


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


        $maperas = $this->serviceMaperas->getAllData($banjar_adat_id, $request)->get()->filter(function ($item) use ($banjar_adat) {
            $banjar_adat_id = $banjar_adat;
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
            'banjar_adat' => $banjarAdat,
            'desa_adat' => $desaAdat
        ];

        $pdf = PDF::loadView('vendor.export.pdf.laporan-maperas', ["data" => $data])->setPaper('letter', 'portrait');
        return $pdf->download('Laporan Data Maperas.pdf');
    }
}
