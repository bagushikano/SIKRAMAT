<?php

namespace App\Services;

use App\Helper\Helper;
use App\Models\Perceraian;
use DateInterval;
use DatePeriod;
use DateTime;

class PerceraianService
{
    public function getAllData($banjar_adat_id, $request)
    {
        if(is_array($banjar_adat_id)){
            $perceraian = Perceraian::with('purusa.penduduk')->with('pradana.penduduk')->whereIn('banjar_adat_purusa_id', $banjar_adat_id)->where('status_perceraian', '3');
            if ($request->tgl_perceraian_awal) {
                $tgl_perceraian_awal = date("Y-m-d", strtotime($request->tgl_perceraian_awal));
                $perceraian = $perceraian->where('tanggal_perceraian', '>=', $tgl_perceraian_awal);
            }

            if ($request->tgl_perceraian_akhir) {
                $tgl_perceraian_akhir = date("Y-m-d", strtotime($request->tgl_perceraian_akhir));
                $perceraian = $perceraian->where('tanggal_perceraian', '<=', $tgl_perceraian_akhir);
            }
        }else{
            $perceraian = Perceraian::with('purusa.penduduk')->with('pradana.penduduk')->where('banjar_adat_purusa_id', $banjar_adat_id)->where('status_perceraian', '3');
            if ($request->tgl_perceraian_awal) {
                $tgl_perceraian_awal = date("Y-m-d", strtotime($request->tgl_perceraian_awal));
                $perceraian = $perceraian->where('tanggal_perceraian', '>=', $tgl_perceraian_awal);
            }

            if ($request->tgl_perceraian_akhir) {
                $tgl_perceraian_akhir = date("Y-m-d", strtotime($request->tgl_perceraian_akhir));
                $perceraian = $perceraian->where('tanggal_perceraian', '<=', $tgl_perceraian_akhir);
            }
        }
        
        return $perceraian;
    }

    public function getGrafikBulanCerai($banjar_adat_id, $request)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);

        if(is_array($banjar_adat_id)){
            if ($request->tgl_perceraian_awal) {
                $start = $month = strtotime($request->tgl_perceraian_awal);
            }else{
                $start = $month = strtotime(Perceraian::whereIn('banjar_adat_purusa_id', $banjar_adat_id)->min('tanggal_perceraian'));
            }
    
            if($request->tgl_perceraian_akhir){
                $end = strtotime($request->tgl_perceraian_akhir);
            }else{
                $end = strtotime(Perceraian::whereIn('banjar_adat_purusa_id', $banjar_adat_id)->max('tanggal_perceraian'));
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
                $perceraian[] = Perceraian::with('purusa.penduduk')->with('pradana.penduduk')
                ->whereIn('banjar_adat_purusa_id', $banjar_adat_id)->where('status_perceraian', '3')
                ->whereMonth('tanggal_perceraian', $raw_bulan)->whereYear('tanggal_perceraian', $raw_tahun)->count();
            }
        }else{
            if ($request->tgl_perceraian_awal) {
                $start = $month = strtotime($request->tgl_perceraian_awal);
            }else{
                $start = $month = strtotime(Perceraian::where('banjar_adat_purusa_id', $banjar_adat_id)->min('tanggal_perceraian'));
            }
    
            if($request->tgl_perceraian_akhir){
                $end = strtotime($request->tgl_perceraian_akhir);
            }else{
                $end = strtotime(Perceraian::where('banjar_adat_purusa_id', $banjar_adat_id)->max('tanggal_perceraian'));
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
                $perceraian[] = Perceraian::with('purusa.penduduk')->with('pradana.penduduk')
                ->where('banjar_adat_purusa_id', $banjar_adat_id)->where('status_perceraian', '3')
                ->whereMonth('tanggal_perceraian', $raw_bulan)->whereYear('tanggal_perceraian', $raw_tahun)->count();
            }
        }

        $grafikPerceraian = [
            'jumlah' => $perceraian,
        ];

        return $grafikPerceraian;
    }

    public function getGrafikBanjar($banjar_adat_id, $request)
    {
        foreach ($banjar_adat_id as $banjar) {
            $cerai = Perceraian::with('purusa.penduduk')->with('pradana.penduduk')
            ->where('banjar_adat_purusa_id', $banjar)->where('status_perceraian', '3');
            
            if(isset($request->tgl_perceraian_awal)){
                $tgl_perceraian_awal = date("Y-m-d", strtotime($request->tgl_perceraian_awal));
                $cerai->where('tanggal_perceraian', '>=', $tgl_perceraian_awal);
            }
            if(isset($request->tgl_perceraian_akhir)){
                $tgl_perceraian_akhir = date("Y-m-d", strtotime($request->tgl_perceraian_akhir));
                $cerai->where('tanggal_perceraian', '<=', $tgl_perceraian_akhir);
            }
            $cerai = $cerai->count();
            $perceraian[] = $cerai;
        }

        $grafikPerceraian = [
            'jumlah' => $perceraian,
        ];

        return $grafikPerceraian;
    }
}
