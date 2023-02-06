<?php

namespace App\Http\Controllers\Exports\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\LaporanTamiu;
use App\Services\TamiuService;
use App\Exports\LaporanCacahTamiu;
use App\Exports\LaporanKramaMipil;
use App\Exports\LaporanKramaTamiu;
use App\Services\CacahTamiuService;
use App\Services\KramaMipilService;
use App\Services\KramaTamiuService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanCacahKramaMipil;
use App\Exports\LaporanCacahKramaTamiu;
use App\Services\CacahKramaMipilService;
use App\Services\CacahKramaTamiuService;

class ExportExcelController extends Controller
{
    public function __construct
    (
        KramaMipilService $serviceKramaMipil,
        KramaTamiuService $serviceKramaTamiu,
        TamiuService $serviceTamiu,
        CacahKramaMipilService $serviceCacahKramaMipil,
        CacahKramaTamiuService $serviceCacahKramaTamiu,
        CacahTamiuService $serviceCacahTamiu
    )
    {
        $this->serviceKramaMipil = $serviceKramaMipil;
        $this->serviceKramaTamiu = $serviceKramaTamiu;
        $this->serviceTamiu = $serviceTamiu;
        $this->serviceCacahKramaMipil = $serviceCacahKramaMipil;
        $this->serviceCacahKramaTamiu = $serviceCacahKramaTamiu;
        $this->serviceCacahTamiu = $serviceCacahTamiu;
    }

    public function lapKramaMipil(Request $request,$banjar_adat)
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
        $kramaMipil = $this->serviceKramaMipil->getAllData($banjar_adat_id, $request);

        $file_name = "Laporan Krama Mipil.xlsx";
        try {
            return Excel::download(new LaporanKramaMipil($kramaMipil, $request), $file_name);
        } catch (\Throwable $th) {
            return redirect('/');
        }
    }

    public function lapKramaTamiu(Request $request,$banjar_adat)
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
        $kramaTamiu = $this->serviceKramaTamiu->getAllData($banjar_adat_id, $request);

        $file_name = "Laporan Krama Tamiu.xlsx";
        try {
            return Excel::download(new LaporanKramaTamiu($kramaTamiu, $request), $file_name);
        } catch (\Throwable $th) {
            return redirect('/');
        }
    }

    public function lapTamiu(Request $request,$banjar_adat)
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
        $tamiu = $this->serviceTamiu->getAllData($banjar_adat_id, $request);

        $file_name = "Laporan Tamiu.xlsx";
        try {
            return Excel::download(new LaporanTamiu($tamiu, $request), $file_name);
        } catch (\Throwable $th) {
            return redirect('/');
        }
    }

    public function lapCacahKramaMipil(Request $request,$banjar_adat)
    {
        if ($request->banjar_adat_mipil) {
            if (!$request->banjar_adat_mipil) {
                return redirect()->back()->with('failed', 'Tempekan wajib dipilih');
            }
        } else {
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
        $kramaMipil = $this->serviceCacahKramaMipil->getAllData($banjar_adat_id, $request);

        $file_name = "Laporan Cacah Krama Mipil.xlsx";
        try {
            return Excel::download(new LaporanCacahKramaMipil($kramaMipil, $request), $file_name);
        } catch (\Throwable $th) {
            return redirect('/');
        }
    }

    public function lapCacahKramaTamiu(Request $request,$banjar_adat)
    {
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
        $kramaTamiu = $this->serviceCacahKramaTamiu->getAllData($banjar_adat_id, $request);


        $file_name = "Laporan Cacah Krama Tamiu.xlsx";
        try {
            return Excel::download(new LaporanCacahKramaTamiu($kramaTamiu, $request), $file_name);
        } catch (\Throwable $th) {
            return redirect('/');
        }
    }

    public function lapCacahTamiu(Request $request,$banjar_adat)
    {
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
        $tamiu = $this->serviceCacahTamiu->getAllData($banjar_adat_id, $request);

        $file_name = "Laporan Cacah Tamiu.xlsx";
        try {
            return Excel::download(new LaporanCacahTamiu($tamiu, $request), $file_name);
        } catch (\Throwable $th) {
            return redirect('/');
        }
    }
}
