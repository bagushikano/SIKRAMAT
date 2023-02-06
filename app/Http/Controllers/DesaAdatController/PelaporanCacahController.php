<?php

namespace App\Http\Controllers\DesaAdatController;

use Carbon\Carbon;
use App\Models\Pekerjaan;
use App\Models\BanjarAdat;
use App\Models\Pendidikan;
use App\Models\BanjarDinas;
use Illuminate\Http\Request;
use App\Services\PekerjaanService;
use App\Services\BanjarAdatService;
use App\Services\CacahTamiuService;
use App\Services\PendidikanService;
use App\Http\Controllers\Controller;
use App\Services\BanjarDinasService;
use App\Services\CacahKramaMipilService;
use App\Services\CacahKramaTamiuService;

class PelaporanCacahController extends Controller
{
    public function __construct
    (
        BanjarAdatService $serviceBanjarAdat,
        BanjarDinasService $serviceBanjarDinas,
        PendidikanService $servicePendidikan,
        PekerjaanService $servicePekerjaan,
        CacahKramaMipilService $serviceCacahKramaMipil,
        CacahKramaTamiuService $serviceCacahKramaTamiu,
        CacahTamiuService $serviceCacahTamiu
    )
    {
        $this->middleware('auth');

        $this->serviceBanjarAdat = $serviceBanjarAdat;
        $this->serviceBanjarDinas = $serviceBanjarDinas;
        $this->servicePendidikan = $servicePendidikan;
        $this->servicePekerjaan = $servicePekerjaan;
        $this->serviceCacahKramaMipil = $serviceCacahKramaMipil;
        $this->serviceCacahKramaTamiu = $serviceCacahKramaTamiu;
        $this->serviceCacahTamiu = $serviceCacahTamiu;
    }

    public function index()
    {
        $desadat_id = session()->get('desa_adat_id');

        $data = [
            'banjar_adat' => $this->serviceBanjarAdat->findByDesaAdatId($desadat_id),
            'banjar_dinas' => $this->serviceBanjarDinas->findByDesaAdatId(session()->get('desa_adat_id')),
            'pendidikan' => $this->servicePendidikan->all(),
            'pekerjaan' => $this->servicePekerjaan->all(),
        ];

        return view('pages.desa.pelaporan.cacah.index', compact('data'));
    }

