<?php

namespace App\Helper;

use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\Penduduk;
use Carbon\Carbon;
use DateTime;
use PhpParser\Node\Expr\FuncCall;

class Helper
{
    public static function convert_date_to_locale_id($date){
        $converted_date = Carbon::parse($date)->locale('id');
        $converted_date->settings(['formatFunction' => 'translatedFormat']);
        $converted_date = $converted_date->format('j F Y');
        return $converted_date;
    }

    public static function convert_month_to_locale_id($date){
        if (is_int($date)) {
            $converted_date = Carbon::parse(date('Y-m', $date))->locale('id');
        }else{
            $converted_date = Carbon::parse($date)->locale('id');
        }
        $converted_date->settings(['formatFunction' => 'translatedFormat']);
        $converted_date = $converted_date->format('F Y');
        return $converted_date;
    }

    public static function generate_nama_krama_mipil($krama_mipil){
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != NULL){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        return $krama_mipil;
    }

    public static function generate_nama_cacah_krama_mipil($cacah_krama_mipil){
        $nama = '';
        if($cacah_krama_mipil->penduduk->gelar_depan != NULL){
            $nama = $nama.$cacah_krama_mipil->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$cacah_krama_mipil->penduduk->nama;
        if($cacah_krama_mipil->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $cacah_krama_mipil->penduduk->nama = $nama;

        return $cacah_krama_mipil;
    }

    public static function generate_nama_collection_cacah_krama_mipil($collection){
        foreach($collection as $cacah_krama_mipil){
            $nama = '';
            if($cacah_krama_mipil->penduduk->gelar_depan != NULL){
                $nama = $nama.$cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$cacah_krama_mipil->penduduk->nama;
            if($cacah_krama_mipil->penduduk->gelar_belakang != NULL){
                $nama = $nama.', '.$cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $cacah_krama_mipil->penduduk->nama = $nama;
        }

        return $collection;
    }

    public static function generate_nama_anggota_keluarga_krama_mipil($anggota_krama_mipil){
        foreach($anggota_krama_mipil as $item){
            $nama = '';
            if($item->cacah_krama_mipil->penduduk->gelar_depan != NULL){
                $nama = $nama.$item->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$item->cacah_krama_mipil->penduduk->nama;
            if($item->cacah_krama_mipil->penduduk->gelar_belakang != NULL){
                $nama = $nama.', '.$item->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $item->cacah_krama_mipil->penduduk->nama = $nama;
        }

        return $anggota_krama_mipil;
    }

    public static function generate_nomor_cacah_krama_mipil($penduduk_id, $banjar_adat_id){
        $penduduk = Penduduk::find($penduduk_id);
        
        //NOMOR CACAH KRAMA MIPIL
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $nomor_cacah_krama_mipil = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'01'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.$jumlah_penduduk_tanggal_sama;
        }

        return $nomor_cacah_krama_mipil;
    }
}