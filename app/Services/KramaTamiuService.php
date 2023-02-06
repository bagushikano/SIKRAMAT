<?php

namespace App\Services;

use App\Models\KramaTamiu;

class KramaTamiuService
{
    public function getAllData($banjar_adat_id, $request)
    {
        if (count($request->status_tamiu) > 1){
            if ($request->banjar_adat_tamiu) {
                $kramaTamiu = KramaTamiu::whereIn('tb_krama_tamiu.banjar_adat_id', $request->banjar_adat_tamiu)
                    ->join('tb_cacah_krama_tamiu', 'tb_cacah_krama_tamiu.id', 'tb_krama_tamiu.cacah_krama_tamiu_id')
                    ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_tamiu.penduduk_id')
                    ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                    ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                    ->join('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_krama_tamiu.banjar_dinas_id')
                    ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_tamiu.banjar_adat_id')
                    ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                    ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                    ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                    ->whereIn('tb_cacah_krama_tamiu.asal', $request->asal)
                    ->whereIn('tb_cacah_krama_tamiu.banjar_dinas_id', $request->banjar_dinas_tamiu)
                    ->where('tb_krama_tamiu.status', '1')->orWhere(function ($query) use ($request) {
                        $query->where('tb_krama_tamiu.status', '0')
                            ->whereNotNull('tb_krama_tamiu.tanggal_nonaktif')
                            ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                            ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                            ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                            ->orderBy('tb_cacah_krama_tamiu.tanggal_registrasi', 'DESC');
                    })
                    ->orderBy('tb_cacah_krama_tamiu.tanggal_masuk', 'DESC')
                    ->get();
            } else {
                $kramaTamiu = KramaTamiu::where('tb_krama_tamiu.banjar_adat_id', $banjar_adat_id)
                    ->join('tb_cacah_krama_tamiu', 'tb_cacah_krama_tamiu.id', 'tb_krama_tamiu.cacah_krama_tamiu_id')
                    ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_tamiu.penduduk_id')
                    ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                    ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                    ->join('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_krama_tamiu.banjar_dinas_id')
                    ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_tamiu.banjar_adat_id')
                    ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                    ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                    ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                    ->whereIn('tb_cacah_krama_tamiu.asal', $request->asal)
                    ->whereIn('tb_cacah_krama_tamiu.banjar_dinas_id', $request->banjar_dinas_tamiu)
                    ->where('tb_krama_tamiu.status', '1')->orWhere(function ($query) use ($request) {
                        $query->where('tb_krama_tamiu.status', '0')
                            ->whereNotNull('tb_krama_tamiu.tanggal_nonaktif')
                            ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                            ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                            ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                            ->orderBy('tb_cacah_krama_tamiu.tanggal_registrasi', 'DESC');
                    })
                    ->orderBy('tb_cacah_krama_tamiu.tanggal_masuk', 'DESC')
                    ->get();
            }
            
        } else {
            if (in_array('0', $request->status_tamiu)) {
                if ($request->banjar_adat_tamiu) {
                    $kramaTamiu = KramaTamiu::whereIn('tb_krama_tamiu.banjar_adat_id', $request->banjar_adat_tamiu)
                        ->join('tb_cacah_krama_tamiu', 'tb_cacah_krama_tamiu.id', 'tb_krama_tamiu.cacah_krama_tamiu_id')
                        ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_tamiu.penduduk_id')
                        ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->join('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_krama_tamiu.banjar_dinas_id')
                        ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_tamiu.banjar_adat_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.asal', $request->asal)
                        ->whereIn('tb_krama_tamiu.status', $request->status_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.banjar_dinas_id', $request->banjar_dinas_tamiu)
                        ->whereNotNull('tb_krama_tamiu.tanggal_nonaktif')
                        ->orderBy('tb_cacah_krama_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                } else {
                    $kramaTamiu = KramaTamiu::where('tb_krama_tamiu.banjar_adat_id', $banjar_adat_id)
                        ->join('tb_cacah_krama_tamiu', 'tb_cacah_krama_tamiu.id', 'tb_krama_tamiu.cacah_krama_tamiu_id')
                        ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_tamiu.penduduk_id')
                        ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->join('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_krama_tamiu.banjar_dinas_id')
                        ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_tamiu.banjar_adat_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.asal', $request->asal)
                        ->whereIn('tb_krama_tamiu.status', $request->status_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.banjar_dinas_id', $request->banjar_dinas_tamiu)
                        ->whereNotNull('tb_krama_tamiu.tanggal_nonaktif')
                        ->orderBy('tb_cacah_krama_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                }
            } elseif (in_array('1', $request->status_tamiu)) {
                if ($request->banjar_adat_tamiu) {
                    $kramaTamiu = KramaTamiu::whereIn('tb_krama_tamiu.banjar_adat_id', $request->banjar_adat_tamiu)
                        ->join('tb_cacah_krama_tamiu', 'tb_cacah_krama_tamiu.id', 'tb_krama_tamiu.cacah_krama_tamiu_id')
                        ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_tamiu.penduduk_id')
                        ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->join('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_krama_tamiu.banjar_dinas_id')
                        ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_tamiu.banjar_adat_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.asal', $request->asal)
                        ->whereIn('tb_krama_tamiu.status', $request->status_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.banjar_dinas_id', $request->banjar_dinas_tamiu)
                        ->orderBy('tb_cacah_krama_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                } else {
                    $kramaTamiu = KramaTamiu::where('tb_krama_tamiu.banjar_adat_id', $banjar_adat_id)
                        ->join('tb_cacah_krama_tamiu', 'tb_cacah_krama_tamiu.id', 'tb_krama_tamiu.cacah_krama_tamiu_id')
                        ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_tamiu.penduduk_id')
                        ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                        ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                        ->join('tb_m_banjar_dinas', 'tb_m_banjar_dinas.id', 'tb_cacah_krama_tamiu.banjar_dinas_id')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_tamiu)
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_tamiu)
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.asal', $request->asal)
                        ->whereIn('tb_krama_tamiu.status', $request->status_tamiu)
                        ->whereIn('tb_cacah_krama_tamiu.banjar_dinas_id', $request->banjar_dinas_tamiu)
                        ->orderBy('tb_cacah_krama_tamiu.tanggal_masuk', 'DESC')
                        ->get();
                }
            }
        }

        if ($request->tgl_lahir_tamiu_awal) {
            $tgl_lahir_tamiu_awal = date("Y-m-d", strtotime($request->tgl_lahir_tamiu_awal));
            $kramaTamiu = $kramaTamiu->where('tanggal_lahir', '>=', $tgl_lahir_tamiu_awal);
        }

        if ($request->tgl_lahir_tamiu_akhir) {
            $tgl_lahir_tamiu_akhir = date("Y-m-d", strtotime($request->tgl_lahir_tamiu_akhir));
            $kramaTamiu = $kramaTamiu->where('tanggal_lahir', '<=', $tgl_lahir_tamiu_akhir);
        }

        if ($request->tgl_registrasi_tamiu_awal) {
            $tgl_regis_tamiu_awal = date("Y-m-d", strtotime($request->tgl_registrasi_tamiu_awal));
            $kramaTamiu = $kramaTamiu->where('tanggal_registrasi', '>=', $tgl_regis_tamiu_awal);
        }
        
        if ($request->tgl_registrasi_tamiu_akhir) {
            $tgl_regis_tamiu_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_tamiu_akhir));
            $kramaTamiu = $kramaTamiu->where('tanggal_registrasi', '<=', $tgl_regis_tamiu_akhir);
        }
        
        return $kramaTamiu;
    }

    public function getGrafikProfesi($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->pekerjaan_tamiu as $data) {
            $kramaTamiu[] = $all_data->where('profesi_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikProfesi = [
            'jumlah' => $kramaTamiu,
            'gender' => $gender
        ];

        return $grafikProfesi;
    }

    public function getGrafikPendidikan($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->pendidikan_tamiu as $data) {
            $kramaTamiu[] = $all_data->where('pendidikan_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikPendidikan = [
            'jumlah' => $kramaTamiu,
            'gender' => $gender
        ];

        return $grafikPendidikan;
    }

    public function getGrafikGoldar($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->goldar_tamiu as $data) {
            $kramaTamiu[] = $all_data->where('golongan_darah', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikGoldar = [
            'jumlah' => $kramaTamiu,
            'gender' => $gender
        ];

        return $grafikGoldar;
    }

    public function getGrafikAsal($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->asal as $data) {
            $kramaTamiu[] = $all_data->whereIn('asal', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikAsal = [
            'jumlah' => $kramaTamiu,
            'gender' => $gender
        ];

        return $grafikAsal;
    }

    public function getGrafikBanjarDinas($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        foreach ($request->banjar_dinas_tamiu as $data) {
            $kramaTamiu[] = $all_data->whereIn('banjar_dinas_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikBanjarDinas = [
            'jumlah' => $kramaTamiu,
            'gender' => $gender
        ];

        return $grafikBanjarDinas;
    }
}
