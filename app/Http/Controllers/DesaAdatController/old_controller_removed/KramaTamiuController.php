<?php

namespace App\Http\Controllers\DesaAdatController;
use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\CacahKramaTamiu;
use App\Models\KramaTamiu;
use App\Models\Penduduk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class KramaTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk', 'banjar_adat')->get();
        return view('pages.desa.krama_tamiu.krama_tamiu', compact('banjar_adat', 'krama_tamiu'));
    }

    public function generate_nomor_krama_tamiu($banjar_adat_id){
        $banjar_adat = BanjarAdat::find($banjar_adat_id);

        $curr_month = Carbon::now()->format('m');
        $curr_year = Carbon::now()->year;
        $jumlah_krama_bulan_regis_sama = KramaTamiu::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
        $curr_year = Carbon::now()->format('y');
        $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
        $nomor_krama_tamiu = $banjar_adat->kode_banjar_adat.'02'.$curr_month.$curr_year;
        if($jumlah_krama_bulan_regis_sama < 10){
            $nomor_krama_tamiu = $nomor_krama_tamiu.'00'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 100){
            $nomor_krama_tamiu = $nomor_krama_tamiu.'000'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 1000){
            $nomor_krama_tamiu = $nomor_krama_tamiu.$jumlah_krama_bulan_regis_sama;
        }
        return response()->json([
            'nomor_krama_tamiu' => $nomor_krama_tamiu
        ]);
    }

    public function search_krama(Request $request){
        $response = array();
        $banjar_adat = BanjarAdat::find($request->input('banjar_adat_id', ''));
        if($banjar_adat){
            $penduduk = Penduduk::where('nomor_induk_cacah_krama', $request->input('q', ''))->first();
            if($penduduk){
                $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat->id)->first();
                if($cacah_krama_tamiu){
                    $text = $penduduk->nomor_induk_cacah_krama.' - '.$penduduk->gelar_depan.' '.$penduduk->nama;
                    if($penduduk->gelar_belakang != ''){
                        $text = $text.', '.$penduduk->gelar_belakang;
                    }
                    $response[] = array(
                        "id"=>$cacah_krama_tamiu->id,
                        "text"=>$text
                    );
                    return ['results' => $response];
                }else{
                    return ['results' => $response];
                }
            }else{
                $arr_penduduk_id = CacahKramaTamiu::where('banjar_adat_id', $banjar_adat->id)->pluck('penduduk_id')->toArray();
                $penduduks = Penduduk::where('nama', 'LIKE', '%'.$request->input('q', '').'%')->whereIn('id', $arr_penduduk_id)->get();
                foreach($penduduks as $penduduk){
                    $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->first();
                    $text = $penduduk->nomor_induk_cacah_krama.' - '.$penduduk->gelar_depan.' '.$penduduk->nama;
                    if($penduduk->gelar_belakang != ''){
                        $text = $text.', '.$penduduk->gelar_belakang;
                    }
                    $response[] = array(
                        "id"=>$cacah_krama_tamiu->id,
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
            'cacah_krama_tamiu' => 'required',
            'nomor_krama_tamiu' => 'required',
          ],[
              'banjar_adat.required' => "Banjar Adat wajib dipilih",
              'cacah_krama_tamiu.required' => "Cacah Krama Tamiu wajib dipilih",
              'nomor_krama_tamiu.required' => "Nomor Krama Tamiu wajib diisi",
          ]);
    
          if($validator->fails()){
              return back()->withInput()->withErrors($validator);
          }

          $krama_tamiu = new Kramatamiu();
          $krama_tamiu->nomor_krama_tamiu = $request->nomor_krama_tamiu;
          $krama_tamiu->banjar_adat_id = $request->banjar_adat;
          $krama_tamiu->cacah_krama_tamiu_id = $request->cacah_krama_tamiu;
          $krama_tamiu->status = '1';
          $krama_tamiu->alasan_perubahan = 'Krama Tamiu Baru';
          $krama_tamiu->tanggal_registrasi = Carbon::now()->toDateString();
          $krama_tamiu->save();

          return redirect()->route('desa-krama-tamiu-home')->with('success', 'Krama Tamiu berhasil ditambahkan');
    }
}