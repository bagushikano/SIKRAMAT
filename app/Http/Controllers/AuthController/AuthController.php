<?php

namespace App\Http\Controllers\AuthController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Admin;
use App\Helper\Helper;
use App\Models\AdminBanjarAdat;
use App\Models\AdminDesaAdat;
use App\Models\AkunKrama;
use App\Models\DesaAdat;
use App\Models\Kabupaten;
use App\Models\DesaDinas;
use App\Models\PrajuruBanjarAdat;
use App\Models\PrajuruDesaAdat;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{

    public function loginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|captcha',
        ],[
            'email.required' => "Email wajib diisi",
            'email.email' => "Masukkan Email dengan benar",
            'password.required' => "Password wajib diisi",
            'captcha.required' => "Captcha wajib diisi",
            'captcha.captcha' => "Captcha tidak valid",
        ]);

        if($validator->fails()){
            // dd($validator);
            return back()->withInput()->withErrors($validator);
        }

        if(Auth::guard()->attempt(['email' => $request->email, 'password' => $request->password])){
            if(auth()->guard()->user()->status){
                if(auth()->guard()->user()->role == 'super_admin'){
                    return redirect()->route('admin-dashboard');
                }else if(auth()->guard()->user()->role == 'admin_desa_adat'){
                    $desa_adat = AdminDesaAdat::with('user', 'desa_adat')->where('user_id', auth()->guard()->user()->id)->first();
                    session(['desa_adat_nama' => $desa_adat->desa_adat->desadat_nama]);
                    session(['desa_adat_id' => $desa_adat->desa_adat->id]);
                    return redirect()->route('desa-dashboard');
                }else if(auth()->guard()->user()->role == 'bendesa' || auth()->guard()->user()->role == 'pangliman' || auth()->guard()->user()->role == 'penyarikan' || auth()->guard()->user()->role == 'patengen'){
                    $desa_adat = PrajuruDesaAdat::with('user', 'desa_adat')->where('user_id', auth()->guard()->user()->id)->first();
                    // $krama_mipil = Helper::generate_nama_krama_mipil($desa_adat->krama_mipil);
                    $krama_mipil = $desa_adat->krama_mipil;
                    session(['nama_user' => $krama_mipil->cacah_krama_mipil->penduduk->nama]);
                    session(['desa_adat_nama' => $desa_adat->desa_adat->desadat_nama]);
                    session(['desa_adat_id' => $desa_adat->desa_adat->id]);
                    return redirect()->route('desa-dashboard');
                }else if(auth()->guard()->user()->role == 'admin_banjar_adat'){
                    $banjar_adat = AdminBanjarAdat::with('user', 'banjar_adat')->where('user_id', auth()->guard()->user()->id)->first();
                    $desa_adat = DesaAdat::find($banjar_adat->banjar_adat->desa_adat_id);
                    session(['nama_user' => $banjar_adat->nama]);
                    session(['banjar_adat_nama' => $banjar_adat->banjar_adat->nama_banjar_adat]);
                    session(['banjar_adat_id' => $banjar_adat->banjar_adat->id]);
                    session(['desa_adat_nama' => $desa_adat->desadat_nama]);
                    session(['desa_adat_id' => $desa_adat->id]);
                    return redirect()->route('banjar-dashboard');
                }else if(auth()->guard()->user()->role == 'kelihan_adat' || auth()->guard()->user()->role == 'pangliman_banjar' || auth()->guard()->user()->role == 'penyarikan_banjar' || auth()->guard()->user()->role == 'patengen_banjar'){
                    $banjar_adat = PrajuruBanjarAdat::with('user', 'banjar_adat', 'krama_mipil')->where('user_id', auth()->guard()->user()->id)->first();
                    $desa_adat = DesaAdat::find($banjar_adat->banjar_adat->desa_adat_id);
                    // $krama_mipil = Helper::generate_nama_krama_mipil($banjar_adat->krama_mipil);
                    $krama_mipil = $banjar_adat->krama_mipil;
                    session(['nama_user' => $krama_mipil->cacah_krama_mipil->penduduk->nama]);
                    session(['banjar_adat_nama' => $banjar_adat->banjar_adat->nama_banjar_adat]);
                    session(['banjar_adat_id' => $banjar_adat->banjar_adat->id]);
                    session(['desa_adat_nama' => $desa_adat->desadat_nama]);
                    session(['desa_adat_id' => $desa_adat->id]);
                    return redirect()->route('banjar-dashboard');
                }else{
                    $akun_krama = AkunKrama::with('penduduk')->where('user_id', auth()->guard()->user()->id)->first();
                    session(['nama_user' => $akun_krama->penduduk->nama]);
                    return redirect()->route('Dashboard Krama');
                }
            }else{
                Auth::guard()->logout();
                return redirect()->back()->with('error', 'Akun telah tidak aktif');                
            }
        } else {
            return redirect()->back()->with('error', 'Email atau Password Salah');
        }
    }

    public function logout()
    {
        Auth::guard()->logout();
        session()->flush();
        return redirect()->route('login-form');
    }

    public function reload_captcha()
    {
        return response()->json(['captcha'=> captcha_img('flat')]);
    }

    public function email_verified(){
        return view('auth.verified');
    }

    public function profile()
    {
        return view('adminpages.auth.profile');
    }

    public function updateProfile(Request $request){
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'nip' => 'required:unique:admins'
        ],[
            'nama.required' => "Nama wajib diisi",
            'nip.required' => "nip wajib diisi",
            'nip.unique' => "nip telah terdaftar",
        ]);

        if($validator->fails()){
            return back()->withErrors($validator);
        }

        $admin = Admin::find(Auth::user()->id);
        $admin->nama = $request->nama;
        $admin->nip=$request->nip;

        if($request->foto!=''){
            $image_parts = explode(';base64', $request->foto);
            $image_type_aux = explode('image/', $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $filename = uniqid().'.png';
            $fileLocation = '/image/admin/profile';
            $path = $fileLocation."/".$filename;
            $admin->foto = '/storage'.$path;
            Storage::disk('public')->put($path, $image_base64);
        }

        $admin->update();

        return redirect('admin/profile')->with('statusInput', 'Data profile berhasil disimpan');
    }

    public function password(){
        return view('adminpages.auth.password');
    }

    public function editpassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password_lama' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required|same:password'
        ],[
            'password_confirmation.same' => "Konfirmasi password baru tidak sesuai",
        ]);

        if($validator->fails()){
            return back()->withErrors($validator);
        }

        $admin = Admin::find(Auth::user()->id);
        if (Hash::check($request->password_lama, $admin->password)) {
            Admin::where('id', $admin->id)->update([
                'password' => bcrypt($request->password)
            ]);
            //dd($admin->nama);
            return redirect()->back()->with('statusInput', 'Password berhasil diganti');
        } else{
            //dd($admin->nama);
            return redirect()->back()->with('error', 'Password lama salah');
        }
    }
}
