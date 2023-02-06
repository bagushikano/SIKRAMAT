<?php

namespace App\Services;

use App\Helper\Helper;
use App\Models\Kelahiran;
use DateInterval;
use DatePeriod;
use DateTime;

class KelahiranService
{
    public function getAllData($banjar_adat_id, $request)
    {
        if(is_array($banjar_adat_id)){
            $kelahiran = Kelahiran::with('cacah_krama_mipil.penduduk.ayah')
            ->with('cacah_krama_mipil.penduduk.ibu')
            ->whereIn('banjar_adat_id', $banjar_adat_id)->where('status', '1');
            if ($request->tgl_lahir_awal) {
                $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_awal));
                $kelahiran = $kelahiran->where('tanggal_lahir', '>=', $tgl_lahir_awal);
            }
    
            if ($request->tgl_lahir_akhir) {
                $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_akhir));
                $kelahiran = $kelahiran->where('tanggal_lahir', '<=', $tgl_lahir_akhir);
            }
            
            return $kelahiran;
        }else{
            $kelahiran = Kelahiran::with('cacah_krama_mipil.penduduk.ayah')->with('cacah_krama_mipil.penduduk.ibu')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1');
            if ($request->tgl_lahir_awal) {
                $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_awal));
                $kelahiran = $kelahiran->where('tanggal_lahir', '>=', $tgl_lahir_awal);
            }
    
            if ($request->tgl_lahir_akhir) {
                $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_akhir));
                $kelahiran = $kelahiran->where('tanggal_lahir', '<=', $tgl_lahir_akhir);
            }
            
            return $kelahiran;
        }
    }

    public function getGrafikBulanLahir($banjar_adat_id, $request, $gender)
    {
        $all_data = $this->getAllData($banjar_adat_id, $request);

        if(is_array($banjar_adat_id)){
            if ($request->tgl_lahir_awal) {
                $start = $month = strtotime($request->tgl_lahir_awal);
            }else{
                $start = $month = strtotime(Kelahiran::whereIn('banjar_adat_id', $banjar_adat_id)->min('tanggal_lahir'));
            }
    
            if($request->tgl_lahir_akhir){
                $end = strtotime($request->tgl_lahir_akhir);
            }else{
                $end = strtotime(Kelahiran::whereIn('banjar_adat_id', $banjar_adat_id)->max('tanggal_lahir'));
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
                $kelahiran[] = Kelahiran::with('cacah_krama_mipil.penduduk')
                ->whereIn('banjar_adat_id', $banjar_adat_id)->where('status', '1')
                ->whereMonth('tanggal_lahir', $raw_bulan)->whereYear('tanggal_lahir', $raw_tahun)->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($gender) {
                    return $query->where('jenis_kelamin', $gender);
                })->count();
            }
        }else{
            if ($request->tgl_lahir_awal) {
                $start = $month = strtotime($request->tgl_lahir_awal);
            }else{
                $start = $month = strtotime(Kelahiran::where('banjar_adat_id', $banjar_adat_id)->min('tanggal_lahir'));
            }
    
            if($request->tgl_lahir_akhir){
                $end = strtotime($request->tgl_lahir_akhir);
            }else{
                $end = strtotime(Kelahiran::where('banjar_adat_id', $banjar_adat_id)->max('tanggal_lahir'));
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
                $kelahiran[] = Kelahiran::with('cacah_krama_mipil.penduduk')
                ->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')
                ->whereMonth('tanggal_lahir', $raw_bulan)->whereYear('tanggal_lahir', $raw_tahun)->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($gender) {
                    return $query->where('jenis_kelamin', $gender);
                })->count();
            }
        }

        $grafikKelahiran = [
            'jumlah' => $kelahiran,
            'gender' => $gender
        ];

        return $grafikKelahiran;
    }

    public function getGrafikBanjar($banjar_adat_id, $request, $gender)
    {
        foreach ($banjar_adat_id as $banjar) {
            $lahir = Kelahiran::with('cacah_krama_mipil.penduduk')
            ->where('banjar_adat_id', $banjar)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($gender) {
                return $query->where('jenis_kelamin', $gender);
            });
            if(isset($request->tgl_lahir_awal)){
                $tgl_lahir_awal = date("Y-m-d", strtotime($request->tgl_lahir_awal));
                $lahir->where('tanggal_lahir', '>=', $tgl_lahir_awal);
            }
            if(isset($request->tgl_lahir_akhir)){
                $tgl_lahir_akhir = date("Y-m-d", strtotime($request->tgl_lahir_akhir));
                $lahir->where('tanggal_lahir', '<=', $tgl_lahir_akhir);
            }
            $lahir = $lahir->count();
            $kelahiran[] = $lahir;
        }

        $grafikKelahiran = [
            'jumlah' => $kelahiran,
            'gender' => $gender
        ];

        return $grafikKelahiran;
    }
}
