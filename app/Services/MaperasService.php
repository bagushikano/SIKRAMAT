<?php

namespace App\Services;

use App\Helper\Helper;
use App\Models\Maperas;
use DateInterval;
use DatePeriod;
use DateTime;

class MaperasService
{
    public function getAllData($banjar_adat_id, $request)
    {
        if(is_array($banjar_adat_id)){
            $maperas = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')
            ->with('ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru')
            ->where('status_maperas', '3')->whereIn('jenis_maperas', $request->jenis_maperas);
            if ($request->tgl_maperas_awal) {
                $tgl_maperas_awal = date("Y-m-d", strtotime($request->tgl_maperas_awal));
                $maperas = $maperas->where('tanggal_maperas', '>=', $tgl_maperas_awal);
            }

            if ($request->tgl_maperas_akhir) {
                $tgl_maperas_akhir = date("Y-m-d", strtotime($request->tgl_maperas_akhir));
                $maperas = $maperas->where('tanggal_maperas', '<=', $tgl_maperas_akhir);
            }
            $maperas->where(function ($query) use ($banjar_adat_id) {
                $query->whereIn('banjar_adat_lama_id', $banjar_adat_id)
                    ->orWhereIn('banjar_adat_baru_id', $banjar_adat_id);
            })->orderBy('tanggal_maperas', 'DESC');
        }else{
            $maperas = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')
            ->with('ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru')
            ->where('status_maperas', '3')->whereIn('jenis_maperas', $request->jenis_maperas);
            if ($request->tgl_maperas_awal) {
                $tgl_maperas_awal = date("Y-m-d", strtotime($request->tgl_maperas_awal));
                $maperas = $maperas->where('tanggal_maperas', '>=', $tgl_maperas_awal);
            }

            if ($request->tgl_maperas_akhir) {
                $tgl_maperas_akhir = date("Y-m-d", strtotime($request->tgl_maperas_akhir));
                $maperas = $maperas->where('tanggal_maperas', '<=', $tgl_maperas_akhir);
            }
            $maperas->where(function ($query) use ($banjar_adat_id) {
                $query->where('banjar_adat_lama_id', $banjar_adat_id)
                    ->orWhere('banjar_adat_baru_id', $banjar_adat_id);
            })->orderBy('tanggal_maperas', 'DESC');
        }
        
        return $maperas;
    }

