<?php

namespace App\Http\Middleware;

use App\Helper\Helper;
use App\Models\AdminBanjarAdat;
use App\Models\AdminDesaAdat;
use App\Models\AkunKrama;
use App\Models\DesaAdat;
use App\Models\PrajuruBanjarAdat;
use App\Models\PrajuruDesaAdat;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
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
        }

        return $next($request);
    }
}
