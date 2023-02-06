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
use App\Models\Kelahiran;
use App\Models\KelahiranAjuan;
use App\Models\KeluargaKrama;
use App\Models\Kematian;
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

class AjuanKelahiranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kelahiran = KelahiranAjuan::with('cacah_krama_mipil.penduduk', 'krama_mipil.cacah_krama_mipil', 'cacah_krama_mipil.tempekan')->where('banjar_adat_id', $banjar_adat_id);
        if(isset($request->rentang_waktu)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $kelahiran->whereBetween('tanggal_lahir', [$start_date, $end_date]);
        }
        
        if (isset($request->status)) {
            if($request->status == '0'){
                $kelahiran->where('status', '0');
            }else if($request->status == '1'){
                $kelahiran->where('status', '1');
            }else if($request->status == '2'){
                $kelahiran->where('status', '2');
            }else if($request->status == '3'){
                $kelahiran->where('status', '3');
            }
        }

        $kelahiran->orderByRaw('FIELD(status, "1", "0", "3", "2")')->orderBy('created_at', 'DESC');
        
        return DataTables::of($kelahiran)
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
                $return .= '<a class="btn btn-primary btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-ajuan-kelahiran-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                return $return;
            })
            ->rawColumns(['status', 'link'])
            ->make(true);
    }
    
    public function index(){
        return view('pages.banjar.ajuan_kelahiran.ajuan_kelahiran');
    }

    public function detail($id){
        $kelahiran = KelahiranAjuan::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
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
        return view('pages.banjar.ajuan_kelahiran.detail', compact('kelahiran', 'cacah_krama_mipil', 'penduduk'));
    }

    public function proses_kelahiran(Request $req)
    {
        $kelahiran = KelahiranAjuan::where('id', $req->id)->first();
        $kelahiran->status = 1;
        $kelahiran->tanggal_proses = Carbon::now();
        $kelahiran->update();

        /**
         * create notif baru
         */
        
        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan($kelahiran->user_id, $kelahiran->banjar_adat_id, $kelahiran->id, $kelahiran->krama_mipil_id, 1);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kelahiran sedang dalam proses oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kelahiran->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kelahiran->user_id,
                                "Ajuan kelahiran sedang dalam proses.",
                                null,
                                $kelahiran->id,
                                0,
                            );

        return redirect()->back()->with('alert', 'Ajuan berhasil diproses, silahkan pilih tindakan selanjutnya');
    }

    public function tolak_kelahiran(Request $req)
    {
        $kelahiran = KelahiranAjuan::where('id', $req->id)->first();
        $kelahiran->status = 2;
        $kelahiran->alasan_tolak_ajuan = $req->alasan_penolakan;
        $kelahiran->tanggal_tolak = Carbon::now();
        $kelahiran->save();

        /**
         * create notif baru
         */
        
        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan($kelahiran->user_id, $kelahiran->banjar_adat_id, $kelahiran->id, $kelahiran->krama_mipil_id, 2);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kelahiran tidak dapat disahkan dengan alasan: ".$kelahiran->alasan_tolak_ajuan;
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kelahiran->user_id,
                                "Ajuan kelahiran tidak dapat disahkan.",
                                null,
                                $kelahiran->id,
                                0,
                            );

        $penduduk = Penduduk::find($kelahiran->cacah_krama_mipil->penduduk_id);
        $penduduk->delete();

        return redirect()->route('banjar-ajuan-kelahiran-home')->with('success', 'Ajuan Data Kelahiran berhasil ditolak');
    }

    public function sahkan_kelahiran($id)
    {
        /**
         * 1. Update ajuan kelahiran jadi 3 (di approve)
         * 2. copy data dari tb_kelahiran_ajuan jadi data tb_kelahiran
         * 3. klo acc, tanggal updated at yg jadi tanggal pengesahan data
         */

        $kelahiranAjuan = KelahiranAjuan::where('id', $id)->first();

        /**
         * update status ajuan kelahiran jadi 3 (di acc)
         */
        $kelahiranAjuan->status = 3;
        $kelahiranAjuan->tanggal_sah = Carbon::now();
        $kelahiranAjuan->update();

        /**
         * Copy data tb_kelahiran_ajuan ke tb_kelahiran
         */
        $kelahiran= new Kelahiran();
        $kelahiran->status = 1;
        $kelahiran->nomor_akta_kelahiran = $kelahiranAjuan->nomor_akta_kelahiran;
        $kelahiran->cacah_krama_mipil_id = $kelahiranAjuan->cacah_krama_mipil_id;
        $kelahiran->banjar_adat_id = $kelahiranAjuan->banjar_adat_id;
        $kelahiran->krama_mipil_id = $kelahiranAjuan->krama_mipil_id;
        $kelahiran->file_akta_kelahiran = $kelahiranAjuan->file_akta_kelahiran;
        $kelahiran->tanggal_lahir = $kelahiranAjuan->tanggal_lahir;
        $kelahiran->keterangan = $kelahiranAjuan->keterangan;
        $kelahiran->save();

        /**
         * isi kelahiran_id di data ajuan kelahiran
         */
         $kelahiranAjuan->kelahiran_id = $kelahiran->id;
         $kelahiranAjuan->update();

        /**
         * Update cacah mipil jadi 1 (aktif)
         */
        $cacah_krama_mipil = CacahKramaMipil::where('id', $kelahiran->cacah_krama_mipil_id)->first();
        $cacah_krama_mipil->tanggal_registrasi = $kelahiran->tanggal_lahir;
        $cacah_krama_mipil->status = 1;
        $cacah_krama_mipil->save();

        $pendudukLahir = Penduduk::where('id', $cacah_krama_mipil->penduduk_id)->first();

        /**
         * Update keluarga
         */
        $krama_mipil_lama = KramaMipil::find($kelahiran->krama_mipil_id);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

        /**
         * Copy data dari keluarga lama ke keluarga baru
         */
        $krama_mipil_baru = new KramaMipil();
        $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
        $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
        $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
        $krama_mipil_baru->status = '1';
        $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
        $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
        $krama_mipil_baru->alasan_perubahan = 'Kelahiran Anggota Keluarga Baru';
        $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
        $krama_mipil_baru->save();

        foreach($anggota_krama_mipil_lama as $anggota_lama){
            $anggota_krama_mipil_baru = new AnggotaKramaMipil();
            $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
            $anggota_krama_mipil_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
            $anggota_krama_mipil_baru->status_hubungan = $anggota_lama->status_hubungan;
            $anggota_krama_mipil_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
            $anggota_krama_mipil_baru->status = '1';
            $anggota_krama_mipil_baru->save();

            /**
             * Nonaktif anggota keluarga yang lama
             */
            $anggota_lama->status = '0';
            $anggota_lama->update();
        }

        /**
         * Insert yang baru lahir ke anggota keluarga
         */
        $anggota_krama_mipil = new AnggotaKramaMipil();
        $anggota_krama_mipil->krama_mipil_id = $krama_mipil_baru->id;
        $anggota_krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        $anggota_krama_mipil->status_hubungan = 'anak';
        $anggota_krama_mipil->tanggal_registrasi = $pendudukLahir->tanggal_lahir;
        $anggota_krama_mipil->status = '1';
        $anggota_krama_mipil->save();

        /**
         * nonaktifin keluarga yg lama
         */
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();

        /**
         * create notif baru
         */
        
        $notifikasi = new Notifikasi();
        $notifikasi->notif_kelahiran_ajuan($kelahiranAjuan->user_id, $kelahiran->banjar_adat_id, $kelahiranAjuan->id, $kelahiran->krama_mipil_id, 3);

        $userNotif = new User();
        error_log('ngirim notif gan 2');

        $kontenNotif = "Ajuan kelahiran telah disahkan oleh Prajuru Banjar pada tanggal ".Helper::convert_date_to_locale_id($kelahiran->updated_at);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                $kelahiranAjuan->user_id,
                                "Ajuan telah disahkan.",
                                null,
                                $kelahiranAjuan->id,
                                0,
                            );


        return redirect()->route('banjar-ajuan-kelahiran-home')->with('success', 'Ajuan Data Kelahiran berhasil disahkan');
    }
}