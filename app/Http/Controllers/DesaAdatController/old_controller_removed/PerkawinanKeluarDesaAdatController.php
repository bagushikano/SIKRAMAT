<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Perkawinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class PerkawinanKeluarDesaAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $perkawinans = Perkawinan::with('purusa', 'pradana')->where('desa_adat_pradana_id', $desa_adat_id)->where('desa_adat_purusa_id', '!=', $desa_adat_id)->get();
        foreach($perkawinans as $perkawinan){
            //SET NAMA LENGKAP PURUSA
            $nama = '';
            if($perkawinan->purusa->penduduk->gelar_depan != ''){
                $nama = $nama.$perkawinan->purusa->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->purusa->penduduk->nama;
            if($perkawinan->purusa->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->purusa->penduduk->gelar_belakang;
            }
            $perkawinan->purusa->penduduk->nama = $nama;

            //SET NAMA LENGKAP PRADANA
            $nama = '';
            if($perkawinan->pradana->penduduk->gelar_depan != ''){
                $nama = $nama.$perkawinan->pradana->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->pradana->penduduk->nama;
            if($perkawinan->pradana->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->pradana->penduduk->gelar_belakang;
            }
            $perkawinan->pradana->penduduk->nama = $nama;

            //SET TANGGAL PERKAWINAN DD-MMM-YYYY
            $perkawinan->tanggal_perkawinan = date('d M Y', strtotime($perkawinan->tanggal_perkawinan));
        }
        return view('pages.desa.perkawinan_keluar_desa_adat.perkawinan_keluar_desa_adat', compact('perkawinans'));
    }

    public function detail($id){
        $perkawinan = Perkawinan::find($id);

        //GET DETAIL DATA PERKAWINAN
        //SET FORMAT TANGGAL
        $perkawinan->tanggal_perkawinan = date('d M Y', strtotime($perkawinan->tanggal_perkawinan));

        //SET NAMA LENGKAP PURUSA
        $nama = '';
        if($perkawinan->purusa->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->purusa->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->purusa->penduduk->nama;
        if($perkawinan->purusa->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->purusa->penduduk->gelar_belakang;
        }
        $perkawinan->purusa->penduduk->nama = $nama;

        //SET NAMA LENGKAP PRADANA
        $nama = '';
        if($perkawinan->pradana->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->pradana->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->pradana->penduduk->nama;
        if($perkawinan->pradana->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->pradana->penduduk->gelar_belakang;
        }
        $perkawinan->pradana->penduduk->nama = $nama;

        //GET DATA ASAL PURUSA
        $banjar_adat_purusa = BanjarAdat::find($perkawinan->banjar_adat_purusa_id);
        $desa_adat_purusa = DesaAdat::find($perkawinan->desa_adat_purusa_id);
        $kecamatan_purusa = Kecamatan::find($desa_adat_purusa->kecamatan_id);
        $kabupaten_purusa = Kabupaten::find($kecamatan_purusa->kabupaten_id);
        
        //GET BANJAR PRADANA
        $banjar_adat_pradana = BanjarAdat::find($perkawinan->banjar_adat_pradana_id);
        return view('pages.desa.perkawinan_keluar_desa_adat.detail', compact('perkawinan', 'banjar_adat_purusa', 'desa_adat_purusa', 'kecamatan_purusa', 'kabupaten_purusa', 'banjar_adat_pradana'));
    }
}
