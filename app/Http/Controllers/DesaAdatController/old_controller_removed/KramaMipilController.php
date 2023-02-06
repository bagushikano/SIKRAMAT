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

class KramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $desa_adat_id = session()->get('desa_adat_id');
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'banjar_adat')->get();
        return view('pages.desa.krama_mipil.krama_mipil', compact('banjar_adat', 'krama_mipil'));
    }

    public function generate_nomor_krama_mipil($banjar_adat_id){
        $banjar_adat = BanjarAdat::find($banjar_adat_id);

        $curr_month = Carbon::now()->format('m');
        $curr_year = Carbon::now()->year;
        $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
        $curr_year = Carbon::now()->format('y');
        $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
        $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
        if($jumlah_krama_bulan_regis_sama < 10){
            $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 100){
            $nomor_krama_mipil = $nomor_krama_mipil.'000'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 1000){
            $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
        }
        return response()->json([
            'nomor_krama_mipil' => $nomor_krama_mipil
        ]);
    }

    public function search_krama(Request $request){
        $response = array();
        $banjar_adat = BanjarAdat::find($request->input('banjar_adat_id', ''));
        if($banjar_adat){
            $penduduk = Penduduk::where('nomor_induk_cacah_krama', $request->input('q', ''))->first();
            if($penduduk){
                $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat->id)->first();
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
                    $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->first();
                    $text = $penduduk->nomor_induk_cacah_krama.' - '.$penduduk->gelar_depan.' '.$penduduk->nama;
                    if($penduduk->gelar_belakang != ''){
                        $text = $text.', '.$penduduk->gelar_belakang;
                    }
                    $response[] = array(
                        "id"=>$cacah_krama_mipil->id,
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
            'cacah_krama_mipil' => 'required',
            'nomor_krama_mipil' => 'required',
          ],[
              'banjar_adat.required' => "Banjar Adat wajib dipilih",
              'cacah_krama_mipil.required' => "Cacah Krama Mipil wajib dipilih",
              'nomor_krama_mipil.required' => "Nomor Krama Mipil wajib diisi",
          ]);
    
          if($validator->fails()){
              return back()->withInput()->withErrors($validator);
          }

          $krama_mipil = new KramaMipil();
          $krama_mipil->nomor_krama_mipil = $request->nomor_krama_mipil;
          $krama_mipil->banjar_adat_id = $request->banjar_adat;
          $krama_mipil->cacah_krama_mipil_id = $request->cacah_krama_mipil;
          $krama_mipil->status = '1';
          $krama_mipil->alasan_perubahan = 'Krama Mipil Baru';
          $krama_mipil->tanggal_registrasi = Carbon::now()->toDateString();
          $krama_mipil->save();

          return redirect()->route('desa-krama-mipil-home')->with('success', 'Krama Mipil berhasil ditambahkan');
    }

    public function edit($id){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $anggota_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->orderBy('status_hubungan', 'ASC')->get();
        return view('pages.desa.krama_mipil.edit', compact('krama_mipil', 'anggota_krama_mipil'));
    }

    public function update($id, Request $request){
        $validator = Validator::make($request->all(), [
            'krama_mipil_baru' => 'required',
            'alasan_penggantian' => 'required',
        ],[
            'krama_mipil_baru.required' => "Krama Mipil baru wajib dipilih",
            'alasan_penggantian.required' => "Alasan Pergantian wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET KELUARGA LAMA
        $krama_mipil_lama = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil_lama->id)->get();

        //INSERT KRAMA MIPIL BARU
        $krama_mipil_baru = new KramaMipil();
        $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
        $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
        $krama_mipil_baru->cacah_krama_mipil_id = $request->krama_mipil_baru;
        $krama_mipil_baru->status = '1';
        $krama_mipil_baru->alasan_perubahan = ucwords(str_replace('_', ' ', $request->alasan_penggantian));
        $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
        $krama_mipil_baru->save();

        //INSERT ANGGOTA BARU DAN DELETE ANGGOTA LAMA SEBAGAI HISTORY
        foreach ($anggota_krama_mipil_lama as $anggota_lama){
            if($anggota_lama->cacah_krama_mipil_id != $request->krama_mipil_baru){
                $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                $anggota_krama_mipil_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                $anggota_krama_mipil_baru->status_hubungan = $anggota_lama->status_hubungan;
                $anggota_krama_mipil_baru->save();
            }else if($anggota_lama->cacah_krama_mipil_id == $request->krama_mipil_baru && $request->alasan_penggantian != 'krama_mipil_meninggal_dunia'){
                    $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                    $anggota_krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_baru->status_hubungan = 'famili_lain';
                    $anggota_krama_mipil_baru->save();
                // if($anggota_lama->status_hubungan == 'anak'){
                //     $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                //     $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                //     $anggota_krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                //     if($krama_mipil_lama->cacah_krama_mipil->penduduk->jenis_kelamin == 'perempuan'){
                //         $anggota_krama_mipil_baru->status_hubungan = 'ibu';
                //     }else{
                //         $krama_mipil_lama->status_hubungan = 'ayah';
                //     }
                //     $anggota_krama_mipil_baru->save();
                // }else if($anggota_lama->status_hubungan == 'istri'){
                //     $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                //     $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                //     $anggota_krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                //     $anggota_krama_mipil_baru->status_hubungan = 'suami';
                //     $anggota_krama_mipil_baru->save();
                // }else if($anggota_lama->status_hubungan == 'suami'){
                //     $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                //     $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                //     $anggota_krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                //     $anggota_krama_mipil_baru->status_hubungan = 'istri';
                //     $anggota_krama_mipil_baru->save();
                // }else if($anggota_lama->status_hubungan == 'ayah' || $anggota_lama->status_hubungan == 'ibu'){
                //     $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                //     $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                //     $anggota_krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                //     $anggota_krama_mipil_baru->status_hubungan = 'anak';
                //     $anggota_krama_mipil_baru->save();
                // }
                
            }
            $anggota_lama->delete();
        }

        //DELETE KRAMA MIPIL LAMA
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();
        $krama_mipil_lama->delete();

        return redirect()->route('desa-krama-mipil-edit', $krama_mipil_baru->id)->with('success', 'Krama Mipil berhasil diganti');
    }

    public function delete($id){
         //GET KRAMA DAN KELUARGA
         $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
         $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->get();

         foreach($anggota_krama_mipil as $anggota){
             $anggota->delete();
         }
         $krama_mipil->delete();

         return redirect()->back()->with('success', 'Krama Mipil berhasil dinonaktifkan');
    }
}