<?php

namespace App\Services;

use App\Helper\Helper;
use App\Models\Perkawinan;
use DateInterval;
use DatePeriod;
use DateTime;

class PerkawinanService
{
    public function getAllData($banjar_adat_id, $request)
    {
        if(is_array($banjar_adat_id)){
            $perkawinan = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')
            ->where('status_perkawinan', '3')->whereIn('jenis_perkawinan', $request->jenis_perkawinan);
            if ($request->tgl_perkawinan_awal) {
                $tgl_perkawinan_awal = date("Y-m-d", strtotime($request->tgl_perkawinan_awal));
                $perkawinan = $perkawinan->where('tanggal_perkawinan', '>=', $tgl_perkawinan_awal);
            }

            if ($request->tgl_perkawinan_akhir) {
                $tgl_perkawinan_akhir = date("Y-m-d", strtotime($request->tgl_perkawinan_akhir));
                $perkawinan = $perkawinan->where('tanggal_perkawinan', '<=', $tgl_perkawinan_akhir);
            }
            $perkawinan->where(function ($query) use ($banjar_adat_id) {
                $query->whereIn('banjar_adat_purusa_id', $banjar_adat_id)
                    ->orWhereIn('banjar_adat_pradana_id', $banjar_adat_id);
            })->orderBy('tanggal_perkawinan', 'DESC');
        }else{
            $perkawinan = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')
            ->where('status_perkawinan', '3')->whereIn('jenis_perkawinan', $request->jenis_perkawinan);
            if ($request->tgl_perkawinan_awal) {
                $tgl_perkawinan_awal = date("Y-m-d", strtotime($request->tgl_perkawinan_awal));
                $perkawinan = $perkawinan->where('tanggal_perkawinan', '>=', $tgl_perkawinan_awal);
            }

            if ($request->tgl_perkawinan_akhir) {
                $tgl_perkawinan_akhir = date("Y-m-d", strtotime($request->tgl_perkawinan_akhir));
                $perkawinan = $perkawinan->where('tanggal_perkawinan', '<=', $tgl_perkawinan_akhir);
            }
            $perkawinan->where(function ($query) use ($banjar_adat_id) {
                $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                    ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
            })->orderBy('tanggal_perkawinan', 'DESC');
        }
        
        return $perkawinan;
    }

