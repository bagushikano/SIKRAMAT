<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use Carbon\Carbon;
use App\Models\Tempekan;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\BanjarDinas;
use Illuminate\Http\Request;
use App\Services\TempekanService;
use App\Services\PekerjaanService;
use App\Services\CacahTamiuService;
use App\Services\PendidikanService;
use App\Http\Controllers\Controller;
use App\Services\BanjarDinasService;
use App\Services\CacahKramaMipilService;
use App\Services\CacahKramaTamiuService;


class PelaporanCacahKramaController extends Controller
{
    public function __construct
    (
        PendidikanService $servicePendidikan,
        PekerjaanService $servicePekerjaan,
        TempekanService $serviceTempekan,
        BanjarDinasService $serviceBanjarDinas,
        CacahKramaMipilService $serviceCacahKramaMipil,
        CacahKramaTamiuService $serviceCacahKramaTamiu,
        CacahTamiuService $serviceCacahTamiu
    )
    {
        // $this->middleware('auth');

        $this->serviceTempekan = $serviceTempekan;
        $this->servicePendidikan = $servicePendidikan;
        $this->servicePekerjaan = $servicePekerjaan;
        $this->serviceBanjarDinas = $serviceBanjarDinas;
        $this->serviceCacahKramaMipil = $serviceCacahKramaMipil;
        $this->serviceCacahKramaTamiu = $serviceCacahKramaTamiu;
        $this->serviceCacahTamiu = $serviceCacahTamiu;
    }

    public function index(Request $req)
    {
        $data = [
            'tempekan' => $this->serviceTempekan->findByBanjar($req->query('banjar_adat_id')),
            'banjar_dinas' => $this->serviceBanjarDinas->findByDesaAdatId($req->query('desa_adat_id')),
            'pendidikan' => $this->servicePendidikan->all(),
            'pekerjaan' => $this->servicePekerjaan->all(),
            'banjar_adat_id' => $req->query('banjar_adat_id'),
            'desa_adat_id' => $req->query('desa_adat_id')
        ];

        return view('pages.banjar.pelaporan.mobile.cacahkrama', compact('data'));
    }
}
