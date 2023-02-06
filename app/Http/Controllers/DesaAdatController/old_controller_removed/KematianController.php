<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluargaKrama;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\KeluargaKrama;
use App\Models\Kematian;
use App\Models\KramaMipil;
use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KematianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $kematians = Kematian::with('cacah_krama_mipil.penduduk')->whereIn('banjar_adat_id', $arr_banjar_id)->get();
        return view('pages.desa.kematian.kematian', compact('kematians'));
    }

    public function get_cacah_krama_mipil(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $kramas = CacahKramaMipil::with('penduduk')->whereIn('banjar_adat_id', $arr_banjar_adat_id)->get();
        $hasil = view('pages.desa.kematian.list_cacah_krama_mipil', ['kramas' => $kramas])->render();
        return response()->json(['hasil' => $hasil]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_akta_kematian' => 'required|unique:tb_kematian',
            'tanggal_kematian' => 'required',
            'cacah_krama_mipil' => 'required',
            'penyebab_kematian' => 'required',
            'lampiran' => 'required'
        ],[
            'nomor_akta_kematian.required' => "Nomor Akta Kematian / Surat Keterangan Kematian wajib diisi",
            'kematian.unique' => "Nomor Akta Kematian yang dimasukkan telah terdaftar",
            'tanggal_kematian.required' => "Tanggal Kematian wajib diisi",
            'cacah_krama_mipil.required' => "Cacah Krama wajib dipilih",
            'penyebab_kematian.required' => "Penyebab Kematian wajib diisi",
            'lampiran.required' => "Lampiran wajib diisi"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET DATA YANG MENINGGAL
        $cacah_krama_mipil = CacahKramaMipil::find($request->cacah_krama_mipil);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        //INSERT KEMATIAN
        $kematian = new Kematian();
        $kematian->nomor_akta_kematian = $request->nomor_akta_kematian;
        $kematian->cacah_krama_mipil_id = $request->cacah_krama_mipil;
        $kematian->banjar_adat_id = $cacah_krama_mipil->banjar_adat_id;
        $kematian->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
        $kematian->penyebab_kematian = $request->penyebab_kematian;
        if($request->file('lampiran')!=""){
            $file = $request->file('lampiran');
            $fileLocation = '/file/kematian/'.$request->nomor_akta_kematian.'/lampiran';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $kematian->lampiran = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $kematian->save();

        //UPDATE STATUS PENDUDUK
        $penduduk->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
        $penduduk->update();

        return redirect()->route('desa-kematian-home')->with('success', 'Kematian berhasil ditambahkan');
    }

    public function edit($id){
        $kematian = Kematian::with('cacah_krama_mipil.penduduk')->find($id);
        $kematian->tanggal_kematian = date("d-m-Y", strtotime($kematian->tanggal_kematian));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($kematian->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$kematian->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$kematian->cacah_krama_mipil->penduduk->nama;
        if($kematian->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$kematian->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $kematian->cacah_krama_mipil->penduduk->nama = $nama;
        return response()->json([
            'kematian' => $kematian
        ]);
    }

    public function get_cacah_krama_mipil_edit(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $kramas = CacahKramaMipil::with('penduduk')->whereIn('banjar_adat_id', $arr_banjar_adat_id)->get();
        $hasil = view('pages.desa.kematian.list_cacah_krama_mipil_edit', ['kramas' => $kramas])->render();
        return response()->json(['hasil' => $hasil]);
    }
    
    public function update($id, Request $request){
        //GET DATA HISTORY KEMATIAN
        $kematian = Kematian::find($id);

        $validator = Validator::make($request->all(), [
            'edit_nomor_akta_kematian' => 'required|unique:tb_kematian,nomor_akta_kematian',
            'edit_nomor_akta_kematian' => [
                Rule::unique('tb_kematian', 'nomor_akta_kematian')->ignore($kematian->id),
            ],
            'edit_tanggal_kematian' => 'required',
            'edit_penyebab_kematian' => 'required'
        ],[
            'edit_nomor_akta_kematian.required' => "Nomor Akta Kematian / Surat Keterangan Kematian wajib diisi",
            'edit_nomor_akta_kematian.unique' => "Nomor Akta Kematian yang dimasukkan telah terdaftar",
            'edit_tanggal_kematian.required' => "Tanggal Kematian wajib diisi",
            'edit_penyebab_kematian.required' => "Penyebab Kematian wajib diisi"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //UPDATE KEMATIAN
        $kematian->nomor_akta_kematian = $request->edit_nomor_akta_kematian;
        $kematian->tanggal_kematian = date("Y-m-d", strtotime($request->edit_tanggal_kematian));
        $kematian->penyebab_kematian = $request->edit_penyebab_kematian;
        if($request->file('edit_lampiran')!=""){
            $file = $request->file('lampiran');
            $fileLocation = '/file/kematian/'.$request->edit_nomor_akta_kematian.'/lampiran';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $kematian->lampiran = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $kematian->update();

        //GET DATA YANG MENINGGAL
        $cacah_krama_mipil = CacahKramaMipil::find($kematian->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        //UPDATE STATUS PENDUDUK
        $penduduk->tanggal_kematian = date("Y-m-d", strtotime($request->edit_tanggal_kematian));
        $penduduk->update();

        return redirect()->route('desa-kematian-home')->with('success', 'Kematian berhasil diperbaharui');
    }

    public function detail($id){
        $kematian = Kematian::with('cacah_krama_mipil.penduduk')->find($id);
        $kematian->tanggal_kematian = date("d-m-Y", strtotime($kematian->tanggal_kematian));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($kematian->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$kematian->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$kematian->cacah_krama_mipil->penduduk->nama;
        if($kematian->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$kematian->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $kematian->cacah_krama_mipil->penduduk->nama = $nama;
        return response()->json([
            'kematian' => $kematian
        ]);
    }
}
