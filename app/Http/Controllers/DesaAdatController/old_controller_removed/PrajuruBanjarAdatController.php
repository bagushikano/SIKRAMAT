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


class PrajuruBanjarAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banjar_adat_id' => 'required',
            'krama_mipil_banjar' => 'required',
            'jabatan_banjar' => 'required',
            'tanggal_mulai_menjabat_banjar' => 'required',
            'tanggal_akhir_menjabat_banjar' => 'required',
            'email_banjar' => 'required|email|unique:tb_user,email',
        ],[
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'krama_mipil_banjar.required' => "Krama Mipil wajib dipilih",
            'jabatan_banjar.required' => "Jabatan wajib dipilih",
            'tanggal_mulai_menjabat_banjar.required' => "Tahun Mulai Jabatan wajib diisi",
            'tanggal_akhir_menjabat_banjar.required' => "Tahun Akhir Jabatan wajib diisi",
            'email_banjar.required' => "Email Prajuru Desa Adat wajib diisi",
            'email_banjar.unique' => "Email yang dimasukkan telah terdaftar"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $krama_mipil = KramaMipil::find($request->krama_mipil_banjar);
        $cacah_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);

        $user = new User();
        $user->email = $request->email_banjar;
        $user->password = bcrypt($krama_mipil->nomor_krama_mipil);
        $user->role = $request->jabatan_banjar;
        $user->save();

        $prajuru_banjar_adat = new PrajuruBanjarAdat();
        $prajuru_banjar_adat->user_id = $user->id;
        $prajuru_banjar_adat->krama_mipil_id = $request->krama_mipil_banjar;
        $prajuru_banjar_adat->banjar_adat_id = $request->banjar_adat_id;
        $prajuru_banjar_adat->jabatan = $request->jabatan_banjar;
        $prajuru_banjar_adat->tanggal_mulai_menjabat = date("Y-m-d", strtotime($request->tanggal_mulai_menjabat_banjar));
        $prajuru_banjar_adat->tanggal_akhir_menjabat = date("Y-m-d", strtotime($request->tanggal_akhir_menjabat_banjar));
        $prajuru_banjar_adat->status_prajuru_banjar_adat = '1';
        $prajuru_banjar_adat->save();

        return back()->with(['success' => 'Prajuru Banjar Adat Berhasil Ditambahkan', 'prajuru' => 'banjar']);
    }

    public function edit($id)
    {
        $prajuru_banjar = PrajuruBanjarAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->find($id);
        $user = User::find($prajuru_banjar->user_id);
        $prajuru_banjar->tanggal_mulai_menjabat = date("d-m-Y", strtotime($prajuru_banjar->tanggal_mulai_menjabat));
        $prajuru_banjar->tanggal_akhir_menjabat = date("d-m-Y", strtotime($prajuru_banjar->tanggal_akhir_menjabat));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $prajuru_banjar->krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;
        return response()->json([
            'prajuru_banjar' => $prajuru_banjar,
            'user' => $user
        ]);
    }

    public function update($id, Request $request)
    {
        $prajuru_banjar_adat = PrajuruBanjarAdat::find($id);
        $user = User::find($prajuru_banjar_adat->user_id);

        $validator = Validator::make($request->all(), [
            'edit_banjar_adat_id' => 'required',
            'edit_krama_mipil_banjar' => 'required',
            'edit_jabatan_banjar' => 'required',
            'edit_tanggal_mulai_menjabat_banjar' => 'required',
            'edit_tanggal_akhir_menjabat_banjar' => 'required',
            'edit_email_banjar' => 'required|email|unique:tb_user,email',
            'edit_email_banjar' => [
                Rule::unique('tb_user', 'email')->ignore($user->id),
            ],
        ],[
            'edit_banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'edit_krama_mipil_banjar.required' => "Krama Mipil wajib dipilih",
            'edit_jabatan_banjar.required' => "Jabatan wajib dipilih",
            'edit_tanggal_mulai_menjabat_banjar.required' => "Tahun Mulai Jabatan wajib diisi",
            'edit_tanggal_akhir_menjabat_banjar.required' => "Tahun Akhir Jabatan wajib diisi",
            'edit_email_banjar.required' => "Email Prajuru Desa Adat wajib diisi",
            'edit_email_banjar.unique' => "Email yang dimasukkan telah terdaftar"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $krama_mipil = KramaMipil::find($request->edit_krama_mipil_banjar);

        $user->email = $request->edit_email_banjar;
        if($request->reset_password_banjar){
            $user->password = bcrypt($krama_mipil->nomor_krama_mipil);
        }
        $user->role = $request->edit_jabatan_banjar;
        $user->update();

        $prajuru_banjar_adat->krama_mipil_id = $request->edit_krama_mipil_banjar;
        $prajuru_banjar_adat->banjar_adat_id = $request->edit_banjar_adat_id;
        $prajuru_banjar_adat->jabatan = $request->edit_jabatan_banjar;
        $prajuru_banjar_adat->tanggal_mulai_menjabat = date("Y-m-d", strtotime($request->edit_tanggal_mulai_menjabat_banjar));
        $prajuru_banjar_adat->tanggal_akhir_menjabat = date("Y-m-d", strtotime($request->edit_tanggal_akhir_menjabat_banjar));
        $prajuru_banjar_adat->status_prajuru_banjar_adat = $request->edit_status_prajuru_banjar;
        $prajuru_banjar_adat->update();

        return back()->with(['success' => 'Prajuru Banjar Adat Berhasil Diperbaharui', 'prajuru' => 'banjar']);
    }

    public function destroy($id)
    {
        $prajuru_banjar_adat = PrajuruBanjarAdat::find($id);
        $user = User::find($prajuru_banjar_adat->user_id);
        $user->delete();
        $prajuru_banjar_adat->delete();
        return back()->with(['success' => 'Prajuru Banjar Adat Berhasil Dihapus', 'prajuru' => 'banjar']);
    }

    public function filter($status)
    {
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        if($status == 'menjabat'){ 
            $prajuru_banjar_adat = PrajuruBanjarAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->whereIn('banjar_adat_id', $banjar_adat_id)->where('status_prajuru_banjar_adat', '1')->orderBy('jabatan', 'ASC')->get();
            $hasil = view('pages.desa.prajuru.filter_prajuru_banjar', ['prajuru_banjar_adat' => $prajuru_banjar_adat])->render();
            return response()->json(['success' => 'Prajuru difilter', 'hasil' => $hasil]);
        }else if($status == 'purna'){
            $prajuru_banjar_adat = PrajuruBanjarAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->whereIn('banjar_adat_id', $banjar_adat_id)->where('status_prajuru_banjar_adat', '0')->orderBy('jabatan', 'ASC')->get();
            $hasil = view('pages.desa.prajuru.filter_prajuru_banjar', ['prajuru_banjar_adat' => $prajuru_banjar_adat])->render();
            return response()->json(['success' => 'Prajuru difilter', 'hasil' => $hasil]);
        }
    }

    public function get_krama_mipil($banjar_adat_id){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $arr_prajuru_desa_id = PrajuruDesaAdat::where('desa_adat_id', $desa_adat_id)->pluck('krama_mipil_id')->toArray();
        $arr_prajuru_banjar_id = PrajuruBanjarAdat::whereIn('banjar_adat_id', $arr_banjar_adat_id)->pluck('krama_mipil_id')->toArray();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->whereNotIn('id', $arr_prajuru_desa_id)->whereNotIn('id', $arr_prajuru_banjar_id)->get();
        $hasil = view('pages.desa.prajuru.list_krama_mipil_banjar', ['krama_mipil' => $krama_mipil])->render();
        return response()->json(['hasil' => $hasil]);
    }

    public function get_krama_mipil_edit($banjar_adat_id){
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $arr_prajuru_desa_id = PrajuruDesaAdat::where('desa_adat_id', $desa_adat_id)->pluck('krama_mipil_id')->toArray();
        $arr_prajuru_banjar_id = PrajuruBanjarAdat::whereIn('banjar_adat_id', $arr_banjar_adat_id)->pluck('krama_mipil_id')->toArray();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->whereNotIn('id', $arr_prajuru_desa_id)->whereNotIn('id', $arr_prajuru_banjar_id)->get();
        $hasil = view('pages.desa.prajuru.list_krama_mipil_banjar_edit', ['krama_mipil' => $krama_mipil])->render();
        return response()->json(['hasil' => $hasil]);
    }
}
