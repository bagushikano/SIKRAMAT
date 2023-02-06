<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AkunKrama;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\KramaMipil;
use App\Models\Penduduk;
use App\Models\PrajuruBanjarAdat;
use App\Models\PrajuruDesaAdat;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PrajuruDesaAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $desa_adat_id = session()->get('desa_adat_id');
        $desa_adat = DesaAdat::find($desa_adat_id);
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();

        $prajuru_desa_adat = PrajuruDesaAdat::with('krama_mipil.cacah_krama_mipil.penduduk', 'krama_mipil.cacah_krama_mipil.banjar_adat', 'user')->where('desa_adat_id', $desa_adat_id);

        if(isset($request->status)){
            if($request->status == '1'){
                $prajuru_desa_adat->where('status', '1');
            }else if($request->status == '0'){
                $prajuru_desa_adat->where('status', '0');
            }
        }else{
            $prajuru_desa_adat->where('status', '1');
        }

        if(isset($request->rentang_waktu)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $prajuru_desa_adat->whereBetween('tanggal_mulai_menjabat', [$start_date, $end_date])->whereBetween('tanggal_akhir_menjabat', [$start_date, $end_date]);
        }
        
        return DataTables::of($prajuru_desa_adat)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $return = '';
                if($data->status == '0'){
                    $return .= '<button class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="detail_prajuru('.$data->id.')"><i class="fas fa-eye"></i></button>';
                }else{
                    $return .= '<button class="btn btn-primary btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" onclick="detail_prajuru('.$data->id.')"><i class="fas fa-eye"></i></button>';
                    $return .= '<button class="btn btn-warning btn-sm mx-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit_prajuru('.$data->id.')"><i class="fas fa-edit"></i></button>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_prajuru('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }

    public function datatable_krama_mipil(Request $request){
        $desa_adat_id = session()->get('desa_adat_id');
        $desa_adat = DesaAdat::find($desa_adat_id);
        $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();

        $arr_prajuru_desa_adat_id = PrajuruDesaAdat::where('desa_adat_id', $desa_adat->id)->pluck('krama_mipil_id')->toArray();
        $arr_prajuru_banjar_adat_id = PrajuruBanjarAdat::whereIn('banjar_adat_id', $arr_banjar_adat_id)->pluck('krama_mipil_id')->toArray();
        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->whereNotIn('id', $arr_prajuru_desa_adat_id)->whereNotIn('id', $arr_prajuru_banjar_adat_id)->where('status', '1');

        if(isset($request->banjar_adat_id)){
            $kramas->where('banjar_adat_id', $request->banjar_adat_id);
        }else{
            $kramas->whereIn('banjar_adat_id', $arr_banjar_adat_id);
        }

        return Datatables::of($kramas)
        ->addIndexColumn()
        ->addColumn('link', function ($data) {
            $nama = '';
            if($data->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$data->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$data->cacah_krama_mipil->penduduk->nama;
            if($data->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$data->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_mipil('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function datatable_krama_mipil_edit(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $arr_prajuru_desa_adat_id = PrajuruDesaAdat::where('desa_adat_id', $desa_adat->id)->pluck('krama_mipil_id')->toArray();
        $arr_prajuru_banjar_adat_id = PrajuruBanjarAdat::where('banjar_adat_id', $banjar_adat_id)->pluck('krama_mipil_id')->toArray();
        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->whereNotIn('id', $arr_prajuru_desa_adat_id)->whereNotIn('id', $arr_prajuru_banjar_adat_id)->where('status', '1')->get();
        return Datatables::of($kramas)
        ->addIndexColumn()
        ->addColumn('link', function ($data) {
            $nama = '';
            if($data->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$data->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$data->cacah_krama_mipil->penduduk->nama;
            if($data->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$data->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="edit_pilih_krama_mipil('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function index(Request $request)
    {
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.prajuru.prajuru', compact('banjar_adat'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'krama_mipil' => 'required',
            'jabatan' => 'required',
            'tanggal_mulai_menjabat' => 'required',
            'tanggal_akhir_menjabat' => 'required',
            'email' => 'required|email|unique:tb_user',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ],[
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'jabatan.required' => "Jabatan wajib dipilih",
            'tanggal_mulai_menjabat.required' => "Tahun Mulai Jabatan wajib diisi",
            'tanggal_akhir_menjabat.required' => "Tahun Akhir Jabatan wajib diisi",
            'email.required' => "Email Prajuru Desa Adat wajib diisi",
            'email.unique' => "Email yang dimasukkan telah terdaftar",
            'password.required' => "Password wajib diisi",
            'confirm_pass.required' => "Konfirmasi Password wajib diisi",
            'confirm_pass.same' => "Konfirmasi Password dan Password tidak cocok",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $desa_adat_id = session()->get('desa_adat_id');

        $krama_mipil = KramaMipil::find($request->krama_mipil);
        $cacah_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        //Create User
        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->jabatan;
        $user->status = '1';
        $user->save();

        //Create Prajuru
        $prajuru_desa_adat = new PrajuruDesaAdat();
        $prajuru_desa_adat->user_id = $user->id;
        $prajuru_desa_adat->krama_mipil_id = $request->krama_mipil;
        $prajuru_desa_adat->desa_adat_id = $desa_adat_id;
        $prajuru_desa_adat->jabatan = $request->jabatan;
        $prajuru_desa_adat->tanggal_mulai_menjabat = date("Y-m-d", strtotime($request->tanggal_mulai_menjabat));
        $prajuru_desa_adat->tanggal_akhir_menjabat = date("Y-m-d", strtotime($request->tanggal_akhir_menjabat));
        $prajuru_desa_adat->status = '1';
        $prajuru_desa_adat->save();

        //Create Akun Krama
        $akun_krama = new AkunKrama();
        $akun_krama->penduduk_id = $penduduk->id;
        $akun_krama->user_id = $user->id;
        $akun_krama->status = "aktif";
        $akun_krama->save();

        return back()->with('success', 'Prajuru Desa Adat Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $prajuru = PrajuruDesaAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->find($id);
        $krama_mipil = KramaMipil::find($prajuru->krama_mipil_id);
        $banjar_adat = BanjarAdat::find($krama_mipil->banjar_adat_id);
        $user = User::find($prajuru->user_id);
        $prajuru->tanggal_mulai_menjabat = date("d-m-Y", strtotime($prajuru->tanggal_mulai_menjabat));
        $prajuru->tanggal_akhir_menjabat = date("d-m-Y", strtotime($prajuru->tanggal_akhir_menjabat));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$prajuru->krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $prajuru->krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;
        return response()->json([
            'prajuru' => $prajuru,
            'user' => $user,
            'banjar_adat' => $banjar_adat
        ]);
    }

    public function update($id, Request $request)
    {
        $prajuru = PrajuruDesaAdat::find($id);
        $user = User::find($prajuru->user_id);
        $akun_krama = AkunKrama::where('user_id', $user->id)->first();

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
        $cacah_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        //UPDATE USER
        $user->email = $request->edit_email;
        $user->role = $request->edit_jabatan;
        if($request->edit_password != NULL){
            $user->password = bcrypt($request->password);
        }
        $user->update();

        //UPDATE PRAJURU
        $prajuru->krama_mipil_id = $request->edit_krama_mipil;
        $prajuru->jabatan = $request->edit_jabatan;
        $prajuru->tanggal_mulai_menjabat = date("Y-m-d", strtotime($request->edit_tanggal_mulai_menjabat));
        $prajuru->tanggal_akhir_menjabat = date("Y-m-d", strtotime($request->edit_tanggal_akhir_menjabat));
        $prajuru->status = '1';
        $prajuru->update();

        //UPDATE AKUN KRAMA
        $akun_krama->penduduk_id = $penduduk->id;
        $akun_krama->user_id = $user->id;
        $akun_krama->status = "aktif";
        $akun_krama->update();

        return back()->with('success', 'Prajuru Desa Adat berhasil diperbaharui');
    }

    public function detail($id)
    {
        $prajuru = PrajuruDesaAdat::with('krama_mipil.cacah_krama_mipil.penduduk')->find($id);
        $user = User::find($prajuru->user_id);
        $prajuru->tanggal_mulai_menjabat = date("d-m-Y", strtotime($prajuru->tanggal_mulai_menjabat));
        $prajuru->tanggal_akhir_menjabat = date("d-m-Y", strtotime($prajuru->tanggal_akhir_menjabat));

        //SET NAMA LENGKAP PRAJURU
        $nama = '';
        if($prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$prajuru->krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$prajuru->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $prajuru->krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;
        return response()->json([
            'prajuru' => $prajuru,
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $prajuru = PrajuruDesaAdat::find($id);
        $user = User::find($prajuru->user_id);
        
        $prajuru->status = '0';
        $prajuru->update();

        $user->role = 'krama';
        $user->update();
        return back()->with('success', 'Prajuru Desa Adat berhasil dinonaktifkan');
    }

}
