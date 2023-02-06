<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKeluargaKrama;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\KramaMipil;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kematian;
use App\Models\KematianAjuan;
use App\Models\KeluargaKrama;
use App\Models\KramaTamiu;
use App\Models\Notifikasi;
use App\Models\Tamiu;
use App\Models\Tempekan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AjuanKematianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kematian = KematianAjuan::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan')->where('banjar_adat_id', $banjar_adat_id);
        if(isset($request->rentang_waktu)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $kematian->whereBetween('tanggal_kematian', [$start_date, $end_date]);
        }
        
        if (isset($request->status)) {
            if($request->status == '0'){
                $kematian->where('status', '0');
            }else if($request->status == '1'){
                $kematian->where('status', '1');
            }else if($request->status == '2'){
                $kematian->where('status', '2');
            }else if($request->status == '3'){
                $kematian->where('status', '3');
            }
        }

        $kematian->orderByRaw('FIELD(status, "1", "0", "3", "2")')->orderBy('created_at', 'DESC');
        
        return DataTables::of($kematian)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                $return = '';
                if($data->status == '0'){
                    $return .= '<span class="badge badge-warning text-wrap px-3 py-1"> Menunggu Konfirmasi </span>';
                }else if($data->status == '1'){
                    $return .= '<span class="badge badge-info text-wrap px-3 py-1"> Sedang Diproses </span>';
                }else if($data->status == '2'){
                    $return .= '<span class="badge badge-danger text-wrap px-3 py-1"> Ditolak </span>';
                }else{
                    $return .= '<span class="badge badge-success text-wrap px-3 py-1"> Sah </span>';
                }
                return $return;
            })
            ->addColumn('link', function ($data) {
                $return = '';
                $return .= '<a class="btn btn-primary btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-ajuan-kematian-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                return $return;
            })
            ->rawColumns(['status', 'link'])
            ->make(true);
    }
    
    public function index(){
        return view('pages.banjar.ajuan_kematian.ajuan_kematian');
    }

    public function detail($id){
        $kematian = KematianAjuan::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($kematian->cacah_krama_mipil_id);
        $penduduk = Penduduk::withTrashed()->with('ayah', 'ibu')->find($cacah_krama_mipil->penduduk_id);

        //SET NAMA AYAH
        $nama = '';
        if($penduduk->ayah->gelar_depan != ''){
            $nama = $nama.$penduduk->ayah->gelar_depan;
        }
        $nama = $nama.' '.$penduduk->ayah->nama;
        if($penduduk->ayah->gelar_belakang != ''){
            $nama = $nama.', '.$penduduk->ayah->gelar_belakang;
        }
        $penduduk->ayah->nama = $nama;

        //SET NAMA IBU
        $nama = '';
        if($penduduk->ibu->gelar_depan != ''){
            $nama = $nama.$penduduk->ibu->gelar_depan;
        }
        $nama = $nama.' '.$penduduk->ibu->nama;
        if($penduduk->ibu->gelar_belakang != ''){
            $nama = $nama.', '.$penduduk->ibu->gelar_belakang;
        }
        $penduduk->ibu->nama = $nama;
        return view('pages.banjar.ajuan_kematian.detail', compact('kematian', 'cacah_krama_mipil', 'penduduk'));
    }

    public function proses_kematian(Request $req)
    {
        $kematian = KematianAjuan::where('id', $req->id)->first();
        $kematian->status = 1;
        $kematian->tanggal_proses = Carbon::now();
        $kematian->save();

        $notifikasi = new Notifikasi();
        $userAjuan = User::where('id', $kematian->user_id)->with('user.penduduk')->first();
        $notifikasi->notif_kematian_ajuan($kematian->user_id, $kematian->banjar_adat_id, $kematian->id, $userAjuan->user->penduduk->nama, 1);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan data kematian sedang dalam proses oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kematian->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kematian->user_id,
                                "Ajuan kematian sedang dalam proses.",
                                null,
                                $kematian->id,
                                1,
                            );

        return redirect()->back()->with('alert', 'Ajuan berhasil diproses, silahkan pilih tindakan selanjutnya');
    }

    public function tolak_kematian(Request $req)
    {
        $kematian = KematianAjuan::where('id', $req->id)->first();
        $kematian->status = 2;
        $kematian->alasan_tolak_ajuan = $req->alasan_penolakan;
        $kematian->tanggal_tolak = Carbon::now();
        $kematian->save();

        $notifikasi = new Notifikasi();
        $userAjuan = User::where('id', $kematian->user_id)->with('user.penduduk')->first();

        $notifikasi->notif_kematian_ajuan($kematian->user_id, $kematian->banjar_adat_id, $kematian->id, $userAjuan->user->penduduk->nama, 2);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan data kematian tidak dapat disahkan dengan alasan: ".$kematian->alasan_tolak_ajuan;
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kematian->user_id,
                                "Ajuan kematian tidak dapat disahkan.",
                                null,
                                $kematian->id,
                                1,
                            );

        return redirect()->route('banjar-ajuan-kematian-home')->with('success', 'Ajuan Data Kematian berhasil ditolak');
    }

    public function sahkan_kematian($id)
    {
        /**
         * 1. Update ajuan kematian jadi 3 (di approve)
         * 2. copy data dari tb_kematian_ajuan jadi data tb_kematian
         * 3. klo acc, tanggal updated at yg jadi tanggal pengesahan data
         */
        $kematianAjuan = KematianAjuan::where('id', $id)->first();

        /**
         * update status kematian jadi 3 (approve)
         */
        $kematianAjuan->status = 3;
        $kematianAjuan->tanggal_sah = Carbon::now();
        $kematianAjuan->update();

        /**
         * Copy data tb_kelahiran_ajuan ke tb_kelahiran
         */
        $kematian = new Kematian();
        $kematian->nomor_akta_kematian = $kematianAjuan->nomor_akta_kematian;
        $kematian->nomor_suket_kematian = $kematianAjuan->nomor_suket_kematian;
        $kematian->cacah_krama_mipil_id = $kematianAjuan->cacah_krama_mipil_id;
        $kematian->banjar_adat_id = $kematianAjuan->banjar_adat_id;
        $kematian->tanggal_kematian = $kematianAjuan->tanggal_kematian;
        $kematian->penyebab_kematian = $kematianAjuan->penyebab_kematian;
        $kematian->keterangan = $kematianAjuan->keterangan;
        $kematian->status = 1;
        $kematian->file_akta_kematian = $kematianAjuan->file_akta_kematian;
        $kematian->file_suket_kematian = $kematianAjuan->file_suket_kematian;
        $kematian->save();

        /**
         * isi kelahiran_id di data ajuan kelahiran
         */
        $kematianAjuan->kematian_id = $kematian->id;
        $kematianAjuan->update();

         /**
          * cari yg meninggal
          */
         $cacah_krama_mipil = CacahKramaMipil::find($kematian->cacah_krama_mipil_id);
         $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

         /**
          * nonaktif cacah + penduduk
          */
         $cacah_krama_mipil->status = '0';
         $cacah_krama_mipil->update();
         $penduduk->tanggal_kematian = $kematian->tanggal_kematian;
         $penduduk->update();

         /**
          * Keluarin yang meninggal dari anggota keluarga jika anggota keluarga
          */
         $anggota_keluarga = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacah_krama_mipil->id)->where('status', '1')->first();
         if($anggota_keluarga){
             /**
              * cari kk yg lama
              */
             $krama_mipil_lama = KramaMipil::find($anggota_keluarga->krama_mipil_id);
             $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

             /**
              * copy data dari kk lama ke kk baru
              */
             $krama_mipil_baru = new KramaMipil();
             $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
             $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
             $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
             $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
             $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
             $krama_mipil_baru->status = '1';
             $krama_mipil_baru->alasan_perubahan = 'Kematian Anggota Keluarga';
             $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
             $krama_mipil_baru->save();

             foreach($anggota_krama_mipil_lama as $anggota_lama){
                 if($anggota_lama->id != $anggota_keluarga->id){
                     $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                     $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                     $anggota_krama_mipil_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                     $anggota_krama_mipil_baru->status_hubungan = $anggota_lama->status_hubungan;
                     $anggota_krama_mipil_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                     $anggota_krama_mipil_baru->status = '1';
                     $anggota_krama_mipil_baru->save();
                 }
                 /**
                  * Nonaktifin anggota keluarga yang lama yg lama
                  */
                 $anggota_lama->status = '0';
                 $anggota_lama->update();
             }

             /**
              * nonaktifin krama mipil yg lama
              */
             $krama_mipil_lama->status = '0';
             $krama_mipil_lama->update();
         }

        $notifikasi = new Notifikasi();
        $userAjuan = User::where('id', $kematianAjuan->user_id)->with('user.penduduk')->first();

        $notifikasi->notif_kematian_ajuan($kematianAjuan->user_id, $kematian->banjar_adat_id, $kematianAjuan->id, $userAjuan->user->penduduk->nama, 3);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan data kematian telah disahkan oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kematian->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kematianAjuan->user_id,
                                "Ajuan telah disahkan.",
                                null,
                                $kematianAjuan->id,
                                1,
                            );

        return redirect()->route('banjar-ajuan-kematian-home')->with('success', 'Ajuan Data Kematian berhasil disahkan');

    }
}