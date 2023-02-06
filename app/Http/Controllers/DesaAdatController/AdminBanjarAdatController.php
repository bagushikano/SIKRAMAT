<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\AdminBanjarAdat;
use App\Models\BanjarAdat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AdminBanjarAdatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $desa_adat_id = session()->get('desa_adat_id');
        $arr_banjar_id = BanjarAdat::where('desa_adat_id', $desa_adat_id)->pluck('id')->toArray();
        $users = AdminBanjarAdat::with('user', 'banjar_adat')->whereIn('banjar_adat_id', $arr_banjar_id)->get();
        return view('pages.desa.akun_banjar_adat.akun_banjar_adat', compact('users'));
    }

    public function create()
    {
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.akun_banjar_adat.create', compact('banjar_adat'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banjar_adat_id' => 'required',
            'email' => 'required|email|unique:tb_user',
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'jenis_kelamin' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ],[
            'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
            'email.required' => "Email Desa Adat wajib diisi",
            'email.unique' => "Email yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'password.required' => "Password wajib diisi",
            'confirm_pass.required' => "Konfirmasi Password wajib diisi",
            'confirm_pass.same' => "Konfirmasi Password dan Password tidak cocok",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = 'admin_banjar_adat';
        $user->save();

        $admin_banjar_adat = new AdminBanjarAdat();
        $admin_banjar_adat->user_id = $user->id;
        $admin_banjar_adat->banjar_adat_id = $request->banjar_adat_id;
        $admin_banjar_adat->nama = $request->nama;
        $admin_banjar_adat->jenis_kelamin = $request->jenis_kelamin;
        $admin_banjar_adat->status = '1';
        $admin_banjar_adat->save();

        return redirect()->route('desa-admin-banjar-home')->with('success', 'Akun Banjar Adat berhasil ditambahkan');
    }

    public function edit($id)
    {
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $admin_banjar_adat = AdminBanjarAdat::with('user')->find($id);
        return view('pages.desa.akun_banjar_adat.edit', compact('banjar_adat', 'admin_banjar_adat'));
    }

    public function update($id, Request $request)
    {
        $admin_banjar_adat = AdminBanjarAdat::find($id);
        $user = User::find($admin_banjar_adat->user_id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:tb_user',
            'email' => [
                Rule::unique('tb_user')->ignore($user->id),
            ],
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'jenis_kelamin' => 'required',
            // 'password' => 'required',
            // 'confirm_password' => 'required|same:password',
        ],[
            'email.required' => "Email Desa Adat wajib diisi",
            'email.unique' => "Email yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'password.required' => "Password wajib diisi",
            'confirm_pass.required' => "Konfirmasi Password wajib diisi",
            'confirm_pass.same' => "Konfirmasi Password dan Password tidak cocok",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $admin_banjar_adat->nama = $request->nama;
        $admin_banjar_adat->jenis_kelamin = $request->jenis_kelamin;
        $admin_banjar_adat->status = '1';
        $admin_banjar_adat->update();

        $user->email = $request->email;
        if($request->password != NULL){
            $user->password = bcrypt($request->password);
        }
        $user->update();
        return redirect()->route('desa-admin-banjar-home')->with('success', 'Akun Banjar Adat berhasil diperbaharui');
    }

    public function destroy($id)
    {
        $admin_banjar_adat = AdminBanjarAdat::find($id);
        $user = User::find($admin_banjar_adat->user_id);
        $user->status = '0';
        $user->update();
        $user->delete();

        $admin_banjar_adat->status = '0';
        $admin_banjar_adat->update();
        $admin_banjar_adat->delete();
        return redirect()->route('desa-admin-banjar-home')->with('success', 'Akun Banjar Adat berhasil dihapus');
    }

    public function status($id, $status){
        $admin_banjar_adat = AdminBanjarAdat::find($id);
        $admin_banjar_adat->status = $status;
        $admin_banjar_adat->update();

        $user = User::find($admin_banjar_adat->user_id);
        $user->status = $status;
        $user->update();

        return response()->json([
            'sukses' => 'sukses'
        ]);
    }
}
