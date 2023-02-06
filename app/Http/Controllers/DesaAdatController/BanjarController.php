<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KramaMipil;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BanjarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $banjar_dinas = BanjarDinas::with('desa_dinas')->where('desa_adat_id', $desa_adat_id)->get();
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
        return view('pages.desa.banjar.banjar', compact('banjar_adat', 'banjar_dinas', 'kabupatens'));
    }

    //BANJAR ADAT FUNCTION
    public function get_kode_banjar_adat(){
        $desa_adat_id = session()->get('desa_adat_id');
        $last_kode = BanjarAdat::where('desa_adat_id', $desa_adat_id)->where('id', BanjarAdat::where('desa_adat_id', $desa_adat_id)->max('id'))->first();
        if($last_kode == ''){
            $desa_adat = DesaAdat::find($desa_adat_id);
            $last_kode = $desa_adat->desadat_kode;
            $last_kode = $last_kode.'01';
            return response()->json([
                'last_kode' => $last_kode
            ]);
        }
        $last_kode = (int)$last_kode->kode_banjar_adat+1;
        if($last_kode < 100000){
            $last_kode = '0'.$last_kode;
        }
        return response()->json([
            'last_kode' => $last_kode
        ]);
    }

    public function store_banjar_adat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_banjar_adat' => 'required|unique:tb_m_banjar_adat,deleted_at,NULL',
            'nama_banjar_adat' => 'required||regex:/^[a-zA-Z\s]*$/'
        ],[
            'kode_banjar_adat.required' => "Kode Banjar Adat wajib diisi",
            'kode_banjar_adat.unique' => "Kode Banjar Adat yang dimasukkan telah terdaftar",
            'nama_banjar_adat.required' => "Nama Banjar Adat wajib diisi",
            'nama_banjar_adat.regex' => "Nama Banjar Adat yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $banjar_adat = new BanjarAdat();
        $banjar_adat->desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat->kode_banjar_adat = $request->kode_banjar_adat;
        $banjar_adat->nama_banjar_adat = $request->nama_banjar_adat;
        $banjar_adat->save();

        return back()->with('success', 'Banjar Adat berhasil ditambahkan');
    }

    public function edit_banjar_adat($id)
    {
        $banjar_adat = BanjarAdat::find($id);
        return response()->json(['success' => 'Berhasil', 'banjar_adat' => $banjar_adat]);
    }

    public function update_banjar_adat($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit_kode_banjar_adat' => 'required|unique:tb_m_banjar_adat,kode_banjar_adat,deleted_at,NULL',
            'edit_kode_banjar_adat' => [
                Rule::unique('tb_m_banjar_adat', 'kode_banjar_adat')->ignore($id),
            ],
            'edit_nama_banjar_adat' => 'required||regex:/^[a-zA-Z\s]*$/'
        ],[
            'edit_kode_banjar_adat.required' => "Kode Banjar Adat wajib diisi",
            'edit_kode_banjar_adat.unique' => "Kode Banjar Adat yang dimasukkan telah terdaftar",
            'edit_nama_banjar_adat.required' => "Nama Banjar Adat wajib diisi",
            'edit_nama_banjar_adat.regex' => "Nama Banjar Adat yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $banjar_adat = BanjarAdat::find($id);
        $banjar_adat->kode_banjar_adat = $request->edit_kode_banjar_adat;
        $banjar_adat->nama_banjar_adat = $request->edit_nama_banjar_adat;
        $banjar_adat->update();

        return back()->with('success', 'Banjar Adat berhasil diperbaharui');
    }

    public function delete_banjar_adat($id)
    {
        $banjar_adat = BanjarAdat::find($id);
        $banjar_adat->delete();
        return back()->with('success', 'Banjar Adat berhasil dihapus');
    }

    public function get_banjar_adat($id){
        $banjar_adats = BanjarAdat::where('desa_adat_id', $id)->get();
        return response()->json([
            'banjar_adats' => $banjar_adats
        ]);
    }
    //AKHIR BANJAR ADAT FUNCTION

    //BANJAR DINAS FUNCTION
    public function get_kode_banjar_dinas($id){
        $banjar_dinas = BanjarDinas::where('desa_dinas_id', $id)->count();
        $banjar_dinas = $banjar_dinas+1;
        $kode_banjar_dinas = $id;

        if($banjar_dinas<10){
            $kode_banjar_dinas = $kode_banjar_dinas.'0'.$banjar_dinas;
        }else if($banjar_dinas<100){
            $kode_banjar_dinas = $kode_banjar_dinas.''.$banjar_dinas;
        }
        return response()->json([
            'last_kode' => $kode_banjar_dinas
        ]);
    }

    public function store_banjar_dinas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kabupaten_id' => 'required',
            'kecamatan_id' => 'required',
            'desa_dinas_id' => 'required',
            'jenis_banjar_dinas' => 'required',
            'kode_banjar_dinas' => 'required|unique:tb_m_banjar_dinas,deleted_at,NULL',
            'nama_banjar_dinas' => 'required||regex:/^[a-zA-Z\s]*$/'
        ],[
            'kode_banjar_dinas.required' => "Kode Banjar Dinas wajib diisi",
            'kode_banjar_dinas.unique' => "Kode Banjar Dinas yang dimasukkan telah terdaftar",
            'nama_banjar_dinas.required' => "Nama Banjar Dinas wajib diisi",
            'kabupaten_id.required' => "Kabupaten wajib dipilih",
            'kecamatan_id.required' => "Kecamatan wajib dipilih",
            'jenis_banjar_dinas.required' => "Jenis Banjar Dinas wajib dipilih",
            'desa_dinas_id.required' => "Desa/Kelurahan wajib dipilih",
            'nama_banjar_dinas.regex' => "Nama Banjar Dinas yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $banjar_dinas = new BanjarDinas();
        $banjar_dinas->desa_adat_id = session()->get('desa_adat_id');
        $banjar_dinas->desa_dinas_id = $request->desa_dinas_id;
        $banjar_dinas->kode_banjar_dinas = $request->kode_banjar_dinas;
        $banjar_dinas->nama_banjar_dinas = $request->nama_banjar_dinas;
        $banjar_dinas->jenis_banjar_dinas = $request->jenis_banjar_dinas;
        $banjar_dinas->save();

        return back()->with(['success' => 'Banjar Dinas berhasil ditambahkan', 'banjar' => 'dinas']);
    }

    public function edit_banjar_dinas($id)
    {
        $banjar_dinas = BanjarDinas::find($id);
        $desa = DesaDinas::find($banjar_dinas->desa_dinas_id);
        $kecamatan = Kecamatan::where('id', $desa->kecamatan_id)->first();
        $kabupaten = Kabupaten::where('id', $kecamatan->kabupaten_id)->first();
        
        //Data Master
        $kabupatens = Kabupaten::get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();
        return response()->json([
            'success' => 'Berhasil',
            'banjar_dinas' => $banjar_dinas,
            'desa' => $desa,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
            'desas' => $desas,
            'kecamatans' => $kecamatans,
            'kabupatens' => $kabupatens,
        ]);
    }

    public function update_banjar_dinas($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'edit_kabupaten_id' => 'required',
            'edit_kecamatan_id' => 'required',
            'edit_desa_dinas_id' => 'required',
            'edit_jenis_banjar_dinas' => 'required',
            'edit_kode_banjar_dinas' => 'required|unique:tb_m_banjar_dinas,kode_banjar_dinas,deleted_at,NULL',
            'edit_kode_banjar_dinas' => [
                Rule::unique('tb_m_banjar_dinas', 'kode_banjar_dinas')->ignore($id),
            ],
            'edit_nama_banjar_dinas' => 'required||regex:/^[a-zA-Z\s]*$/'
        ],[
            'edit_kabupaten_id.required' => "Kabupaten wajib dipilih",
            'edit_kecamatan_id.required' => "Kecamatan wajib dipilih",
            'edit_desa_dinas_id.required' => "Desa/Kelurahan wajib dipilih",
            'edit_kode_banjar_dinas.required' => "Kode Banjar Dinas wajib diisi",
            'edit_kode_banjar_dinas.unique' => "Kode Banjar Dinas yang dimasukkan telah terdaftar",
            'edit_nama_banjar_dinas.required' => "Nama Banjar Dinas wajib diisi",
            'edit_nama_banjar_dinas.regex' => "Nama Banjar Dinas yang dimasukkan tidak hanya boleh mengandung huruf",
            'edit_enis_banjar_dinas.required' => "Jenis Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $banjar_dinas = BanjarDinas::find($id);
        $banjar_dinas->desa_dinas_id = $request->edit_desa_dinas_id;
        $banjar_dinas->nama_banjar_dinas = $request->edit_nama_banjar_dinas;
        $banjar_dinas->jenis_banjar_dinas = $request->edit_jenis_banjar_dinas;
        $banjar_dinas->update();

        return back()->with(['success' => 'Banjar Dinas berhasil diperbaharui', 'banjar' => 'dinas']);
    }

    public function delete_banjar_dinas($id)
    {
        $banjar_dinas = BanjarDinas::find($id);
        $banjar_dinas->delete();
        return back()->with(['success' => 'Banjar Dinas berhasil dihapus', 'banjar' => 'dinas']);
    }
    //AKHIR BANJAR DINAS FUNCTION
}
