<?php

namespace App\Services;

use App\Helper\Helper;
use App\Models\Kematian;
use DateInterval;
use DatePeriod;
use DateTime;

class KematianService
{
    public function getAllData($banjar_adat_id, $request)
    {
        if(is_array($banjar_adat_id)){
            $kematian = Kematian::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $banjar_adat_id)->where('status', '1');
            if ($request->tgl_kematian_awal) {
                $tgl_kematian_awal = date("Y-m-d", strtotime($request->tgl_kematian_awal));
                $kematian = $kematian->where('tanggal_kematian', '>=', $tgl_kematian_awal);
            }
    
            if ($request->tgl_kematian_akhir) {
                $tgl_kematian_akhir = date("Y-m-d", strtotime($request->tgl_kematian_akhir));
                $kematian = $kematian->where('tanggal_kematian', '<=', $tgl_kematian_akhir);
            }
            
            return $kematian;
        }else{
            $kematian = Kematian::with('cacah_krama_mipil.penduduk')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1');
            if ($request->tgl_kematian_awal) {
                $tgl_kematian_awal = date("Y-m-d", strtotime($request->tgl_kematian_awal));
                $kematian = $kematian->where('tanggal_kematian', '>=', $tgl_kematian_awal);
            }
    
            if ($request->tgl_kematian_akhir) {
                $tgl_kematian_akhir = date("Y-m-d", strtotime($request->tgl_kematian_akhir));
                $kematian = $kematian->where('tanggal_kematian', '<=', $tgl_kematian_akhir);
            }
            
            return $kematian;
        }
    }

    public function getGrafikBulanMati($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);
        if(is_array($banjar_adat_id)){
            if ($request->tgl_kematian_awal) {
                $start = $month = strtotime($request->tgl_kematian_awal);
            }else{
                $start = $month = strtotime(Kematian::whereIn('banjar_adat_id', $banjar_adat_id)->min('tanggal_kematian'));
            }
    
            if($request->tgl_kematian_akhir){
                $end = strtotime($request->tgl_kematian_akhir);
            }else{
                $end = strtotime(Kematian::whereIn('banjar_adat_id', $banjar_adat_id)->max('tanggal_kematian'));
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
                $kematian[] = Kematian::with('cacah_krama_mipil.penduduk')
                ->whereIn('banjar_adat_id', $banjar_adat_id)->where('status', '1')
                ->whereMonth('tanggal_kematian', $raw_bulan)->whereYear('tanggal_kematian', $raw_tahun)->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($gender) {
                    return $query->where('jenis_kelamin', $gender);
                })->count();
            }
        }else{
            if ($request->tgl_kematian_awal) {
                $start = $month = strtotime($request->tgl_kematian_awal);
            }else{
                $start = $month = strtotime(Kematian::where('banjar_adat_id', $banjar_adat_id)->min('tanggal_kematian'));
            }
    
            if($request->tgl_kematian_akhir){
                $end = strtotime($request->tgl_kematian_akhir);
            }else{
                $end = strtotime(Kematian::where('banjar_adat_id', $banjar_adat_id)->max('tanggal_kematian'));
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
                $kematian[] = Kematian::with('cacah_krama_mipil.penduduk')
                ->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')
                ->whereMonth('tanggal_kematian', $raw_bulan)->whereYear('tanggal_kematian', $raw_tahun)->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($gender) {
                    return $query->where('jenis_kelamin', $gender);
                })->count();
            }
        }

        $grafikKematian = [
            'jumlah' => $kematian,
            'gender' => $gender
        ];

        return $grafikKematian;
    }

    public function getGrafikBanjar($banjar_adat_id, $request, $gender)
    {
        foreach ($banjar_adat_id as $banjar) {
            $mati = Kematian::with('cacah_krama_mipil.penduduk')
            ->where('banjar_adat_id', $banjar)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($gender) {
                return $query->where('jenis_kelamin', $gender);
            });
            if(isset($request->tgl_kematian_awal)){
                $tgl_kematian_awal = date("Y-m-d", strtotime($request->tgl_kematian_awal));
                $mati->where('tanggal_kematian', '>=', $tgl_kematian_awal);
            }
            if(isset($request->tgl_kematian_akhir)){
                $tgl_kematian_akhir = date("Y-m-d", strtotime($request->tgl_kematian_akhir));
                $mati->where('tanggal_kematian', '<=', $tgl_kematian_akhir);
            }
            $mati = $mati->count();
            $kematian[] = $mati;
        }

        $grafikKematian = [
            'jumlah' => $kematian,
            'gender' => $gender
        ];

        return $grafikKematian;
    }
}
