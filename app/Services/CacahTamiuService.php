<?php

namespace App\Services;

use App\Models\CacahTamiu;
use App\Models\Tamiu;

class CacahTamiuService
{
    public function getAllData($banjar_adat_id, $request)
    {
        if($request->banjar_adat_tamu){
            $kk_id = Tamiu::whereIn('banjar_adat_id', $request->banjar_adat_tamu)->where('status', '1')->pluck('cacah_tamiu_id');
        }else{
            $kk_id = Tamiu::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('cacah_tamiu_id');
        }

        if (count($request->status_tamu) > 1) {
            if ($request->banjar_adat_tamu) {
                $tamiu = CacahTamiu::whereIn('tb_cacah_tamiu.banjar_adat_id', $request->banjar_adat_tamu)
                    ->leftJoin('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_tamiu.penduduk_id')
                    ->leftJoin('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                    ->leftJoin('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                    ->leftJoin('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_tamiu.banjar_dinas_id')
                    ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_tamiu.banjar_adat_id')
                    ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu ?? [''])
                    ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu ?? [''])
                    ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu ?? [''])
                    ->whereIn('tb_cacah_tamiu.banjar_dinas_id', $request->banjar_dinas_tamu)
                    ->whereNotIn('tb_cacah_tamiu.id', $kk_id)
                    ->where('tb_cacah_tamiu.status', '1')->orWhere(function ($query) use ($request) {
                        $query->where('tb_cacah_tamiu.status', '0')
                            ->whereNotNull('tb_cacah_tamiu.tanggal_keluar')
                            ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu)
                            ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu)
                            ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu)
                            ->orderBy('tb_cacah_tamiu.tanggal_registrasi', 'DESC');
                    })
                    ->orderBy('tb_cacah_tamiu.tanggal_masuk', 'DESC')
                    ->get();
            } else {
                $tamiu = CacahTamiu::where('tb_cacah_tamiu.banjar_adat_id', $banjar_adat_id)
                    ->leftJoin('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_tamiu.penduduk_id')
                    ->leftJoin('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                    ->leftJoin('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                    ->leftJoin('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_tamiu.banjar_dinas_id')
                    ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_tamiu.banjar_adat_id')
                    ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu ?? [''])
                    ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu ?? [''])
                    ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu ?? [''])
                    ->whereIn('tb_cacah_tamiu.banjar_dinas_id', $request->banjar_dinas_tamu)
                    ->whereNotIn('tb_cacah_tamiu.id', $kk_id)
                    ->where('tb_cacah_tamiu.status', '1')->orWhere(function ($query) use ($request) {
                        $query->where('tb_cacah_tamiu.status', '0')
                            ->whereNotNull('tb_cacah_tamiu.tanggal_keluar')
                            ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu)
                            ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu)
                            ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu)
                            ->orderBy('tb_cacah_tamiu.tanggal_registrasi', 'DESC');
                    })
                    ->orderBy('tb_cacah_tamiu.tanggal_masuk', 'DESC')
                    ->get();
            }
        } else {
            if (in_array('0', $request->status_tamu)) {
                if ($request->banjar_adat_tamu) {
                    $tamiu = CacahTamiu::whereIn('tb_cacah_tamiu.banjar_adat_id', $request->banjar_adat_tamu)
                        ->leftJoin('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_tamiu.penduduk_id')
                        ->leftJoin('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->leftJoin('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->leftJoin('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_tamiu.banjar_dinas_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu ?? [''])
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu ?? [''])
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu ?? [''])
                        ->whereIn('tb_cacah_tamiu.status', $request->status_tamu)
                        ->whereIn('tb_cacah_tamiu.banjar_dinas_id', $request->banjar_dinas_tamu)
                        ->whereNotIn('tb_cacah_tamiu.id', $kk_id)
                        ->whereNotNul('tb_cacah_tamiu.tanggal_keluar')
                        ->orderBy('tb_cacah_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                } else {
                    $tamiu = CacahTamiu::where('tb_cacah_tamiu.banjar_adat_id', $banjar_adat_id)
                        ->leftJoin('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_tamiu.penduduk_id')
                        ->leftJoin('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->leftJoin('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->leftJoin('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_tamiu.banjar_dinas_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu ?? [''])
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu ?? [''])
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu ?? [''])
                        ->whereIn('tb_cacah_tamiu.status', $request->status_tamu)
                        ->whereIn('tb_cacah_tamiu.banjar_dinas_id', $request->banjar_dinas_tamu)
                        ->whereNotNul('tb_cacah_tamiu.tanggal_keluar')
                        ->orderBy('tb_cacah_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                }
            } elseif (in_array('1', $request->status_tamu)) {
                if ($request->banjar_adat_tamu) {
                    $tamiu = CacahTamiu::whereIn('tb_cacah_tamiu.banjar_adat_id', $request->banjar_adat_tamu)
                        ->leftJoin('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_tamiu.penduduk_id')
                        ->leftJoin('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->leftJoin('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->leftJoin('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_tamiu.banjar_dinas_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu ?? [''])
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu ?? [''])
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu ?? [''])
                        ->whereIn('tb_cacah_tamiu.status', $request->status_tamu)
                        ->whereIn('tb_cacah_tamiu.banjar_dinas_id', $request->banjar_dinas_tamu)
                        ->whereNotIn('tb_cacah_tamiu.id', $kk_id)
                        ->orderBy('tb_cacah_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                } else {
                    $tamiu = CacahTamiu::where('tb_cacah_tamiu.banjar_adat_id', $banjar_adat_id)
                        ->leftJoin('tb_wna', 'tb_wna.id', 'tb_cacah_tamiu.wna_id')
                        ->leftJoin('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_tamiu.penduduk_id')
                        ->leftJoin('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->leftJoin('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->leftJoin('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_tamiu.banjar_dinas_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamu ?? [''])
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamu ?? [''])
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamu ?? [''])
                        ->whereIn('tb_cacah_tamiu.status', $request->status_tamu)
                        ->whereNotIn('tb_cacah_tamiu.id', $kk_id)
                        ->whereIn('tb_cacah_tamiu.banjar_dinas_id', $request->banjar_dinas_tamu)
                        ->orderBy('tb_cacah_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                }
            }
        }

        if ($request->tgl_lahir_tamu_awal) {
            $tgl_lahir_tamu_awal = date("Y-m-d", strtotime($request->tgl_lahir_tamu_awal));
            $tamiu = $tamiu->where('tanggal_lahir', '>=', $tgl_lahir_tamu_awal);
        }

        if ($request->tgl_lahir_tamu_akhir) {
            $tgl_lahir_tamu_akhir = date("Y-m-d", strtotime($request->tgl_lahir_tamu_akhir));
            $tamiu = $tamiu->where('tanggal_lahir', '<=', $tgl_lahir_tamu_akhir);
        }

        if ($request->tgl_registrasi_tamu_awal) {
            $tgl_regis_tamu_awal = date("Y-m-d", strtotime($request->tgl_registrasi_tamu_awal));
            $tamiu = $tamiu->where('tanggal_masuk', '>=', $tgl_regis_tamu_awal);
        }
        
        if ($request->tgl_registrasi_tamu_akhir) {
            $tgl_regis_tamu_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_tamu_akhir));
            $tamiu = $tamiu->where('tanggal_masuk', '<=', $tgl_regis_tamu_akhir);
        }
        
        return $tamiu;
    }

    public function getGrafikProfesi($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->pekerjaan_tamu as $data) {
            $tamiu[] = $all_data->where('profesi_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikProfesi = [
            'jumlah' => $tamiu,
            'gender' => $gender
        ];

        return $grafikProfesi;
    }

    public function getGrafikPendidikan($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->pendidikan_tamu as $data) {
            $tamiu[] = $all_data->where('pendidikan_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikPendidikan = [
            'jumlah' => $tamiu,
            'gender' => $gender
        ];

        return $grafikPendidikan;
    }

    public function getGrafikGoldar($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->goldar_tamu as $data) {
            $tamiu[] = $all_data->where('golongan_darah', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikGoldar = [
            'jumlah' => $tamiu,
            'gender' => $gender
        ];

        return $grafikGoldar;
    }

    public function getGrafikBanjarDinas($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->banjar_dinas_tamu as $data) {
            $tamiu[] = $all_data->whereIn('banjar_dinas_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikBanjarDinas = [
            'jumlah' => $tamiu,
            'gender' => $gender
        ];

        return $grafikBanjarDinas;
    }
}
