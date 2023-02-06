<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\Tempekan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TempekanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $tempekans = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        return view('pages.banjar.tempekan.tempekan', compact('tempekans'));
    }

    public function get_kode_tempekan(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $last_kode = Tempekan::where('banjar_adat_id', $banjar_adat_id)->where('id', Tempekan::where('banjar_adat_id', $banjar_adat_id)->max('id'))->first();
        if($last_kode == ''){
            $banjar_adat = BanjarAdat::find($banjar_adat_id);
            $last_kode = $banjar_adat->kode_banjar_adat;
            $last_kode = $last_kode.'01';
            return response()->json([
                'last_kode' => $last_kode
            ]);
        }
        $last_kode = (int)$last_kode->kode_tempekan+1;
        $last_kode = '0'.$last_kode;
        return response()->json([
            'last_kode' => $last_kode
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'kode_tempekan' => 'required|unique:tb_m_tempekan,deleted_at,NULL',
            'nama_tempekan' => 'required||regex:/^[a-zA-Z\s]*$/'
        ],[
            'kode_tempekan.required' => "Kode Tempekan wajib diisi",
            'kode_tempekan.unique' => "Kode Tempekan yang dimasukkan telah terdaftar",
            'nama_tempekan.required' => "Nama Tempekan wajib diisi",
            'nama_tempekan.regex' => "Nama Tempekan yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $tempekan = new Tempekan();
        $tempekan->banjar_adat_id = session()->get('banjar_adat_id');
        $tempekan->kode_tempekan = $request->kode_tempekan;
        $tempekan->nama_tempekan = $request->nama_tempekan;
        $tempekan->save();

        return back()->with('success', 'Tempekan berhasil ditambahkan');
    }

    public function edit($id){
        $tempekan = Tempekan::find($id);
        return response()->json([
            'tempekan' => $tempekan
        ]);
    }

    public function update($id, Request $request){
        $validator = Validator::make($request->all(), [
            'edit_kode_tempekan' => 'required|unique:tb_m_tempekan,deleted_at,NULL',
            'edit_nama_tempekan' => 'required||regex:/^[a-zA-Z\s]*$/'
        ],[
            'edit_kode_tempekan.required' => "Kode Tempekan wajib diisi",
            'edit_kode_tempekan.unique' => "Kode Tempekan yang dimasukkan telah terdaftar",
            'edit_nama_tempekan.required' => "Nama Tempekan wajib diisi",
            'edit_nama_tempekan.regex' => "Nama Tempekan yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $tempekan = Tempekan::find($id);
        $tempekan->banjar_adat_id = session()->get('banjar_adat_id');
        $tempekan->kode_tempekan = $request->edit_kode_tempekan;
        $tempekan->nama_tempekan = $request->edit_nama_tempekan;
        $tempekan->save();

        return back()->with('success', 'Tempekan berhasil diperbaharui');
    }

    public function destroy($id){
        $tempekan = Tempekan::find($id);
        $tempekan->delete();
        return back()->with('success', 'Tempekan berhasil dihapus');
    }
}
