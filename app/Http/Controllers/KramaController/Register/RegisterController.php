<?php

namespace App\Http\Controllers\KramaController\Register;

use App\Http\Controllers\Controller;
use App\Models\AkunKrama;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PharIo\Manifest\Email;
use Illuminate\Validation\Rule;


class RegisterController extends Controller
{
    public function register_awal()
    {
        return view('pages.krama.register.register_awal');
    }

    public function register_akhir(Request $req)
    {
        $penduduk = Penduduk::where('nik', $req->nik)->first();
        if ($penduduk != null) {
            if($penduduk->tanggal_lahir != date("Y-m-d", strtotime($req->tanggal_lahir))){
                return redirect()->back()->with('error', 'Tanggal lahir tidak valid');
            }
            if(AkunKrama::where('penduduk_id', $penduduk->id)->count() > 0) {
                return redirect()->back()->with('error', 'Anda sudah memiliki akun. Silahkan menuju halaman login');
            }
            else {
                if (CacahKramaMipil::where('penduduk_id', $penduduk->id)->count() > 0 || CacahKramaTamiu::where('penduduk_id', $penduduk->id)->count() > 0) {
                    return view('pages.krama.register.register_akhir', compact('penduduk'));
                }
                else {
                    return redirect()->back()->with('error', 'NIK tidak terdaftar dalam sistem. Silahkan hubungi Prajuru Banjar Adat');
                }
            }
        }
        else {
            return redirect()->back()->with('error', 'NIK tidak terdaftar dalam sistem. Silahkan hubungi Prajuru Banjar Adat');
        }
    }

    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|unique:tb_user,deleted_at,NULL',
            'password' => 'required|min:8',
            'confirm_pass' => 'required|same:password',
        ],[
            'email.required' => "Email Prajuru Desa Adat wajib diisi",
            'email.unique' => "Email yang dimasukkan telah terdaftar",
            'email.email' => "Email tidak valid",
            'password.required' => "Password wajib diisi",
            'password.min' => "Password minimal terdiri dari 8 karakter",
            'confirm_pass.required' => "Konfirmasi Password wajib diisi",
            'confirm_pass.same' => "Konfirmasi Password dan Password tidak cocok",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }


        $user = new User();
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->role = "krama";
        $user->save();

        $akun_krama = new AkunKrama();
        $akun_krama->penduduk_id = $req->penduduk_id;
        $akun_krama->user_id = $user->id;
        $akun_krama->status = "aktif";
        $akun_krama->save();

        if ($akun_krama != null) {
            return view('pages.krama.register.register_final');
        }
    }
}