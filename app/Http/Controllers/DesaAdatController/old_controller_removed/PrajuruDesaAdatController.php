<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AdminDesaAdat;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KramaMipil;
use App\Models\Penduduk;
use App\Models\PrajuruBanjarAdat;
use App\Models\PrajuruDesaAdat;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PrajuruDesaAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search_krama(Request $request)
    {
        $penduduk = Penduduk::where('nik', $request->input('term', ''))->orWhere('nomor_induk_krama', $request->input('term', ''))->first();
        $krama = KramaMipil::where('penduduk_id', $penduduk->id)->first();
        $response = array();
        if($krama){
            $prajuru_desa = PrajuruDesaAdat::where('krama_mipil_id', $krama->id)->first();
            $prajuru_banjar = PrajuruBanjarAdat::where('krama_mipil_id', $krama->id)->first();
            if($prajuru_desa){
                return ['results' => $response];
            }else if($prajuru_banjar){
                return ['results' => $response];
            }else{
                $response[] = array(
                    "id"=>$krama->id,
                    "text"=>$penduduk->nik.' - '.$penduduk->nama
                );
                return ['results' => $response];
            }
        }else{
            return ['results' => $response];
        }
    }

    public function index(Request $request)
    {
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();

        $prajuru_desa_adat = PrajuruDesaAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->where('desa_adat_id', $desa_adat_id)->where('status_prajuru_desa_adat', '1')->orderBy('jabatan', 'ASC')->get();
        $banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $prajuru_banjar_adat = PrajuruBanjarAdat::whereIn('banjar_adat_id', $banjar_adat_id)->where('status_prajuru_banjar_adat', '1')->get();
        return view('pages.desa.prajuru.prajuru', compact('prajuru_desa_adat', 'prajuru_banjar_adat', 'banjar_adat'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'krama_mipil' => 'required',
            'jabatan' => 'required',
            'tanggal_mulai_menjabat' => 'required',
            'tanggal_akhir_menjabat' => 'required',
            'email' => 'required|email|unique:tb_user',
        ],[
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'jabatan.required' => "Jabatan wajib dipilih",
            'tanggal_mulai_menjabat.required' => "Tahun Mulai Jabatan wajib diisi",
            'tanggal_akhir_menjabat.required' => "Tahun Akhir Jabatan wajib diisi",
            'email.required' => "Email Prajuru Desa Adat wajib diisi",
            'email.unique' => "Email yang dimasukkan telah terdaftar"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $desa_adat_id = session()->get('desa_adat_id');

        $krama_mipil = KramaMipil::find($request->krama_mipil);
        $cacah_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($krama_mipil->nomor_krama_mipil);
        $user->role = $request->jabatan;
        $user->save();

        $prajuru_desa_adat = new PrajuruDesaAdat();
        $prajuru_desa_adat->user_id = $user->id;
        $prajuru_desa_adat->krama_mipil_id = $request->krama_mipil;
        $prajuru_desa_adat->desa_adat_id = $desa_adat_id;
        $prajuru_desa_adat->jabatan = $request->jabatan;
        $prajuru_desa_adat->tanggal_mulai_menjabat = date("Y-m-d", strtotime($request->tanggal_mulai_menjabat));
        $prajuru_desa_adat->tanggal_akhir_menjabat = date("Y-m-d", strtotime($request->tanggal_akhir_menjabat));
        $prajuru_desa_adat->status_prajuru_desa_adat = '1';
        $prajuru_desa_adat->save();

        return back()->with('success', 'Prajuru Desa Adat Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $prajuru_desa = PrajuruDesaAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->find($id);
        $user = User::find($prajuru_desa->user_id);
        $prajuru_desa->tanggal_mulai_menjabat = date("d-m-Y", strtotime($prajuru_desa->tanggal_mulai_menjabat));
        $prajuru_desa->tanggal_akhir_menjabat = date("d-m-Y", strtotime($prajuru_desa->tanggal_akhir_menjabat));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $prajuru_desa->krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;
        return response()->json([
            'prajuru_desa' => $prajuru_desa,
            'user' => $user
        ]);
    }

    public function update($id, Request $request)
    {
        $prajuru_desa_adat = PrajuruDesaAdat::find($id);
        $user = User::find($prajuru_desa_adat->user_id);

        $validator = Validator::make($request->all(), [
            'edit_krama_mipil' => 'required',
            'edit_jabatan' => 'required',
            'edit_tanggal_mulai_menjabat' => 'required',
            'edit_tanggal_akhir_menjabat' => 'required',
            'edit_email' => 'required|email|unique:tb_user,email',
            'edit_email' => [
                Rule::unique('tb_user', 'email')->ignore($user->id),
            ],
        ],[
            'edit_krama_mipil.required' => "Krama Mipil wajib dipilih",
            'edit_jabatan.required' => "Jabatan wajib dipilih",
            'edit_tanggal_mulai_menjabat.required' => "Tahun Mulai Jabatan wajib diisi",
            'edit_tanggal_akhir_menjabat.required' => "Tahun Akhir Jabatan wajib diisi",
            'edit_email.required' => "Email Prajuru Desa Adat wajib diisi",
            'edit_email.unique' => "Email yang dimasukkan telah terdaftar"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $krama_mipil = KramaMipil::find($request->edit_krama_mipil);

        $user->email = $request->edit_email;
        $user->role = $request->edit_jabatan;
        if($request->reset_password){
            $user->password = bcrypt($krama_mipil->nomor_krama_mipil);
        }
        $user->update();

        $prajuru_desa_adat->krama_mipil_id = $request->edit_krama_mipil;
        $prajuru_desa_adat->jabatan = $request->edit_jabatan;
        $prajuru_desa_adat->tanggal_mulai_menjabat = date("Y-m-d", strtotime($request->edit_tanggal_mulai_menjabat));
        $prajuru_desa_adat->tanggal_akhir_menjabat = date("Y-m-d", strtotime($request->edit_tanggal_akhir_menjabat));
        $prajuru_desa_adat->status_prajuru_desa_adat = $request->edit_status_prajuru;
        $prajuru_desa_adat->update();

        return back()->with('success', 'Prajuru Desa Adat berhasil diperbaharui');
    }

    public function destroy($id)
    {
        $prajuru_desa_adat = PrajuruDesaAdat::find($id);
        $user = User::find($prajuru_desa_adat->user_id);
        $user->delete();
        $prajuru_desa_adat->delete();
        return back()->with('success', 'Prajuru Desa Adat berhasil dihapus');
    }

    public function filter($status)
    {
        if($status == 'menjabat'){
            $desa_adat_id = session()->get('desa_adat_id');
            $prajuru_desa_adat = PrajuruDesaAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->where('desa_adat_id', $desa_adat_id)->where('status_prajuru_desa_adat', '1')->orderBy('jabatan', 'ASC')->get();
            $hasil = view('pages.desa.prajuru.filter_prajuru_desa', ['prajuru_desa_adat' => $prajuru_desa_adat])->render();
            return response()->json(['success' => 'Prajuru difilter', 'hasil' => $hasil]);
        }else if($status == 'purna'){
            $desa_adat_id = session()->get('desa_adat_id');
            $prajuru_desa_adat = PrajuruDesaAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->where('desa_adat_id', $desa_adat_id)->where('status_prajuru_desa_adat', '0')->orderBy('jabatan', 'ASC')->get();
            $hasil = view('pages.desa.prajuru.filter_prajuru_desa', ['prajuru_desa_adat' => $prajuru_desa_adat])->render();
            return response()->json(['success' => 'Prajuru difilter', 'hasil' => $hasil]);
        }
    }

    public function get_krama_mipil(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $arr_prajuru_desa_id = PrajuruDesaAdat::where('desa_adat_id', $desa_adat_id)->pluck('krama_mipil_id')->toArray();
        $arr_prajuru_banjar_id = PrajuruBanjarAdat::whereIn('banjar_adat_id', $arr_banjar_adat_id)->pluck('krama_mipil_id')->toArray();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')->whereIn('banjar_adat_id', $arr_banjar_adat_id)->whereNotIn('id', $arr_prajuru_desa_id)->whereNotIn('id', $arr_prajuru_banjar_id)->get();
        $hasil = view('pages.desa.prajuru.list_krama_mipil', ['krama_mipil' => $krama_mipil])->render();
        return response()->json(['hasil' => $hasil]);
    }

    public function get_krama_mipil_edit(){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $arr_prajuru_desa_id = PrajuruDesaAdat::where('desa_adat_id', $desa_adat_id)->pluck('krama_mipil_id')->toArray();
        $arr_prajuru_banjar_id = PrajuruBanjarAdat::whereIn('banjar_adat_id', $arr_banjar_adat_id)->pluck('krama_mipil_id')->toArray();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')->whereIn('banjar_adat_id', $arr_banjar_adat_id)->whereNotIn('id', $arr_prajuru_desa_id)->whereNotIn('id', $arr_prajuru_banjar_id)->get();
        $hasil = view('pages.desa.prajuru.list_krama_mipil_edit', ['krama_mipil' => $krama_mipil])->render();
        return response()->json(['hasil' => $hasil]);
    }
}
