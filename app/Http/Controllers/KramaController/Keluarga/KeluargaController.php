<?php

namespace App\Http\Controllers\KramaController\Keluarga;

use App\Http\Controllers\Controller;
use App\Models\AkunKrama;
use Illuminate\Http\Request;
use App\Models\KramaMipil;
use App\Models\KramaTamiu;
use App\Models\AnggotaKramaMipil;
use App\Models\AnggotaKramaTamiu;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Penduduk;
use App\Models\PrajuruDesaAdat;
use App\Models\Provinsi;
use App\Models\Tempekan;
use Carbon\Carbon;
use Auth;

class KeluargaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $penduduk_id = Auth::user()->user->penduduk_id;
        /**
         * Cari cacah krama mipil dulu, klo not found, abort
         */
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        if ($cacahKramaMipil) {
            /**
             * Cari krama mipil (kepala keluarga) dari id cacah krama klo found return krama mipilnya (keluarga)
             * klo not found, cari di anggota krama mipil, klo masih notfound abort, klo found return krama mipilnya (keluarga)
             */
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)
                                        ->with('cacah_krama_mipil.penduduk', 'anggota.cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat.desa_adat')
                                        ->where('status', 1)->first();
            if ($kramaMipil) {
                $krama_mipil = $kramaMipil;
                return view('pages.krama.anggota_keluarga.anggota_keluarga', compact('krama_mipil'));
            }
            else {
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', '1')->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)
                                        ->with('cacah_krama_mipil.penduduk', 'anggota.cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat.desa_adat')
                                        ->where('status', 1)->first();
                $krama_mipil = $kramaMipil;
                return view('pages.krama.anggota_keluarga.anggota_keluarga', compact('krama_mipil'));
                }
                else {
                    return back();
                }
            }
        }
        else {
            return back();
        }
    }

    public function detail_krama($id){
        $krama = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);

        //VALIDASI

        $cacah_krama_mipil = CacahKramaMipil::with('penduduk')->find($krama->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
        $banjar_adat = BanjarAdat::find($cacah_krama_mipil->banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat->id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);
        return view('pages.krama.anggota_keluarga.detail_krama', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas', 'tempekan'));
    }

    public function detail_anggota($id){
        //GET DATA
        $krama = CacahKramaMipil::find($id);

        $banjar_adat_id = BanjarAdat::find($krama->banjar_adat_id);
        $banjar_adat = BanjarAdat::find($krama->banjar_adat_id);

        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat->id)->get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id->id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);
        $anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $krama->id)->first();
        return view('pages.krama.anggota_keluarga.detail_anggota', compact('krama', 'anggota', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas'));
    }

    public function kartu_keluarga($id){
        //VALIDASI
        $penduduk_id = Auth::user()->user->penduduk_id;
        /**
         * Cari cacah krama mipil dulu, klo not found, abort
         */
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        if ($cacahKramaMipil) {
            /**
             * Cari krama mipil (kepala keluarga) dari id cacah krama klo found return krama mipilnya (keluarga)
             * klo not found, cari di anggota krama mipil, klo masih notfound abort, klo found return krama mipilnya (keluarga)
             */
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', '1')->first();
            if ($kramaMipil) {
               if($kramaMipil->id != $id){
                   return back();
               }
            }
            else {
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', '1')->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->first();
                    if($kramaMipil->id != $id){
                        return back();
                    }
                }
                else {
                    return back();
                }
            }
        }

        //GET KRAMA MIPIL
        $banjar_adat_id = session()->get('banjar_adat_id');
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'cacah_krama_mipil.banjar_adat')->find($id);
        $banjar_adat = BanjarAdat::find($krama_mipil->banjar_adat_id);
        $banjar_dinas = BanjarDinas::find($krama_mipil->cacah_krama_mipil->banjar_dinas_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $kecamatan = Kecamatan::find($desa_adat->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);

        //GET BENDESA
        $bendesa = PrajuruDesaAdat::where('desa_adat_id', $desa_adat->id)->where('status', '1')->first();
        //VALIDASI
        if($krama_mipil->status == '0'){
            return redirect()->back();
        }
        if(!$bendesa){
            return redirect()->back()->with('error', 'Bendesa Belum Ditambahkan');
        }
        
        //SET NAMA BENDESA
        $nama = '';
        if($bendesa->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != NULL){
            $nama = $nama.$bendesa->krama_mipil->cacah_krama_mipil->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$bendesa->krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($bendesa->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$bendesa->krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $bendesa->krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        //SET NAMA LENGKAP KRAMA MIPIL
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != NULL){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        //SET TANGGAL LAHIR KRAMA MIPIL
        $tanggal = $krama_mipil->cacah_krama_mipil->penduduk->tanggal_lahir;
        $tanggal = Carbon::parse($tanggal)->locale('id');
        $tanggal->settings(['formatFunction' => 'translatedFormat']);
        $krama_mipil->cacah_krama_mipil->penduduk->tanggal_lahir = $tanggal->format('d F Y');
        //SET NAMA LENGKAP AYAH KRAMA MIPIL
        if($krama_mipil->cacah_krama_mipil->penduduk->ayah){
            $nama = '';
            if($krama_mipil->cacah_krama_mipil->penduduk->ayah->gelar_depan != NULL){
                $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->ayah->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->ayah->nama;
            if($krama_mipil->cacah_krama_mipil->penduduk->ayah->gelar_belakang != NULL){
                $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->ayah->gelar_belakang;
            }
            $krama_mipil->cacah_krama_mipil->penduduk->ayah->nama = $nama;
        }

        //SET NAMA LENGKAP IBU KRAMA MIPIL
        if($krama_mipil->cacah_krama_mipil->penduduk->ibu){
            $nama = '';
            if($krama_mipil->cacah_krama_mipil->penduduk->ibu->gelar_depan != NULL){
                $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->ibu->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->ibu->nama;
            if($krama_mipil->cacah_krama_mipil->penduduk->ibu->gelar_belakang != NULL){
                $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->ibu->gelar_belakang;
            }
            $krama_mipil->cacah_krama_mipil->penduduk->ibu->nama = $nama;
        }

        //GET ANGGOTA KRAMA MIPIL
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk.ayah', 'cacah_krama_mipil.penduduk.ibu')->where('krama_mipil_id', $krama_mipil->id)->where('status', '1')->get();

        //SET NAMA LENGKAP ANGGOTA DAN ORANG TUA
        foreach($anggota_krama_mipil as $item){
            //SET NAMA LENGKAP ANGGOTA
            $nama = '';
            if($item->cacah_krama_mipil->penduduk->gelar_depan != NULL){
                $nama = $nama.$item->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$item->cacah_krama_mipil->penduduk->nama;
            if($item->cacah_krama_mipil->penduduk->gelar_belakang != NULL){
                $nama = $nama.', '.$item->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $item->cacah_krama_mipil->penduduk->nama = $nama;

            //SET TANGGAL LAHIR ANGGOTA
            $tanggal = $item->cacah_krama_mipil->penduduk->tanggal_lahir;
            $tanggal = Carbon::parse($tanggal)->locale('id');
            $tanggal->settings(['formatFunction' => 'translatedFormat']);
            $item->cacah_krama_mipil->penduduk->tanggal_lahir = $tanggal->format('d F Y');

            //SET NAMA LENGKAP AYAH
            // if($item->cacah_krama_mipil->penduduk->ayah_kandung_id){
            //     $nama = '';
            //     if($item->cacah_krama_mipil->penduduk->ayah->gelar_depan != NULL){
            //         $nama = $nama.$item->cacah_krama_mipil->penduduk->ayah->gelar_depan.' ';
            //     }
            //     $nama = $nama.$item->cacah_krama_mipil->penduduk->ayah->nama;
            //     if($item->cacah_krama_mipil->penduduk->ayah->gelar_belakang != NULL){
            //         $nama = $nama.', '.$item->cacah_krama_mipil->penduduk->ayah->gelar_belakang;
            //     }
            //     $item->cacah_krama_mipil->penduduk->ayah->nama = $nama;
            // }

            //SET NAMA LENGKAP IBU
            // if($item->cacah_krama_mipil->penduduk->ibu_kandung_id){
            //     $nama = '';
            //     if($item->cacah_krama_mipil->penduduk->ibu->gelar_depan != NULL){
            //         $nama = $nama.$item->cacah_krama_mipil->penduduk->ibu->gelar_depan.' ';
            //     }
            //     $nama = $nama.$item->cacah_krama_mipil->penduduk->ibu->nama;
            //     if($item->cacah_krama_mipil->penduduk->ibu->gelar_belakang != NULL){
            //         $nama = $nama.', '.$item->cacah_krama_mipil->penduduk->ibu->gelar_belakang;
            //     }
            //     $item->cacah_krama_mipil->penduduk->ibu->nama = $nama;
            // }
        }

        //GET TANGGAL SEKARANG
        $tanggal_sekarang = Carbon::now()->locale('id');
        $tanggal_sekarang->settings(['formatFunction' => 'translatedFormat']);
        $tanggal_sekarang = $tanggal_sekarang->format('j F Y');

        return view('pages.krama.anggota_keluarga.kartu_keluarga', compact(
            'krama_mipil', 'anggota_krama_mipil',
            'banjar_adat', 'banjar_dinas','desa_adat', 'bendesa',
            'kecamatan', 'kabupaten', 'tanggal_sekarang'
        ));
    }
}