<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\BanjarAdat;
use App\Models\DesaAdat;
use App\Models\KramaMipil;
use App\Models\Penduduk;
use App\Models\PrajuruBanjarAdat;
use App\Models\PrajuruDesaAdat;
use App\Models\PrajuruPermission;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class PrajuruPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $desa_adat_id = session()->get('desa_adat_id');
        $permissions = PrajuruPermission::where('desa_adat_id', $desa_adat_id)->get();
        return view('pages.desa.prajuru.permission_prajuru', compact('permissions'));
    }

    public function edit($id)
    {
        $prajuru_permission = PrajuruPermission::find($id);
        $prajuru_permission->role = ucwords(str_replace('_', ' ', $prajuru_permission->role));
        $prajuru_permission->permission = unserialize($prajuru_permission->permission);
        return response()->json([
            'prajuru_permission' => $prajuru_permission
        ]);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required',
        ],[
            'permission.required' => "Hak Akses Wajib Diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $prajuru_permission = PrajuruPermission::find($id);
        $prajuru_permission->permission = serialize($request->permission);
        $prajuru_permission->update();

        return redirect()->back()->with('success', 'Hak Akses Prajuru Berhasil Diperbaharui');
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
            $prajuru_desa_adat = PrajuruDesaAdat::with('krama.penduduk')->where('desa_adat_id', $desa_adat_id)->where('status_prajuru_desa_adat', '1')->orderBy('jabatan', 'ASC')->get();
            $hasil = view('pages.desa.prajuru.filter_prajuru_desa', ['prajuru_desa_adat' => $prajuru_desa_adat])->render();
            return response()->json(['success' => 'Prajuru difilter', 'hasil' => $hasil]);
        }else if($status == 'purna'){
            $desa_adat_id = session()->get('desa_adat_id');
            $prajuru_desa_adat = PrajuruDesaAdat::with('krama.penduduk')->where('desa_adat_id', $desa_adat_id)->where('status_prajuru_desa_adat', '0')->orderBy('jabatan', 'ASC')->get();
            $hasil = view('pages.desa.prajuru.filter_prajuru_desa', ['prajuru_desa_adat' => $prajuru_desa_adat])->render();
            return response()->json(['success' => 'Prajuru difilter', 'hasil' => $hasil]);
        }
    }
}