    public function getGrafikBulanMaperas($banjar_adat_id, $request)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);

        if(is_array($banjar_adat_id)){
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
    
            $start    = new DateTime(date('Y-m-d', $start));
            $start->modify('first day of this month');
            $end      = new DateTime(date('Y-m-d', $end));
            $end->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
    
            foreach ($period as $dt) {
                // echo $dt->format("Y-m") . "<br>\n";
                $raw_bulan = $dt->format("m");
                $raw_tahun = $dt->format("Y");
                $maperas[] = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')
                ->with('ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru')
                ->where('status_maperas', '3')->whereIn('jenis_maperas', $request->jenis_maperas)
                ->whereMonth('tanggal_maperas', $raw_bulan)->whereYear('tanggal_maperas', $raw_tahun)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->whereIn('banjar_adat_lama_id', $banjar_adat_id)
                        ->orWhereIn('banjar_adat_baru_id', $banjar_adat_id);
                })->count();
            }
        }else{
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
    
            $start    = new DateTime(date('Y-m-d', $start));
            $start->modify('first day of this month');
            $end      = new DateTime(date('Y-m-d', $end));
            $end->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
    
            foreach ($period as $dt) {
                // echo $dt->format("Y-m") . "<br>\n";
                $raw_bulan = $dt->format("m");
                $raw_tahun = $dt->format("Y");
                $maperas[] = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')
                ->with('ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru')
                ->where('status_maperas', '3')->whereIn('jenis_maperas', $request->jenis_maperas)
                ->whereMonth('tanggal_maperas', $raw_bulan)->whereYear('tanggal_maperas', $raw_tahun)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->where('banjar_adat_lama_id', $banjar_adat_id)
                        ->orWhere('banjar_adat_baru_id', $banjar_adat_id);
                })->count();
            }
        }
        
        $grafikMaperas = [
            'jumlah' => $maperas,
        ];

        return $grafikMaperas;
    }

    public function getGrafikJenisMaperas($banjar_adat_id, $request)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        if(is_array($banjar_adat_id)){
            foreach($request->jenis_maperas as $jenis_maperas){
                $peras = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')
                ->with('ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru')
                ->where('status_maperas', '3')->whereIn('jenis_maperas', $request->jenis_maperas)
                ->where('jenis_maperas', $jenis_maperas)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->whereIn('banjar_adat_lama_id', $banjar_adat_id)
                        ->orWhereIn('banjar_adat_baru_id', $banjar_adat_id);
                });
                if(isset($request->tgl_maperas_awal)){
                    $tgl_maperas_awal = date("Y-m-d", strtotime($request->tgl_maperas_awal));
                    $peras->where('tanggal_maperas', '>=', $tgl_maperas_awal);
                }
                if(isset($request->tgl_maperas_akhir)){
                    $tgl_maperas_akhir = date("Y-m-d", strtotime($request->tgl_maperas_akhir));
                    $peras->where('tanggal_maperas', '<=', $tgl_maperas_akhir);
                }
                $peras = $peras->count();
                $maperas[] = $peras;
            }
        }else{
            foreach($request->jenis_maperas as $jenis_maperas){
                $peras = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')
                ->with('ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru')
                ->where('status_maperas', '3')->whereIn('jenis_maperas', $request->jenis_maperas)
                ->where('jenis_maperas', $jenis_maperas)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->where('banjar_adat_lama_id', $banjar_adat_id)
                        ->orWhere('banjar_adat_baru_id', $banjar_adat_id);
                });
                if(isset($request->tgl_maperas_awal)){
                    $tgl_maperas_awal = date("Y-m-d", strtotime($request->tgl_maperas_awal));
                    $peras->where('tanggal_maperas', '>=', $tgl_maperas_awal);
                }
                if(isset($request->tgl_maperas_akhir)){
                    $tgl_maperas_akhir = date("Y-m-d", strtotime($request->tgl_maperas_akhir));
                    $peras->where('tanggal_maperas', '<=', $tgl_maperas_akhir);
                }
                $peras = $peras->count();
                $maperas[] = $peras;
            }
        }
        
        $grafikMaperas = [
            'jumlah' => $maperas,
        ];

        return $grafikMaperas;
    }

    public function getGrafikBanjar($banjar_adat_id, $request)
    {
        foreach ($banjar_adat_id as $banjar) {
            $peras = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')
                ->with('ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru')
                ->where('status_maperas', '3')->whereIn('jenis_maperas', $request->jenis_maperas)
                ->where('jenis_maperas', $request->jenis_maperas)
                ->where(function ($query) use ($banjar) {
                    $query->where('banjar_adat_lama_id', $banjar)
                        ->orWhere('banjar_adat_baru_id', $banjar);
                });
                if(isset($request->tgl_maperas_awal)){
                    $tgl_maperas_awal = date("Y-m-d", strtotime($request->tgl_maperas_awal));
                    $peras->where('tanggal_maperas', '>=', $tgl_maperas_awal);
                }
                if(isset($request->tgl_maperas_akhir)){
                    $tgl_maperas_akhir = date("Y-m-d", strtotime($request->tgl_maperas_akhir));
                    $peras->where('tanggal_maperas', '<=', $tgl_maperas_akhir);
                }
                $peras = $peras->count();
                $maperas[] = $peras;
        }

        $grafikMaperas = [
            'jumlah' => $maperas,
        ];

        return $grafikMaperas;
    }
}
