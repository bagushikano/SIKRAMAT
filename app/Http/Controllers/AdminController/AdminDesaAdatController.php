<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AdminDesaAdat;
use App\Models\DesaAdat;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AdminDesaAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
        $users = AdminDesaAdat::with('user', 'desa_adat')->get();
        return view('pages.admin.akun_desa_adat.akun_desa_adat', compact('kabupatens', 'users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kabupaten_id' => 'required',
            'kecamatan_id' => 'required',
            'desa_adat_id' => 'required',
            'email' => 'required|email|unique:tb_user',
        ],[
            'kabupaten_id.required' => "Kabupaten wajib dipilih",
            'kecamatan_id.required' => "Kecamatan wajib dipilih",
            'desa_adat_id.required' => "Desa Adat wajib dipilih",
            'email.required' => "Email Desa Adat wajib diisi",
            'email.unique' => "Email yang dimasukkan telah terdaftar"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $desa_adat = DesaAdat::find($request->desa_adat_id);

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt(Str::lower(str_replace(' ', '', $desa_adat->desadat_nama)).'-'.$desa_adat->desadat_kode);
        $user->role = 'admin_desa_adat';
        $user->save();

        $admin_desa_adat = new AdminDesaAdat();
        $admin_desa_adat->user_id = $user->id;
        $admin_desa_adat->desa_adat_id = $desa_adat->id;
        $admin_desa_adat->save();

        return back()->with('success', 'Akun Desa Adat Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $admin_desa_adat = AdminDesaAdat::find($id);
        $user = User::find($admin_desa_adat->user_id);
        $desa_adat = DesaAdat::find($admin_desa_adat->desa_adat_id);
        $kecamatan = Kecamatan::find($desa_adat->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        
        return response()->json([
            'admin_desa_adat' => $admin_desa_adat,
            'kecamatan' => $kecamatan,
            'kabupaten' => $kabupaten,
            'user' => $user,
            'desa_adat' => $desa_adat
        ]);
    }

    public function update($id, Request $request)
    {
        $admin_desa_adat = AdminDesaAdat::find($id);
        $user = User::find($admin_desa_adat->user_id);

        $validator = Validator::make($request->all(), [
            'edit_email' => 'required|email|unique:tb_user,email',
            'edit_email' => [
                Rule::unique('tb_user', 'email')->ignore($user->id),
            ],
        ],[
            'edit_email.required' => "Email Desa Adat wajib diisi",
            'edit_email.unique' => "Email yang dimasukkan telah terdaftar"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $user->email = $request->edit_email;
        $user->update();

        return back()->with('success', 'Akun Desa Adat Berhasil Diperbaharui');
    }

    public function destroy($id)
    {
        $admin_desa_adat = AdminDesaAdat::find($id);
        $user = User::find($admin_desa_adat->user_id);
        $user->delete();
        $admin_desa_adat->delete();
        return back()->with('success', 'Akun Admin Desa Adat berhasil dihapus');
    }

    public function status($id, $status){
        $admin_desa_adat = AdminDesaAdat::find($id);
        $admin_desa_adat->status = $status;
        $admin_desa_adat->update();

        $user = User::find($admin_desa_adat->user_id);
        if($status == 'aktif'){
            $user->status = '1';
        }else{
            $user->status = '0';
        }
        $user->update();

        return response()->json([
            'sukses' => 'sukses'
        ]);
    }
}
