<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\Kabupaten;
use App\Models\Perkawinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class PerkawinanDalamDesaAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $perkawinans = Perkawinan::with('purusa', 'pradana')->where('desa_adat_purusa_id', $desa_adat_id)->where('desa_adat_pradana_id', $desa_adat_id)->get();
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
        return view('pages.desa.perkawinan_dalam_desa_adat.perkawinan_dalam_desa_adat', compact('perkawinans'));
    }

    public function get_purusa($banjar_adat_id){
        $kramas = CacahKramaMipil::with('penduduk')->where('banjar_adat_id', $banjar_adat_id)->get();
        $hasil = view('pages.desa.perkawinan_dalam_desa_adat.list_purusa', ['kramas' => $kramas])->render();
        return response()->json(['hasil' => $hasil]);
    }

    public function get_pradana($banjar_adat_id){
        $kramas = CacahKramaMipil::with('penduduk')->where('banjar_adat_id', $banjar_adat_id)->get();
        $hasil = view('pages.desa.perkawinan_dalam_desa_adat.list_pradana', ['kramas' => $kramas])->render();
        return response()->json(['hasil' => $hasil]);
    }

    public function create(){
        $desa_adat_id = session()->get('desa_adat_id');

        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
        return view('pages.desa.perkawinan_dalam_desa_adat.create', compact('banjar_adat', 'kabupatens'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required',
            'banjar_adat_purusa' => 'required',
            'banjar_adat_pradana' => 'required',
            'purusa' => 'required',
            'pradana' => 'required',
            'lampiran' => 'required',
        ],[
            'nomor_bukti_serah_terima_perkawinan' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'banjar_adat_purusa.required' => "Banjar Adat Purusa wajib dipilih",
            'banjar_adat_pradana.required' => "Banjar Adat Pradana wajib dipilih",
            'purusa.required' => "Purusa wajib dipilih",
            'pradana.required' => "Pradana wajib dipilih",
            'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //FIND DESA ADAT
        $banjar_adat_purusa = BanjarAdat::find($request->banjar_adat_purusa);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        $banjar_adat_pradana = BanjarAdat::find($request->banjar_adat_pradana);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        $perkawinan = new Perkawinan();
        $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
        $perkawinan->purusa_id = $request->purusa;
        $perkawinan->pradana_id = $request->pradana;
        $perkawinan->banjar_adat_purusa_id = $request->banjar_adat_purusa;
        $perkawinan->banjar_adat_pradana_id = $request->banjar_adat_pradana;
        $perkawinan->desa_adat_purusa_id = $desa_adat_purusa->id;
        $perkawinan->desa_adat_pradana_id = $desa_adat_pradana->id;
        $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
        $perkawinan->status_perkawinan = '1';
        $perkawinan->approval_purusa = '1';
        $perkawinan->approval_pradana = '1';

        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);
        if($request->file('lampiran')!=""){
            $file = $request->file('lampiran');
            $fileLocation = '/file/perkawinan/'.$convert_nomor_perkawinan.'/lampiran';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perkawinan->lampiran = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $perkawinan->save();

        //GENERATE NOMOR PERKAWINAN PRK-0325-01-130222-001 PRK-DESAPURUSA-BANJAR-TGL-DIGIT UNIK
        return redirect()->route('desa-perkawinan-dalam-desa-adat-home')->with('success', 'Perkawinan berhasil ditambahkan');
    }
}