    public function getGrafikBulanKawin($banjar_adat_id, $request)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);

        if(is_array($banjar_adat_id)){
            if ($request->tgl_perkawinan_awal) {
                $start = $month = strtotime($request->tgl_perkawinan_awal);
            }else{
                $start = $month = strtotime(Perkawinan::where(function ($query) use ($banjar_adat_id) {
                    $query->whereIn('banjar_adat_purusa_id', $banjar_adat_id)
                        ->orWhereIn('banjar_adat_pradana_id', $banjar_adat_id);
                })->min('tanggal_perkawinan'));
            }
    
            if($request->tgl_perkawinan_akhir){
                $end = strtotime($request->tgl_perkawinan_akhir);
            }else{
                $end = strtotime(Perkawinan::where(function ($query) use ($banjar_adat_id) {
                    $query->whereIn('banjar_adat_purusa_id', $banjar_adat_id)
                        ->orWhereIn('banjar_adat_pradana_id', $banjar_adat_id);
                })->max('tanggal_perkawinan'));
            }
    
            $start    = new DateTime(date('Y-m-d', $start));
            $start->modify('first day of this month');
            $end      = new DateTime(date('Y-m-d', $end));
            $end->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
    
            foreach ($period as $dt) {
                $raw_bulan = $dt->format("m");
                $raw_tahun = $dt->format("Y");
                $perkawinan[] = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->where('status_perkawinan', '3')->whereIn('jenis_perkawinan', $request->jenis_perkawinan)
                ->whereMonth('tanggal_perkawinan', $raw_bulan)->whereYear('tanggal_perkawinan', $raw_tahun)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->whereIn('banjar_adat_purusa_id', $banjar_adat_id)
                        ->orWhereIn('banjar_adat_pradana_id', $banjar_adat_id);
                })->count();
            }
        }else{
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
    
            $start    = new DateTime(date('Y-m-d', $start));
            $start->modify('first day of this month');
            $end      = new DateTime(date('Y-m-d', $end));
            $end->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
    
            foreach ($period as $dt) {
                $raw_bulan = $dt->format("m");
                $raw_tahun = $dt->format("Y");
                $perkawinan[] = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->where('status_perkawinan', '3')->whereIn('jenis_perkawinan', $request->jenis_perkawinan)
                ->whereMonth('tanggal_perkawinan', $raw_bulan)->whereYear('tanggal_perkawinan', $raw_tahun)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                        ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
                })->count();
            }
        }

        $grafikPerkawinan = [
            'jumlah' => $perkawinan,
        ];

        return $grafikPerkawinan;
    }

    public function getGrafikJenisKawin($banjar_adat_id, $request)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);

        if(is_array($banjar_adat_id)){
            foreach($request->jenis_perkawinan as $jenis_perkawinan){
                $kawin = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->where('status_perkawinan', '3')->where('jenis_perkawinan', $jenis_perkawinan)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->whereIn('banjar_adat_purusa_id', $banjar_adat_id)
                        ->orWhereIn('banjar_adat_pradana_id', $banjar_adat_id);
                });
                if(isset($request->tgl_perkawinan_awal)){
                    $tgl_perkawinan_awal = date("Y-m-d", strtotime($request->tgl_perkawinan_awal));
                    $kawin->where('tanggal_perkawinan', '>=', $tgl_perkawinan_awal);
                }
                if(isset($request->tgl_perkawinan_akhir)){
                    $tgl_perkawinan_akhir = date("Y-m-d", strtotime($request->tgl_perkawinan_akhir));
                    $kawin->where('tanggal_perkawinan', '<=', $tgl_perkawinan_akhir);
                }
                $kawin = $kawin->count();
                $perkawinan[] = $kawin;
            }
        }else{
            foreach($request->jenis_perkawinan as $jenis_perkawinan){
                $kawin = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->where('status_perkawinan', '3')->where('jenis_perkawinan', $jenis_perkawinan)
                ->where(function ($query) use ($banjar_adat_id) {
                    $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                        ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
                });
                if(isset($request->tgl_perkawinan_awal)){
                    $tgl_perkawinan_awal = date("Y-m-d", strtotime($request->tgl_perkawinan_awal));
                    $kawin->where('tanggal_perkawinan', '>=', $tgl_perkawinan_awal);
                }
                if(isset($request->tgl_perkawinan_akhir)){
                    $tgl_perkawinan_akhir = date("Y-m-d", strtotime($request->tgl_perkawinan_akhir));
                    $kawin->where('tanggal_perkawinan', '<=', $tgl_perkawinan_akhir);
                }
                $kawin = $kawin->count();
                $perkawinan[] = $kawin;
            }
        }

        $grafikPerkawinan = [
            'jumlah' => $perkawinan,
        ];

        return $grafikPerkawinan;
    }

    public function getGrafikBanjar($banjar_adat_id, $request)
    {
        foreach ($banjar_adat_id as $banjar) {
            $kawin = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->where('status_perkawinan', '3')->whereIn('jenis_perkawinan', $request->jenis_perkawinan)
            ->where(function ($query) use ($banjar) {
                $query->where('banjar_adat_purusa_id', $banjar)
                    ->orWhere('banjar_adat_pradana_id', $banjar);
            });
            
            if(isset($request->tgl_perkawinan_awal)){
                $tgl_perkawinan_awal = date("Y-m-d", strtotime($request->tgl_perkawinan_awal));
                $kawin->where('tanggal_perkawinan', '>=', $tgl_perkawinan_awal);
            }
            if(isset($request->tgl_perkawinan_akhir)){
                $tgl_perkawinan_akhir = date("Y-m-d", strtotime($request->tgl_perkawinan_akhir));
                $kawin->where('tanggal_perkawinan', '<=', $tgl_perkawinan_akhir);
            }
            $kawin = $kawin->count();
            $perkawinan[] = $kawin;
        }

        $grafikPerkawinan = [
            'jumlah' => $perkawinan,
        ];

        return $grafikPerkawinan;
    }
}