    public function lapKramaMipil(Request $request)
    {
        if (!$request->banjar_adat_mipil) {
            return redirect()->back()->with('failed', 'Banjar adat wajib dipilih');
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


        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();


        # Validasi Tanggal Lahir
        if ($request->tgl_lahir_mipil_awal != NULL && $request->tgl_lahir_mipil_akhir != NULL) {
            $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_mipil_awal));
            $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_mipil_akhir));

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
            if ($request->tgl_lahir_mipil_awal) {
                $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_mipil_awal));
                if ($tgl_lahir_awal > $today) {
                    return redirect()->back()->with('failed', 'Rentang awal tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
    
            if ($request->tgl_lahir_mipil_akhir) {
                $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_mipil_akhir));
                if ($tgl_lahir_akhir > $today) {
                    return redirect()->back()->with('failed', 'Rentang akhir tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
        }


        # Validasi Tanggal Registrasi
        if ($request->tgl_registrasi_mipil_awal != NULL && $request->tgl_registrasi_mipil_akhir != NULL) {
            $tgl_regis_awal = date("Y-m-d", strtotime($request->tgl_registrasi_mipil_awal));
            $tgl_regis_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_mipil_akhir));

            if ($tgl_regis_awal > $today) {
                return redirect()->back()->with('failed', 'Rentang awal tanggal registrasi tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_regis_akhir > $today) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_regis_akhir < $tgl_regis_awal) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi rentang awal tanggal registrasi');
            }
        } else {
            if ($request->tgl_registrasi_mipil_awal) {
                $tgl_regis_awal = date("Y-m-d", strtotime($request->tgl_registrasi_mipil_awal));
                if ($tgl_regis_awal > $today) {
                    return redirect()->back()->with('failed', 'Rentang awal tanggal registrasi tidak dapat melebihi tanggal hari ini');
                }
            }

            if ($request->tgl_registrasi_mipil_akhir) {
                $tgl_regis_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_mipil_akhir));
                if ($tgl_regis_akhir > $today) {
                    return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi tanggal hari ini');
                }
            }
        }
        

        # Get Banjar Data ID
        $desadat_id = session()->get('desa_adat_id');
        
        # Get Grafik Tempekan
        if ($request->banjar_adat_mipil) {
            foreach ($request->banjar_adat_mipil as $data) {
                $nama_banjar_adat[] = BanjarAdat::where('id', $data)->first()->nama_banjar_adat;
            }
            $grafik_banjar_adat_laki = $this->serviceCacahKramaMipil->getGrafikBanjarAdat($desadat_id, $request, 'laki-laki');
            $grafik_banjar_adat_perempuan = $this->serviceCacahKramaMipil->getGrafikBanjarAdat($desadat_id, $request, 'perempuan');
        }

        # Get Grafik Pekerjaan/Profesi
        foreach ($request->pekerjaan_mipil as $data) {
            $nama_profesi[] = Pekerjaan::where('id', $data)->first()->profesi;
        }
        $grafik_profesi_laki = $this->serviceCacahKramaMipil->getGrafikProfesi($desadat_id, $request, 'laki-laki');
        $grafik_profesi_perempuan = $this->serviceCacahKramaMipil->getGrafikProfesi($desadat_id, $request, 'perempuan');

        # Get Grafik Pendidikan
        foreach ($request->pendidikan_mipil as $data) {
            $jenjang_pendidikan[] = Pendidikan::where('id', $data)->first()->jenjang_pendidikan;
        }
        $grafik_pendidikan_laki = $this->serviceCacahKramaMipil->getGrafikPendidikan($desadat_id, $request, 'laki-laki');
        $grafik_pendidikan_perempuan = $this->serviceCacahKramaMipil->getGrafikPendidikan($desadat_id, $request, 'perempuan');

        # Get Grafik Golongan Darah
        foreach ($request->goldar_mipil as $data) {
            $goldar[] = 'Golongan Darah '.$data;
        }
        $grafik_goldar_laki = $this->serviceCacahKramaMipil->getGrafikGoldar($desadat_id, $request, 'laki-laki');
        $grafik_goldar_perempuan = $this->serviceCacahKramaMipil->getGrafikGoldar($desadat_id, $request, 'perempuan');

        # Get All Krama Data
        $krama_mipil = $this->serviceCacahKramaMipil->getAllData($desadat_id, $request);


        $data = [
            'nama_banjar_adat' => $nama_banjar_adat,
            'grafik_banjar_adat_laki' => $grafik_banjar_adat_laki,
            'grafik_banjar_adat_perempuan' => $grafik_banjar_adat_perempuan,
            'nama_profesi' => $nama_profesi,
            'grafik_profesi_laki' => $grafik_profesi_laki,
            'grafik_profesi_perempuan' => $grafik_profesi_perempuan,
            'jenjang_pendidikan' => $jenjang_pendidikan,
            'grafik_pendidikan_laki' => $grafik_pendidikan_laki,
            'grafik_pendidikan_perempuan' => $grafik_pendidikan_perempuan,
            'golongan_darah' => $goldar,
            'grafik_goldar_laki' => $grafik_goldar_laki,
            'grafik_goldar_perempuan' => $grafik_goldar_perempuan,
            'krama_mipil' => $krama_mipil
        ];

        return view('pages.desa.pelaporan.cacah.laporan-krama-mipil', compact('data'));
    }

    public function lapKramaTamiu(Request $request)
    {
        if (!$request->banjar_adat_tamiu) {
            return redirect()->back()->with('failed', 'Banjar adat krama tamiu wajib dipilih');
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


        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();


        # Validasi Tanggal Lahir
        if ($request->tgl_lahir_tamiu_awal != NULL && $request->tgl_lahir_tamiu_akhir != NULL) {
            $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_tamiu_awal));
            $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_tamiu_akhir));

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
            if ($request->tgl_lahir_tamiu_awal) {
                $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_tamiu_awal));
                if ($tgl_lahir_awal > $today) {
                    return redirect()->back()->with('failed', 'Rentang awal tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
    
            if ($request->tgl_lahir_tamiu_akhir) {
                $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_tamiu_akhir));
                if ($tgl_lahir_akhir > $today) {
                    return redirect()->back()->with('failed', 'Rentang akhir tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
        }


        # Validasi Tanggal Registrasi
        if ($request->tgl_registrasi_tamiu_awal != NULL && $request->tgl_registrasi_tamiu_akhir != NULL) {
            $tgl_regis_awal = date("Y-m-d", strtotime($request->tgl_registrasi_tamiu_awal));
            $tgl_regis_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_tamiu_akhir));

            if ($tgl_regis_awal > $today) {
                return redirect()->back()->with('failed', 'Rentang awal tanggal registrasi tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_regis_akhir > $today) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_regis_akhir < $tgl_regis_awal) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi rentang awal tanggal registrasi');
            }
        } else {
            if ($request->tgl_registrasi_tamiu_awal) {
                $tgl_regis_awal = date("Y-m-d", strtotime($request->tgl_registrasi_tamiu_awal));
                if ($tgl_regis_awal > $today) {
                    return redirect()->back()->with('failed', 'Rentang awal tanggal registrasi tidak dapat melebihi tanggal hari ini');
                }
            }

            if ($request->tgl_registrasi_tamiu_akhir) {
                $tgl_regis_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_tamiu_akhir));
                if ($tgl_regis_akhir > $today) {
                    return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi tanggal hari ini');
                }
            }
        }
        

        # Get Banjar Data ID
        $banjar_adat_id = session()->get('banjar_adat_id');
        
        # Get Grafik Banjar Dinas
        foreach ($request->banjar_dinas_tamiu as $data) {
            $nama_banjar_dinas[] = BanjarDinas::where('id', $data)->first()->nama_banjar_dinas;
        }
        $grafik_banjar_dinas_laki = $this->serviceCacahKramaTamiu->getGrafikBanjarDinas($banjar_adat_id, $request, 'laki-laki');
        $grafik_banjar_dinas_perempuan = $this->serviceCacahKramaTamiu->getGrafikBanjarDinas($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Pekerjaan/Profesi
        foreach ($request->pekerjaan_tamiu as $data) {
            $nama_profesi[] = Pekerjaan::where('id', $data)->first()->profesi;
        }
        $grafik_profesi_laki = $this->serviceCacahKramaTamiu->getGrafikProfesi($banjar_adat_id, $request, 'laki-laki');
        $grafik_profesi_perempuan = $this->serviceCacahKramaTamiu->getGrafikProfesi($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Pendidikan
        foreach ($request->pendidikan_tamiu as $data) {
            $jenjang_pendidikan[] = Pendidikan::where('id', $data)->first()->jenjang_pendidikan;
        }
        $grafik_pendidikan_laki = $this->serviceCacahKramaTamiu->getGrafikPendidikan($banjar_adat_id, $request, 'laki-laki');
        $grafik_pendidikan_perempuan = $this->serviceCacahKramaTamiu->getGrafikPendidikan($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Golongan Darah
        foreach ($request->goldar_tamiu as $data) {
            $goldar[] = 'Golongan Darah '.$data;
        }
        $grafik_goldar_laki = $this->serviceCacahKramaTamiu->getGrafikGoldar($banjar_adat_id, $request, 'laki-laki');
        $grafik_goldar_perempuan = $this->serviceCacahKramaTamiu->getGrafikGoldar($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Asal
        $grafik_asal_laki = $this->serviceCacahKramaTamiu->getGrafikAsal($banjar_adat_id, $request, 'laki-laki');
        $grafik_asal_perempuan = $this->serviceCacahKramaTamiu->getGrafikAsal($banjar_adat_id, $request, 'perempuan');

        # Get All Krama Data
        $krama_tamiu = $this->serviceCacahKramaTamiu->getAllData($banjar_adat_id, $request);


        $data = [
            'nama_banjar_dinas' => $nama_banjar_dinas,
            'grafik_banjar_dinas_laki' => $grafik_banjar_dinas_laki,
            'grafik_banjar_dinas_perempuan' => $grafik_banjar_dinas_perempuan,
            'nama_profesi' => $nama_profesi,
            'grafik_profesi_laki' => $grafik_profesi_laki,
            'grafik_profesi_perempuan' => $grafik_profesi_perempuan,
            'jenjang_pendidikan' => $jenjang_pendidikan,
            'grafik_pendidikan_laki' => $grafik_pendidikan_laki,
            'grafik_pendidikan_perempuan' => $grafik_pendidikan_perempuan,
            'golongan_darah' => $goldar,
            'grafik_goldar_laki' => $grafik_goldar_laki,
            'grafik_goldar_perempuan' => $grafik_goldar_perempuan,
            'grafik_asal_laki' => $grafik_asal_laki,
            'grafik_asal_perempuan' => $grafik_asal_perempuan,
            'krama_tamiu' => $krama_tamiu
        ];

        return view('pages.desa.pelaporan.cacah.laporan-krama-tamiu', compact('data'));
    }

    public function lapTamiu(Request $request)
    {
        if (!$request->banjar_adat_tamu) {
            return redirect()->back()->with('failed', 'Banjar adat tamiu wajib dipilih');
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

        
        # Override Request Times Out
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        # Get Date Today
        $today = Carbon::now()->toDateString();


        # Validasi Tanggal Lahir
        if ($request->tgl_lahir_tamu_awal != NULL && $request->tgl_lahir_tamu_akhir != NULL) {
            $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_tamu_awal));
            $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_tamu_akhir));

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
            if ($request->tgl_lahir_tamu_awal) {
                $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_tamu_awal));
                if ($tgl_lahir_awal > $today) {
                    return redirect()->back()->with('failed', 'Rentang awal tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
    
            if ($request->tgl_lahir_tamu_akhir) {
                $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_tamu_akhir));
                if ($tgl_lahir_akhir > $today) {
                    return redirect()->back()->with('failed', 'Rentang akhir tanggal lahir tidak dapat melebihi tanggal hari ini');
                }
            }
        }


        # Validasi Tanggal Registrasi
        if ($request->tgl_registrasi_tamu_awal != NULL && $request->tgl_registrasi_tamu_akhir != NULL) {
            $tgl_regis_awal = date("Y-m-d", strtotime($request->tgl_registrasi_tamu_awal));
            $tgl_regis_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_tamu_akhir));

            if ($tgl_regis_awal > $today) {
                return redirect()->back()->with('failed', 'Rentang awal tanggal registrasi tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_regis_akhir > $today) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi tanggal hari ini');
            }

            if ($tgl_regis_akhir < $tgl_regis_awal) {
                return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi rentang awal tanggal registrasi');
            }
        } else {
            if ($request->tgl_registrasi_tamu_awal) {
                $tgl_regis_awal = date("Y-m-d", strtotime($request->tgl_registrasi_tamu_awal));
                if ($tgl_regis_awal > $today) {
                    return redirect()->back()->with('failed', 'Rentang awal tanggal registrasi tidak dapat melebihi tanggal hari ini');
                }
            }

            if ($request->tgl_registrasi_tamu_akhir) {
                $tgl_regis_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_tamu_akhir));
                if ($tgl_regis_akhir > $today) {
                    return redirect()->back()->with('failed', 'Rentang akhir tanggal registrasi tidak dapat melebihi tanggal hari ini');
                }
            }
        }
        

        # Get Banjar Data ID
        $banjar_adat_id = session()->get('banjar_adat_id');
        
        # Get Grafik Banjar Dinas
        foreach ($request->banjar_dinas_tamu as $data) {
            $nama_banjar_dinas[] = BanjarDinas::where('id', $data)->first()->nama_banjar_dinas;
        }
        $grafik_banjar_dinas_laki = $this->serviceCacahTamiu->getGrafikBanjarDinas($banjar_adat_id, $request, 'laki-laki');
        $grafik_banjar_dinas_perempuan = $this->serviceCacahTamiu->getGrafikBanjarDinas($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Pekerjaan/Profesi
        foreach ($request->pekerjaan_tamu as $data) {
            $nama_profesi[] = Pekerjaan::where('id', $data)->first()->profesi;
        }
        $grafik_profesi_laki = $this->serviceCacahTamiu->getGrafikProfesi($banjar_adat_id, $request, 'laki-laki');
        $grafik_profesi_perempuan = $this->serviceCacahTamiu->getGrafikProfesi($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Pendidikan
        foreach ($request->pendidikan_tamu as $data) {
            $jenjang_pendidikan[] = Pendidikan::where('id', $data)->first()->jenjang_pendidikan;
        }
        $grafik_pendidikan_laki = $this->serviceCacahTamiu->getGrafikPendidikan($banjar_adat_id, $request, 'laki-laki');
        $grafik_pendidikan_perempuan = $this->serviceCacahTamiu->getGrafikPendidikan($banjar_adat_id, $request, 'perempuan');

        # Get Grafik Golongan Darah
        foreach ($request->goldar_tamu as $data) {
            $goldar[] = 'Golongan Darah '.$data;
        }
        $grafik_goldar_laki = $this->serviceCacahTamiu->getGrafikGoldar($banjar_adat_id, $request, 'laki-laki');
        $grafik_goldar_perempuan = $this->serviceCacahTamiu->getGrafikGoldar($banjar_adat_id, $request, 'perempuan');

        # Get All Krama Data
        $tamiu = $this->serviceCacahTamiu->getAllData($banjar_adat_id, $request);


        $data = [
            'nama_banjar_dinas' => $nama_banjar_dinas,
            'grafik_banjar_dinas_laki' => $grafik_banjar_dinas_laki,
            'grafik_banjar_dinas_perempuan' => $grafik_banjar_dinas_perempuan,
            'nama_profesi' => $nama_profesi,
            'grafik_profesi_laki' => $grafik_profesi_laki,
            'grafik_profesi_perempuan' => $grafik_profesi_perempuan,
            'jenjang_pendidikan' => $jenjang_pendidikan,
            'grafik_pendidikan_laki' => $grafik_pendidikan_laki,
            'grafik_pendidikan_perempuan' => $grafik_pendidikan_perempuan,
            'golongan_darah' => $goldar,
            'grafik_goldar_laki' => $grafik_goldar_laki,
            'grafik_goldar_perempuan' => $grafik_goldar_perempuan,
            'tamiu' => $tamiu
        ];

        return view('pages.desa.pelaporan.cacah.laporan-tamiu', compact('data'));
    }
}
