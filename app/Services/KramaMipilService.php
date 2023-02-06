<?php

namespace App\Services;

use App\Models\DesaAdat;
use App\Models\KramaMipil;

class KramaMipilService
{
    public function getAllData($role_id, $request)
    {   //role id ini isinya banjar adat id nya
        // banjar adat mipil ini untuk yg desa adat
        if(count($request->status_mipil) > 1){
            if ($request->banjar_adat_mipil) {
                $kramaMipil = KramaMipil::whereIn('tb_krama_mipil.banjar_adat_id', $request->banjar_adat_mipil)
                ->join('tb_cacah_krama_mipil', 'tb_cacah_krama_mipil.id', 'tb_krama_mipil.cacah_krama_mipil_id')
                ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_mipil.penduduk_id')
                ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_mipil.banjar_adat_id')
                ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC')
                ->where('tb_krama_mipil.status', '1')->orWhere(function ($query) use ($request) {
                    $query->where('tb_krama_mipil.status', '0')
                        ->whereNotNull('tb_krama_mipil.tanggal_nonaktif')
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                        ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC');
                })
                ->get();
            } else {
                $kramaMipil = KramaMipil::where('tb_krama_mipil.banjar_adat_id', $role_id)
                ->join('tb_cacah_krama_mipil', 'tb_cacah_krama_mipil.id', 'tb_krama_mipil.cacah_krama_mipil_id')
                ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_mipil.penduduk_id')
                ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                ->leftJoin('tb_m_tempekan', 'tb_m_tempekan.id', 'tb_cacah_krama_mipil.tempekan_id')
                ->whereIn('tb_cacah_krama_mipil.tempekan_id', $request->tempekan)
                ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC')
                ->where('tb_krama_mipil.status', '1')->orWhere(function ($query) use ($request) {
                    $query->where('tb_krama_mipil.status', '0')
                        ->whereNotNull('tb_krama_mipil.tanggal_nonaktif')
                        ->whereIn('tb_cacah_krama_mipil.tempekan_id', $request->tempekan)
                        ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                        ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                        ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                        ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC');
                })
                ->get();
            }
        } else if(in_array('0', $request->status_mipil)){
            if ($request->banjar_adat_mipil) {
                $kramaMipil = KramaMipil::whereIn('tb_krama_mipil.banjar_adat_id', $request->banjar_adat_mipil)
                ->join('tb_cacah_krama_mipil', 'tb_cacah_krama_mipil.id', 'tb_krama_mipil.cacah_krama_mipil_id')
                ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_mipil.penduduk_id')
                ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_mipil.banjar_adat_id')
                ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                ->where('tb_krama_mipil.status', '0')
                ->whereNotNull('tb_krama_mipil.tanggal_nonaktif')
                ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC')
                ->get();
            } else {
                $kramaMipil = KramaMipil::where('tb_krama_mipil.banjar_adat_id', $role_id)
                ->join('tb_cacah_krama_mipil', 'tb_cacah_krama_mipil.id', 'tb_krama_mipil.cacah_krama_mipil_id')
                ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_mipil.penduduk_id')
                ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                ->leftJoin('tb_m_tempekan', 'tb_m_tempekan.id', 'tb_cacah_krama_mipil.tempekan_id')
                ->whereIn('tb_cacah_krama_mipil.tempekan_id', $request->tempekan)
                ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                ->where('tb_krama_mipil.status', '0')
                ->whereNotNull('tb_krama_mipil.tanggal_nonaktif')
                ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC')
                ->get();
            }
        } else if(in_array('1', $request->status_mipil)){
            if ($request->banjar_adat_mipil) {
                $kramaMipil = KramaMipil::whereIn('tb_krama_mipil.banjar_adat_id', $request->banjar_adat_mipil)
                ->join('tb_cacah_krama_mipil', 'tb_cacah_krama_mipil.id', 'tb_krama_mipil.cacah_krama_mipil_id')
                ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_mipil.penduduk_id')
                ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                ->leftJoin('tb_m_banjar_adat', 'tb_m_banjar_adat.id', 'tb_cacah_krama_mipil.banjar_adat_id')
                ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                ->where('tb_krama_mipil.status', '1')
                ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC')
                ->get();
            } else {
                $kramaMipil = KramaMipil::where('tb_krama_mipil.banjar_adat_id', $role_id)
                ->join('tb_cacah_krama_mipil', 'tb_cacah_krama_mipil.id', 'tb_krama_mipil.cacah_krama_mipil_id')
                ->join('tb_penduduk', 'tb_penduduk.id', 'tb_cacah_krama_mipil.penduduk_id')
                ->join('tb_m_pendidikan', 'tb_m_pendidikan.id', 'tb_penduduk.pendidikan_id')
                ->join('tb_m_profesi', 'tb_m_profesi.id', 'tb_penduduk.profesi_id')
                ->leftJoin('tb_m_tempekan', 'tb_m_tempekan.id', 'tb_cacah_krama_mipil.tempekan_id')
                ->whereIn('tb_cacah_krama_mipil.tempekan_id', $request->tempekan)
                ->whereIn('tb_penduduk.profesi_id', $request->pekerjaan_mipil)
                ->whereIn('tb_penduduk.pendidikan_id', $request->pendidikan_mipil)
                ->whereIn('tb_penduduk.golongan_darah', $request->goldar_mipil)
                ->where('tb_krama_mipil.status', '1')
                ->orderBy('tb_cacah_krama_mipil.tanggal_registrasi', 'DESC')
                ->get();
            }
        }

        if ($request->tgl_lahir_mipil_awal) {
            $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_mipil_awal));
            $kramaMipil = $kramaMipil->where('tanggal_lahir', '>=', $tgl_lahir_awal);
        }

        if ($request->tgl_lahir_mipil_akhir) {
            $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_mipil_akhir));
            $kramaMipil = $kramaMipil->where('tanggal_lahir', '<=', $tgl_lahir_akhir);
        }

        if ($request->tgl_registrasi_mipil_awal) {
            $tgl_regis_awal = date("Y-m-d", strtotime($request->tgl_registrasi_mipil_awal));
            $kramaMipil = $kramaMipil->where('tanggal_registrasi', '>=', $tgl_regis_awal);
        }

        if ($request->tgl_registrasi_mipil_akhir) {
            $tgl_regis_akhir = date("Y-m-d", strtotime($request->tgl_registrasi_mipil_akhir));
            $kramaMipil = $kramaMipil->where('tanggal_registrasi', '<=', $tgl_regis_akhir);
        }

        return $kramaMipil;
    }

    public function getGrafikBanjarAdat($role_id, $request, $gender)
    {
        $all_data = $this->getAllData($role_id, $request);
        foreach ($request->banjar_adat_mipil as $data) {
            $kramaMipil[] = $all_data->whereIn('banjar_adat_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikTempekan = [
            'jumlah' => $kramaMipil,
            'gender' => $gender
        ];

        return $grafikTempekan;
    }

    public function getGrafikTempekan($role_id, $request, $gender)
    {
        $all_data = $this->getAllData($role_id, $request);
        foreach ($request->tempekan as $data) {
            $kramaMipil[] = $all_data->whereIn('tempekan_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikTempekan = [
            'jumlah' => $kramaMipil,
            'gender' => $gender
        ];

        return $grafikTempekan;
    }

    public function getGrafikProfesi($role_id, $request, $gender)
    {
        $all_data = $this->getAllData($role_id, $request);
        foreach ($request->pekerjaan_mipil as $data) {
            $kramaMipil[] = $all_data->where('profesi_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikProfesi = [
            'jumlah' => $kramaMipil,
            'gender' => $gender
        ];

        return $grafikProfesi;
    }

    public function getGrafikPendidikan($role_id, $request, $gender)
    {
        $all_data = $this->getAllData($role_id, $request);
        foreach ($request->pendidikan_mipil as $data) {
            $kramaMipil[] = $all_data->where('pendidikan_id', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikPendidikan = [
            'jumlah' => $kramaMipil,
            'gender' => $gender
        ];

        return $grafikPendidikan;
    }

    public function getGrafikGoldar($role_id, $request, $gender)
    {
        $all_data = $this->getAllData($role_id, $request);
        foreach ($request->goldar_mipil as $data) {
            $kramaMipil[] = $all_data->where('golongan_darah', $data)->where('jenis_kelamin', $gender)->count();
        }

        $grafikGoldar = [
            'jumlah' => $kramaMipil,
            'gender' => $gender
        ];

        return $grafikGoldar;
    }
}
