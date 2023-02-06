<?php

namespace App\Http\Controllers\DesaAdatController;
use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\CacahTamiu;
use App\Models\Tamiu;
use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $tamiu = Tamiu::with('cacah_tamiu.penduduk', 'banjar_adat')->get();
        return view('pages.desa.tamiu.tamiu', compact('banjar_adat', 'tamiu'));
    }

    public function generate_nomor_tamiu($banjar_adat_id){
        $banjar_adat = BanjarAdat::find($banjar_adat_id);

        $curr_month = Carbon::now()->format('m');
        $curr_year = Carbon::now()->year;
        $jumlah_bulan_regis_sama = Tamiu::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
        $curr_year = Carbon::now()->format('y');
        $jumlah_bulan_regis_sama = $jumlah_bulan_regis_sama + 1;
        $nomor_tamiu = $banjar_adat->kode_banjar_adat.'03'.$curr_month.$curr_year;
        if($jumlah_bulan_regis_sama < 10){
            $nomor_tamiu = $nomor_tamiu.'00'.$jumlah_bulan_regis_sama;
        }else if($jumlah_bulan_regis_sama < 100){
            $nomor_tamiu = $nomor_tamiu.'000'.$jumlah_bulan_regis_sama;
        }else if($jumlah_bulan_regis_sama < 1000){
            $nomor_tamiu = $nomor_tamiu.$jumlah_bulan_regis_sama;
        }
        return response()->json([
            'nomor_tamiu' => $nomor_tamiu
        ]);
    }

    public function search_krama(Request $request){
        $response = array();
        $banjar_adat = BanjarAdat::find($request->input('banjar_adat_id', ''));
        if($banjar_adat){
            $penduduk = Penduduk::where('nomor_induk_cacah_krama', $request->input('q', ''))->first();
            if($penduduk){
                $cacah_tamiu = CacahTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat->id)->first();
                if($cacah_tamiu){
                    $text = $penduduk->nomor_induk_cacah_krama.' - '.$penduduk->gelar_depan.' '.$penduduk->nama;
                    if($penduduk->gelar_belakang != ''){
                        $text = $text.', '.$penduduk->gelar_belakang;
                    }
                    $response[] = array(
                        "id"=>$cacah_tamiu->id,
                        "text"=>$text
                    );
                    return ['results' => $response];
                }else{
                    return ['results' => $response];
                }
            }else{
                $arr_penduduk_id = CacahTamiu::where('banjar_adat_id', $banjar_adat->id)->where('wna_id', NULL)->pluck('penduduk_id')->toArray();
                $penduduks = Penduduk::where('nama', 'LIKE', '%'.$request->input('q', '').'%')->whereIn('id', $arr_penduduk_id)->get();
                foreach($penduduks as $penduduk){
                    $cacah_tamiu = CacahTamiu::where('penduduk_id', $penduduk->id)->first();
                    $text = $cacah_tamiu->nomor_cacah_tamiu.' - '.$penduduk->gelar_depan.' '.$penduduk->nama;
                    if($penduduk->gelar_belakang != ''){
                        $text = $text.', '.$penduduk->gelar_belakang;
                    }
                    $response[] = array(
                        "id"=>$cacah_tamiu->id,
                        "text"=>$text
                    );
                }
                return ['results' => $response];
            }
        }else{
            return ['results' => $response];
        }
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'banjar_adat' => 'required',
            'cacah_tamiu' => 'required',
            'nomor_tamiu' => 'required',
          ],[
              'banjar_adat.required' => "Banjar Adat wajib dipilih",
              'cacah_tamiu.required' => "Cacah Krama Tamiu wajib dipilih",
              'nomor_tamiu.required' => "Nomor Krama Tamiu wajib diisi",
          ]);
    
          if($validator->fails()){
              return back()->withInput()->withErrors($validator);
          }

          $tamiu = new Tamiu();
          $tamiu->nomor_tamiu = $request->nomor_tamiu;
          $tamiu->banjar_adat_id = $request->banjar_adat;
          $tamiu->cacah_tamiu_id = $request->cacah_tamiu;
          $tamiu->status = '1';
          $tamiu->alasan_perubahan = 'Tamiu Baru';
          $tamiu->tanggal_registrasi = Carbon::now()->toDateString();
          $tamiu->save();

          return redirect()->route('desa-tamiu-home')->with('success', 'Tamiu berhasil ditambahkan');
    }
}