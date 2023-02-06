<?php

namespace App\Http\Controllers\DesaAdatController;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluargaKrama;
use App\Models\BanjarAdat;
use App\Models\KeluargaKrama;
use App\Models\KramaMipil;
use App\Models\Negara;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KeluargaKramaController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
      $keluargas = KeluargaKrama::with('banjar_adat')->get();
      foreach($keluargas as $keluarga){
        $kepala_keluarga = AnggotaKeluargaKrama::with('krama.penduduk')->where('keluarga_krama_id', $keluarga->id)->where('status_anggota_keluarga', 'kepala_keluarga')->first();
        $keluarga->kepala_keluarga = $kepala_keluarga;
      }
      return view('pages.desa.keluarga_krama.keluarga_krama', compact('keluargas'));
    }

    public function generate_nomor_keluarga($id){
      $banjar_adat = BanjarAdat::find($id);
      $nomor_keluarga = $banjar_adat->kode_banjar_adat.''.Carbon::now()->format('dmy');

      $jumlah_tanggal_registrasi_sama = KeluargaKrama::where('tanggal_registrasi', Carbon::now()->toDateString())->where('banjar_adat_id', $banjar_adat->id)->withTrashed()->count()+1;
      if($jumlah_tanggal_registrasi_sama<10){
        $nomor_keluarga = $nomor_keluarga.'00'.$jumlah_tanggal_registrasi_sama;
      }else if($jumlah_tanggal_registrasi_sama<100){
          $nomor_keluarga = $nomor_keluarga.'0'.$jumlah_tanggal_registrasi_sama;
      }else if($jumlah_tanggal_registrasi_sama<1000){
          $nomor_keluarga = $nomor_keluarga.$jumlah_tanggal_registrasi_sama;
      }
      return response()->json([
        'nomor_keluarga' => $nomor_keluarga
      ]);
    }

    public function search_krama(Request $request)
    {
        $penduduk = Penduduk::where('nik', $request->input('term', ''))->orWhere('nomor_induk_krama', $request->input('term', ''))->first();
        $krama = KramaMipil::where('penduduk_id', $penduduk->id)->first();
        $anggota_keluarga = AnggotaKeluargaKrama::where('krama_id', $krama->id)->first();
        $response = array();
        if($penduduk){
          if($krama){
            if($anggota_keluarga != NULL){
              return ['results' => $response];
            }else{
              $response[] = array(
                "id"=>$krama->id,
                "text"=>$penduduk->nama
              );
              return ['results' => $response];
            }
          }else{
            return ['results' => $response];
          }
        }else{
          return ['results' => $response];
        }
    }

    public function create()
    {
      $desa_adat_id = session()->get('desa_adat_id');
      $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
      return view('pages.desa.keluarga_krama.create', compact('banjar_adat'));
    }

    public function store(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'nomor_keluarga' => 'required',
        'banjar_adat_id' => 'required',
      ],[
          'nomor_keluarga.required' => "Nomor Keluarga wajib diisi",
          'banjar_adat_id.required' => "Banjar Adat wajib dipilih",
      ]);

      if($validator->fails()){
          return back()->withInput()->withErrors($validator);
      }

      if(count(array_unique($request->krama)) != count($request->krama)){
        return back()->with('error', 'Keluarga Krama gagal ditambahkan karena terdapat data Krama yang sama.');
      }

      $keluarga = new KeluargaKrama();
      $keluarga->nomor_keluarga = $request->nomor_keluarga;
      $keluarga->banjar_adat_id = $request->banjar_adat_id;
      $keluarga->status = '1';
      $keluarga->alasan_perubahan = 'KK Baru';
      $keluarga->tanggal_registrasi = Carbon::now()->toDateString();
      $keluarga->save();

      foreach($request->krama as $key=>$value){
        $anggota = new AnggotaKeluargaKrama();
        $anggota->keluarga_krama_id = $keluarga->id;
        $anggota->krama_id = $value;
        $anggota->status_anggota_keluarga = $request->status_hubungan[$key];
        $anggota->save();
      }

      return redirect()->route('desa-keluarga-krama-home')->with('success', 'Keluarga Krama berhasil ditambahkan');
    }

    public function edit($nomor_keluarga)
    {
      $desa_adat_id = session()->get('desa_adat_id');
      $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_id)->get();
      $keluarga = KeluargaKrama::where('nomor_keluarga', $nomor_keluarga)->first();
      $anggota_keluarga = AnggotaKeluargaKrama::with('krama.penduduk')->where('keluarga_krama_id', $keluarga->id)->get();

      //KODE KELUARGA
      $banjar = BanjarAdat::find($keluarga->banjar_adat_id);
      $nomor_keluarga_baru = $banjar->kode_banjar_adat.''.Carbon::now()->format('dmy');
      $jumlah_tanggal_registrasi_sama = KeluargaKrama::where('tanggal_registrasi', Carbon::now()->toDateString())->where('banjar_adat_id', $banjar->id)->withTrashed()->count()+1;
      if($jumlah_tanggal_registrasi_sama<10){
        $nomor_keluarga_baru = $nomor_keluarga_baru.'00'.$jumlah_tanggal_registrasi_sama;
      }else if($jumlah_tanggal_registrasi_sama<100){
          $nomor_keluarga_baru = $nomor_keluarga_baru.'0'.$jumlah_tanggal_registrasi_sama;
      }else if($jumlah_tanggal_registrasi_sama<1000){
          $nomor_keluarga_baru = $nomor_keluarga_baru.$jumlah_tanggal_registrasi_sama;
      }

      $jumlah_anggota_keluarga = count($anggota_keluarga);
      return view('pages.desa.keluarga_krama.edit', compact('banjar_adat', 'keluarga', 'anggota_keluarga', 'nomor_keluarga_baru', 'jumlah_anggota_keluarga'));
    }

    public function update($nomor_krama, Request $request){
      $validator = Validator::make($request->all(), [
        'nomor_keluarga' => 'required',
      ],[
          'nomor_keluarga.required' => "Nomor Keluarga wajib diisi",
      ]);

      if($validator->fails()){
          return back()->withInput()->withErrors($validator);
      }

      if(count(array_unique($request->krama)) != count($request->krama)){
        return back()->with('error', 'Keluarga Krama gagal ditambahkan karena terdapat data Krama yang sama.');
      }

      //GET KELUARGA LAMA
      $keluarga_lama = KeluargaKrama::where('nomor_keluarga', $request->nomor_krama)->first();

      //MASUKKAN KELUARGA BARU
      $keluarga = new KeluargaKrama();
      $keluarga->nomor_keluarga = $request->nomor_keluarga_baru;
      $keluarga->banjar_adat_id = $keluarga_lama->banjar_adat_id;
      $keluarga->status = '1';
      $keluarga->alasan_perubahan = 'Perubahan Data';
      $keluarga->tanggal_registrasi = Carbon::now()->toDateString();
      $keluarga->save();

      foreach($request->krama as $key=>$value){
        $anggota = new AnggotaKeluargaKrama();
        $anggota->keluarga_krama_id = $keluarga->id;
        $anggota->krama_id = $value;
        $anggota->status_anggota_keluarga = $request->status_hubungan[$key];
        $anggota->save();
      }

      //DELETE KELUARGA LAMA
      $anggota_keluarga = AnggotaKeluargaKrama::where('keluarga_krama_id', $keluarga_lama->id)->get();
      foreach($anggota_keluarga as $anggota){
        $anggota->delete();
      }
      $keluarga_lama->status = '0';
      $keluarga_lama->update();
      $keluarga_lama->delete();

      return redirect()->route('desa-keluarga-krama-home')->with('success', 'Keluarga Krama berhasil ditambahkan');
    }

    public function destroy($id){
      $keluarga = KeluargaKrama::find($id);
      $anggota_keluarga = AnggotaKeluargaKrama::where('keluarga_krama_id', $keluarga->id)->get();
      foreach($anggota_keluarga as $anggota){
        $anggota->delete();
      }
      $keluarga->delete();
      return redirect()->route('desa-keluarga-krama-home')->with('success', 'Keluarga Krama berhasil dihapus');
    }
}