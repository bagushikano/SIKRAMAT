<?php

namespace App\Http\Controllers\DesaAdatController;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\KramaMipil;
use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AnggotaKramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search_krama(Request $request){
        $response = array();
        $krama_mipil = KramaMipil::find($request->input('krama_mipil_id', ''));
        $banjar_adat = BanjarAdat::find($krama_mipil->banjar_adat_id);
        $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat->id)->pluck('id')->toArray();
        $arr_cacah_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat->id)->pluck('cacah_krama_mipil_id')->toArray();
        $arr_anggota_krama_mipil_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->pluck('cacah_krama_mipil_id')->toArray();
        if($banjar_adat){
            $penduduk = Penduduk::where('nomor_induk_cacah_krama', $request->input('q', ''))->first();
            if($penduduk){
                $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat->id)->whereNotIn('id', $arr_anggota_krama_mipil_id)->whereNotIn('id', $arr_cacah_krama_mipil_id)->first();
                if($cacah_krama_mipil){
                    $text = $penduduk->nomor_induk_cacah_krama.' - '.$penduduk->gelar_depan.' '.$penduduk->nama;
                    if($penduduk->gelar_belakang != ''){
                        $text = $text.', '.$penduduk->gelar_belakang;
                    }
                    $response[] = array(
                        "id"=>$cacah_krama_mipil->id,
                        "text"=>$text
                    );
                    return ['results' => $response];
                }else{
                    return ['results' => $response];
                }
            }else{
                $arr_penduduk_id = CacahKramaMipil::where('banjar_adat_id', $banjar_adat->id)->pluck('penduduk_id')->toArray();
                $penduduks = Penduduk::where('nama', 'LIKE', '%'.$request->input('q', '').'%')->whereIn('id', $arr_penduduk_id)->get();
                foreach($penduduks as $penduduk){
                    $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->whereNotIn('id', $arr_anggota_krama_mipil_id)->whereNotIn('id', $arr_cacah_krama_mipil_id)->first();
                    if($cacah_krama_mipil){
                        $text = $penduduk->nomor_induk_cacah_krama.' - '.$penduduk->gelar_depan.' '.$penduduk->nama;
                        if($penduduk->gelar_belakang != ''){
                            $text = $text.', '.$penduduk->gelar_belakang;
                        }
                        $response[] = array(
                            "id"=>$cacah_krama_mipil->id,
                            "text"=>$text
                        );
                    }
                }
                return ['results' => $response];
            }
        }else{
            return ['results' => $response];
        }
    }

    public function store($krama_mipil_id, Request $request){
        $validator = Validator::make($request->all(), [
            'cacah_krama_mipil' => 'required',
            'status_hubungan' => 'required'
          ],[
              'cacah_krama_mipil.required' => "Cacah Krama Mipil wajib dipilih",
              'status_hubungan.required' => "Status Hubungan wajib dipilih",
          ]);
    
          if($validator->fails()){
              return back()->withInput()->withErrors($validator);
          }

          $anggota_krama_mipil = new AnggotaKramaMipil();
          $anggota_krama_mipil->krama_mipil_id = $krama_mipil_id;
          $anggota_krama_mipil->cacah_krama_mipil_id = $request->cacah_krama_mipil;
          $anggota_krama_mipil->status_hubungan = $request->status_hubungan;
          $anggota_krama_mipil->save();

          return redirect()->back()->with('success', 'Anggota Krama Mipil berhasil ditambahkan');
    }

    public function edit($id){
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        return response()->json([
            'anggota_krama_mipil' => $anggota_krama_mipil
        ]);
    }

    public function update($id, Request $request){
        $validator = Validator::make($request->all(), [
            'edit_cacah_krama_mipil' => 'required',
            'edit_status_hubungan' => 'required'
          ],[
              'edit_cacah_krama_mipil.required' => "Cacah Krama Mipil wajib dipilih",
              'edit_status_hubungan.required' => "Status Hubungan wajib dipilih",
          ]);
    
          if($validator->fails()){
              return back()->withInput()->withErrors($validator);
          }

          $anggota_krama_mipil = AnggotaKramaMipil::find($id);
          $anggota_krama_mipil->cacah_krama_mipil_id = $request->edit_cacah_krama_mipil;
          $anggota_krama_mipil->status_hubungan = $request->edit_status_hubungan;
          $anggota_krama_mipil->update();

          return redirect()->back()->with('success', 'Anggota Krama Mipil berhasil diperbaharui');
    }

    public function destroy($id){
        $anggota_krama_mipil = AnggotaKramaMipil::find($id);
        $anggota_krama_mipil->delete();
        return redirect()->back()->with('success', 'Anggota Krama Mipil berhasil dihapus');
    }
}