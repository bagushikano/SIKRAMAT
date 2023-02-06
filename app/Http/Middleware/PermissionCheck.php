<?php

namespace App\Http\Middleware;

use App\Models\BanjarAdat;
use App\Models\DesaAdat;
use App\Models\PrajuruBanjarAdat;
use App\Models\PrajuruDesaAdat;
use App\Models\PrajuruPermission;
use Illuminate\Support\Facades\Auth;
use Closure;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $expression)
    {
        $user = auth()->guard()->user();
        if ($user != NULL) {
            if($user->role == 'admin_desa_adat'){
                return $next($request);
            }else if($user->role == 'bendesa' || $user->role == 'pangliman' || $user->role == 'penyarikan' || $user->role == 'patengen'){
                $prajuru_desa_adat = PrajuruDesaAdat::with('user', 'desa_adat')->where('user_id', $user->id)->first();
                $desa_adat = DesaAdat::find($prajuru_desa_adat->desa_adat_id);
                $role = $user->role;
                $prajuru_permission = PrajuruPermission::where('desa_adat_id', $desa_adat->id)->where('role', $role)->first();
                $permission = unserialize($prajuru_permission->permission);
                if(in_array($expression, $permission)){
                    return $next($request);
                }
            }else if($user->role == 'kelihan_adat' || $user->role == 'pangliman_banjar' || $user->role == 'penyarikan_banjar' || $user->role == 'patengen_banjar'){
                $prajuru_banjar_adat = PrajuruBanjarAdat::with('user', 'banjar_adat')->where('user_id', $user->id)->first();
                $banjar_adat = BanjarAdat::find($prajuru_banjar_adat->banjar_adat_id);
                $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
                $role = $user->role;
                $prajuru_permission = PrajuruPermission::where('desa_adat_id', $desa_adat->id)->where('role', $role)->first();
                $permission = unserialize($prajuru_permission->permission);
                if(in_array($expression, $permission)){
                    return $next($request);
                }
            }
        }else{
            return redirect(route('login-post'));
        }
    }
}
