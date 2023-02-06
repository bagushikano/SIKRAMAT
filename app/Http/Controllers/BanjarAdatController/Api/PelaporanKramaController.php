<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use Carbon\Carbon;
use App\Models\DesaAdat;
use App\Models\Tempekan;
use App\Models\DesaDinas;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\BanjarDinas;
use Illuminate\Http\Request;
use App\Services\TamiuService;
use App\Services\TempekanService;
use App\Services\PekerjaanService;
use App\Services\KramaMipilService;
use App\Services\KramaTamiuService;
use App\Services\PendidikanService;
use App\Http\Controllers\Controller;
use App\Services\BanjarDinasService;

class PelaporanKramaController extends Controller
{
    public function __construct
    (
        PendidikanService $servicePendidikan,
        PekerjaanService $servicePekerjaan,
        TempekanService $serviceTempekan,
        BanjarDinasService $serviceBanjarDinas,
        KramaMipilService $serviceKramaMipil,
        KramaTamiuService $serviceKramaTamiu,
        TamiuService $serviceTamiu
    )
    {
        $this->serviceTempekan = $serviceTempekan;
        $this->servicePendidikan = $servicePendidikan;
        $this->servicePekerjaan = $servicePekerjaan;
        $this->serviceBanjarDinas = $serviceBanjarDinas;
        $this->serviceKramaMipil = $serviceKramaMipil;
        $this->serviceKramaTamiu = $serviceKramaTamiu;
        $this->serviceTamiu = $serviceTamiu;
    }

    public function index(Request $req) {
        $data = [
            'tempekan' => $this->serviceTempekan->findByBanjar($req->query('banjar_adat_id')),
            'banjar_dinas' => $this->serviceBanjarDinas->findByDesaAdatId($req->query('desa_adat_id')),
            'pendidikan' => $this->servicePendidikan->all(),
            'pekerjaan' => $this->servicePekerjaan->all(),
            'banjar_adat_id' => $req->query('banjar_adat_id'),
            'desa_adat_id' => $req->query('desa_adat_id'),
        ];
        return view('pages.banjar.pelaporan.mobile.krama', compact('data'));
    }
}
