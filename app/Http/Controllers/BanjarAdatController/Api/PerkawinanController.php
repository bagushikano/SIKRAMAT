<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KramaMipil;
use App\Models\Notifikasi;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Perkawinan;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Models\User;
use App\Helper\Helper;

class PerkawinanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get(Request $req)
    {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;

        $perkawinan = Perkawinan::query();

        if ($req->query('status') == '3') {
            $perkawinan->where('status_perkawinan', '3');
        } else if ($req->query('status') == '2') {
            $perkawinan->where('status_perkawinan', '2');
        } else if ($req->query('status') == '1') {
            $perkawinan->where('status_perkawinan', '1');
        } else if ($req->query('status') == '0') {
            $perkawinan->where('status_perkawinan', '0');
        }

        if ( $req->query('type') ) {
            $perkawinan->where('jenis_perkawinan', $req->query('type'));
        }

        if ( $req->query('start_date') ) {
            // $rentang_waktu = explode(' - ', $request->rentang_waktu);
            // $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            // $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));

            $perkawinan->whereBetween('tanggal_perkawinan', [$req->query('start_date'), $req->query('end_date')]);
        }

        $perkawinan->where(function ($query) use ($banjar_adat_id) {
                                $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                                    ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
                            })
                            ->with('purusa.penduduk', 'pradana.penduduk')
                            ->orderBy('tanggal_perkawinan', 'desc');

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan->paginate(10),
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function detail(Request $req)
    {
        $perkawinan = Perkawinan::where('id', $req->query('perkawinan'))
                                    ->with('purusa.penduduk', 'pradana.penduduk', 'banjar_adat_purusa.desa_adat.kecamatan.kabupaten',
                                            'banjar_adat_pradana.desa_adat.kecamatan.kabupaten', 'desa_adat_pradana', 'desa_adat_purusa',
                                            'desa_asal_pasangan.kecamatan.kabupaten.provinsi', 'desa_asal_pradana.kecamatan.kabupaten.provinsi')
                                    ->first();
        $sisiPradana = false;
        if (Auth::user()->prajuru_banjar_adat->banjar_adat_id == $perkawinan->banjar_adat_pradana_id) {
            $sisiPradana = true;
        }
        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan,
                'sisi_pradana' => $sisiPradana,
                'message' => 'data perkawinan sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function getPurusa(Request $req)
    {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;

        // ambil id pradana dari perkawinan untuk di exclude dari list datanya
        $krama_pradana = Perkawinan::pluck('pradana_id')->toArray();

         // get krama mipil dan istri yg masih aktif untuk di filter dari list purusa nya
        $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('id')->toArray();
        $arr_krama_istri_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->where('status', '1')
                                            ->where('status_hubungan', 'istri')
                                            ->pluck('cacah_krama_mipil_id')
                                            ->toArray();
        $kramaIdToExclude = array_merge($krama_pradana, $arr_krama_istri_id);

        // get cacah krama sesuai id yg di exclude
        $kramas = CacahKramaMipil::with('penduduk', 'tempekan')
                        ->whereHas('penduduk', function($q) use($req) {
                            $q->like('nama', $req->query('nama'));
                        })
                        ->where('banjar_adat_id', $banjar_adat_id)
                        ->where('status', '1')
                        ->whereNotIn('id', $kramaIdToExclude)
                        ->paginate(10);

        if ($kramas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
                'message' => 'data krama perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama perkawinan fail'
            ], 500);
        }
    }

    public function getPradana(Request $req)
    {
        /**
         * query param
         * purusa_id -> id purusa
         * banjar_adat_id -> untuk perkawinan beda banjar
         * jenis_perkawinan -> jenis perkawinan yang gunanya untuk nge exclude (0 satu banjar, 1 beda banjar)
         */

        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;

        //nge exclude yg jadi krama mipil
        $krama_kk = KramaMipil::pluck('cacah_krama_mipil_id')->toArray();

        if ($req->query('purusa_id') != null) {
            $purusa = CacahKramaMipil::with('penduduk')->where('id', $req->query('purusa_id'))->first();

            //untuk exclude pradana yg udh ada di perkawinan yg udh ada
            $krama_pradana = Perkawinan::pluck('pradana_id')->toArray();

            //untuk exclude anggota purusa (biar nggak krama mipil nikah sama anggotanya) atau anggota keluarga nikah di keluarga yang sama
            $purusa_as_kk = KramaMipil::where('cacah_krama_mipil_id', $req->query('purusa_id'))->where('status', '1')->first();
            if ($purusa_as_kk){
                $arr_anggota_keluarga_id = AnggotaKramaMipil::where('krama_mipil_id', $purusa_as_kk->id)->where('status', '1')->pluck('cacah_krama_mipil_id')->toArray();
            } else {
                $purusa_as_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $req->query('purusa_id'))->where('status', '1')->first();
                $kk_purusa = KramaMipil::find($purusa_as_anggota->krama_mipil_id);
                $arr_anggota_keluarga_id = AnggotaKramaMipil::where('krama_mipil_id', $kk_purusa->id)->where('status', '1')->pluck('cacah_krama_mipil_id')->toArray();
            }
        } else {
            $krama_pradana = [];
            $arr_anggota_keluarga_id = [];
        }

        //untuk nge exclude istri orang lain
        $arr_krama_mipil_id = KramaMipil::where('status', '1')->pluck('id')->toArray();
        $arr_krama_istri_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)
                                   ->where('status', '1')->where('status_hubungan', 'istri')
                                   ->pluck('cacah_krama_mipil_id')
                                   ->toArray();
        $kramaIdToExclude = array_merge($krama_kk, $krama_pradana, $arr_anggota_keluarga_id, $arr_krama_istri_id);

        if ($req->query('jenis_perkawinan') == 0) {
            // satu banjar adat
            $kramas = CacahKramaMipil::with('penduduk', 'tempekan')
                                        ->whereHas('penduduk', function($query) use ($purusa) {
                                            $query->where('jenis_kelamin', '!=', $purusa->penduduk->jenis_kelamin);
                                        })
                                        ->whereHas('penduduk', function($q) use($req) {
                                            $q->like('nama', $req->query('nama'));
                                        })
                                        ->with('tempekan')
                                        ->where('status', '1')
                                        ->where('banjar_adat_id',$banjar_adat_id)
                                        ->whereNotIn('id', $kramaIdToExclude)
                                        ->paginate(10);
        } else if ($req->query('jenis_perkawinan') == 1) {
            // beda banjar adat
            $kramas = CacahKramaMipil::with('penduduk', 'tempekan')
                                        ->whereHas('penduduk', function($query) use ($purusa) {
                                            $query->where('jenis_kelamin', '!=', $purusa->penduduk->jenis_kelamin);
                                        })
                                        ->whereHas('penduduk', function($q) use($req) {
                                            $q->like('nama', $req->query('nama'));
                                        })
                                        ->where('status', '1')
                                        ->where('banjar_adat_id', $req->query('banjar_adat_id'))
                                        ->whereNotIn('id', $kramaIdToExclude)
                                        ->paginate(10);

        } else if ($req->query('jenis_perkawinan') == 2) {
                                        // campuran keluar
                                        $kramas = CacahKramaMipil::with('penduduk', 'tempekan')
                                        ->whereHas('penduduk', function($q) use($req) {
                                            $q->like('nama', $req->query('nama'));
                                        })
                                        ->with('tempekan')
                                        ->where('status', '1')
                                        ->where('banjar_adat_id',$banjar_adat_id)
                                        ->whereNotIn('id', $kramaIdToExclude)
                                        ->paginate(10);
        }
        if ($kramas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
                'message' => 'data krama perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama perkawinan fail'
            ], 500);
        }
    }

    public function store_satu_banjar_adat(Request $request)
    {
        /**
         * form data
         * purusa -> id purusa
         * pradana -> id pradana
         * file_bukti_serah_terima_perkawinan -> file
         * nomor_bukti_serah_terima_perkawinan
         * file_akta_perkawinan -> file
         * nomor_akta_perkawinan -> max 21
         * tanggal_perkawinan
         * status_kekeluargaan -> tetap,baru,
         * nama_pemuput
         *
         *
         */
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
        //     'purusa' => 'required',
        //     'pradana' => 'required',
        //     'file_bukti_serah_terima_perkawinan' => 'required',
        //     'tanggal_perkawinan' => 'required',
        //     'status_kekeluargaan' => 'required',
        //     'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
        //     'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',
        // ],[
        //     'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
        //     'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
        //     'purusa.required' => "Purusa wajib dipilih",
        //     'pradana.required' => "Pradana wajib dipilih",
        //     'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
        //     'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
        //     'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
        //     'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
        //     'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
        //     'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
        //     'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }
        //GET PURUSA DAN PRADANA
        $purusa = CacahKramaMipil::with('penduduk')->find($request->purusa);
        $pradana = CacahKramaMipil::with('penduduk')->find($request->pradana);



        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        $banjar_adat_pradana = BanjarAdat::find($banjar_adat_id);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);


        //STATUS PERKAWINAN DRAFT/SAH
        $perkawinan = new Perkawinan();
        $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
        $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
        $perkawinan->jenis_perkawinan = 'satu_banjar_adat';
        $perkawinan->purusa_id = $request->purusa;
        $perkawinan->pradana_id = $request->pradana;
        $perkawinan->banjar_adat_purusa_id = $banjar_adat_id;
        $perkawinan->banjar_adat_pradana_id = $banjar_adat_id;
        $perkawinan->desa_adat_purusa_id = $desa_adat_purusa->id;
        $perkawinan->desa_adat_pradana_id = $desa_adat_pradana->id;
        $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
        $perkawinan->keterangan = $request->keterangan;
        $perkawinan->status_kekeluargaan = $request->status_kekeluargaan;
        if($request->status_kekeluargaan == 'baru'){
            $perkawinan->calon_krama_id = $request->calon_kepala_keluarga;
        }

        $perkawinan->nama_pemuput = $request->nama_pemuput;
        $perkawinan->status_perkawinan = '0';

        if($request->file('file_bukti_serah_terima_perkawinan')!=""){
            $file = $request->file('file_bukti_serah_terima_perkawinan');
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perkawinan->file_bukti_serah_terima_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_perkawinan')!=""){
            $file = $request->file('file_akta_perkawinan');
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perkawinan->file_akta_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        $perkawinan->save();

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perkawinan::where('id', $perkawinan->id)->with('purusa.penduduk', 'pradana.penduduk')->first(),
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function sah_satu_banjar_adat(Request $req)
    {
        $perkawinan = Perkawinan::with('purusa', 'pradana')->where('id', $req->id_perkawinan)->first();
        $perkawinan->status_perkawinan = '3';
        $perkawinan->save();
        $purusa = CacahKramaMipil::with('penduduk')->find($perkawinan->purusa_id);
        $pradana = CacahKramaMipil::with('penduduk')->find($perkawinan->pradana_id);

        //Kekeluargaan
        if($perkawinan->status_kekeluargaan == 'tetap'){
            //GET KK LAMA PURUSA
            $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa->id)->where('status', '1')->first();
            if(!$krama_mipil_purusa_lama){
                $is_kk = 0;
                $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa)->where('status', '1')->first();
                $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
            }else{
                $is_kk = 1;
                $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
            }

            //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PRADANA KE PURUSA)
            //GET KK LAMA PRADANA
            $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->pradana->id)->where('status', '1')->first();
            if($pradana_sebagai_anggota){
                $krama_mipil_pradana_lama = KramaMipil::find($pradana_sebagai_anggota->krama_mipil_id);
                $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();

                //1. COPY DATA KK PRADANA
                $krama_mipil_pradana_baru = new KramaMipil();
                $krama_mipil_pradana_baru->nomor_krama_mipil = $krama_mipil_pradana_lama->nomor_krama_mipil;
                $krama_mipil_pradana_baru->banjar_adat_id = $krama_mipil_pradana_lama->banjar_adat_id;
                $krama_mipil_pradana_baru->cacah_krama_mipil_id = $krama_mipil_pradana_lama->cacah_krama_mipil_id;
                $krama_mipil_pradana_baru->kedudukan_krama_mipil = $krama_mipil_pradana_lama->kedudukan_krama_mipil;
                $krama_mipil_pradana_baru->jenis_krama_mipil = $krama_mipil_pradana_lama->jenis_krama_mipil;
                $krama_mipil_pradana_baru->status = '1';
                $krama_mipil_pradana_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perkawinan)';
                $krama_mipil_pradana_baru->tanggal_registrasi = $krama_mipil_pradana_lama->tanggal_registrasi;
                $krama_mipil_pradana_baru->save();

                //2. COPY ANGGOTA LAMA PRADANA
                foreach($anggota_krama_mipil_pradana_lama as $anggota_lama_pradana){
                    if($anggota_lama_pradana->cacah_krama_mipil_id != $perkawinan->pradana->id){
                        $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                        $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                        $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                        $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                        $anggota_krama_mipil_pradana_baru->status = '1';
                        $anggota_krama_mipil_pradana_baru->save();
                    }else{
                        $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                        $anggota_lama_pradana->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                    }
                    //NONAKTIFKAN ANGGOTA LAMA
                    $anggota_lama_pradana->status = '0';
                    $anggota_lama_pradana->update();
                }

                //3. LEBUR KK PRADANA LAMA
                $krama_mipil_pradana_lama->status = '0';
                $krama_mipil_pradana_lama->update();
            }

            //4. COPY DATA KK PURUSA
            $krama_mipil_purusa_baru = new KramaMipil();
            $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
            $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
            $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
            $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
            $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
            $krama_mipil_purusa_baru->status = '1';
            $krama_mipil_purusa_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru (Perkawinan)';
            $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
            $krama_mipil_purusa_baru->save();

            //5. COPY ANGGOTA LAMA PURUSA
            foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                $anggota_krama_mipil_purusa_baru->status = '1';
                $anggota_krama_mipil_purusa_baru->save();

                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama_purusa->status = '0';
                $anggota_lama_purusa->update();
            }

            //6. MASUKKAN PRADANA KE KK PURUSA
            $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
            $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
            $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $perkawinan->pradana->id;
            if($is_kk){
                if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                    $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                }else{
                    $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                }
            }else{
                $anggota_krama_mipil_purusa_baru->status_hubungan = 'menantu';
            }
            $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
            $anggota_krama_mipil_purusa_baru->status = '1';
            $anggota_krama_mipil_purusa_baru->save();

            //7. LEBUR KK PURUSA LAMA
            $krama_mipil_purusa_lama->status = '0';
            $krama_mipil_purusa_lama->update();
        }else if($perkawinan->status_kekeluargaan == 'baru'){
            //GET CALON KK
            if($perkawinan->calon_krama_id == $perkawinan->purusa->id){
                $calon_kk = 'purusa';
            }else if($perkawinan->calon_krama_id == $perkawinan->pradana->id){
                $calon_kk = 'pradana';
            }

            //IF CALON KK IS PURUSA/PRADANA
            if($calon_kk == 'purusa'){
                //GET KK LAMA PURUSA
                $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa->id)->where('status', '1')->first();
                if(!$krama_mipil_purusa_lama){
                    $is_kk = 0;
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa->id)->where('status', '1')->first();
                    $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }else{
                    $is_kk = 1;
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }

                //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PRADANA KE PURUSA)
                //GET KK LAMA PRADANA
                $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->pradana->id)->where('status', '1')->first();
                if($pradana_sebagai_anggota){
                    $krama_mipil_pradana_lama = KramaMipil::find($pradana_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();

                    //1. COPY DATA KK PRADANA
                    $krama_mipil_pradana_baru = new KramaMipil();
                    $krama_mipil_pradana_baru->nomor_krama_mipil = $krama_mipil_pradana_lama->nomor_krama_mipil;
                    $krama_mipil_pradana_baru->banjar_adat_id = $krama_mipil_pradana_lama->banjar_adat_id;
                    $krama_mipil_pradana_baru->cacah_krama_mipil_id = $krama_mipil_pradana_lama->cacah_krama_mipil_id;
                    $krama_mipil_pradana_baru->kedudukan_krama_mipil = $krama_mipil_pradana_lama->kedudukan_krama_mipil;
                    $krama_mipil_pradana_baru->jenis_krama_mipil = $krama_mipil_pradana_lama->jenis_krama_mipil;
                    $krama_mipil_pradana_baru->status = '1';
                    $krama_mipil_pradana_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perkawinan)';
                    $krama_mipil_pradana_baru->tanggal_registrasi = $krama_mipil_pradana_lama->tanggal_registrasi;
                    $krama_mipil_pradana_baru->save();

                    //2. COPY ANGGOTA LAMA PRADANA
                    foreach($anggota_krama_mipil_pradana_lama as $anggota_lama_pradana){
                        if($anggota_lama_pradana->cacah_krama_mipil_id != $perkawinan->pradana->id){
                            $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                            $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                            $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                            $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                            $anggota_krama_mipil_pradana_baru->status = '1';
                            $anggota_krama_mipil_pradana_baru->save();
                        }else{
                            $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                            $anggota_lama_pradana->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                        }
                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama_pradana->status = '0';
                        $anggota_lama_pradana->update();
                    }

                    //3. LEBUR KK PRADANA LAMA
                    $krama_mipil_pradana_lama->status = '0';
                    $krama_mipil_pradana_lama->update();
                }

                //IF PURUSA KK/ANGGOTA
                if($is_kk){
                    //COPY DATA KK PURUSA
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                    $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                    $krama_mipil_purusa_baru->save();

                    //COPY DATA ANGGOTA KK PURUSA
                    foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                        $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                        $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                        $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                        $anggota_krama_mipil_purusa_baru->status = '1';
                        $anggota_krama_mipil_purusa_baru->save();
                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama_purusa->status = '0';
                        $anggota_lama_purusa->update();
                    }

                    //LEBUR KK PURUSA LAMA
                    $krama_mipil_purusa_lama->status = '0';
                    $krama_mipil_purusa_lama->update();

                    //MASUKKAN PRADANA KE KK PURUSA
                    $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                    $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $perkawinan->pradana->id;
                    if($perkawinan->pradana->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $anggota_krama_mipil_purusa_baru->status = '1';
                    $anggota_krama_mipil_purusa_baru->save();

                }else{
                    //COPY DATA KK LAMA PURUSA
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                    $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                    $krama_mipil_purusa_baru->save();

                    //COPY DATA ANGGOTA LAMA PURUSA
                    foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                        if($anggota_lama_purusa->cacah_krama_mipil_id != $perkawinan->purusa->id){
                            $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                            $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                            $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                            $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                            $anggota_krama_mipil_purusa_baru->status = '1';
                            $anggota_krama_mipil_purusa_baru->save();
                        }else{
                            $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                            $anggota_lama_purusa->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                        }
                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama_purusa->status = '0';
                        $anggota_lama_purusa->update();
                    }

                    //LEBUR KK PURUSA LAMA
                    $krama_mipil_purusa_lama->status = '0';
                    $krama_mipil_purusa_lama->update();

                    //GENERATE NOMOR KK BARU
                    $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
                    $banjar_adat = BanjarAdat::find($banjar_adat_id);
                    $tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
                    $curr_year = Carbon::parse($tanggal_registrasi)->year;
                    $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
                    $curr_year = Carbon::now()->format('y');
                    $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
                    $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
                    if($jumlah_krama_bulan_regis_sama < 10){
                        $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
                    }else if($jumlah_krama_bulan_regis_sama < 100){
                        $nomor_krama_mipil = $nomor_krama_mipil.'0'.$jumlah_krama_bulan_regis_sama;
                    }else if($jumlah_krama_bulan_regis_sama < 1000){
                        $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
                    }

                    //PEMBENTUKAN KK PURUSA BARU
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $perkawinan->purusa->id;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $krama_mipil_purusa_baru->save();

                    //9. MASUKKAN PRADANA KE KK PURUSA
                    $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                    $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $perkawinan->pradana->id;
                    if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $anggota_krama_mipil_purusa_baru->status = '1';
                    $anggota_krama_mipil_purusa_baru->save();
                }
            }else if($calon_kk == 'pradana'){
                //GET KK LAMA PRADANA
                $krama_mipil_pradana_lama = KramaMipil::where('cacah_krama_mipil_id', $perkawinan->pradana->id)->where('status', '1')->first();
                if(!$krama_mipil_pradana_lama){
                    $is_kk = 0;
                    $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->pradana->id)->where('status', '1')->first();
                    $krama_mipil_pradana_lama = KramaMipil::find($pradana_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();
                }else{
                    $is_kk = 1;
                    $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();
                }

                //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PURUSA KE PRADANA)
                //GET KK LAMA PURUSA
                $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa->id)->where('status', '1')->first();
                if($purusa_sebagai_anggota){
                    $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();

                    //1. COPY DATA KK purusa
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                    $krama_mipil_purusa_baru->save();

                    //2. COPY ANGGOTA LAMA purusa
                    foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                        if($anggota_lama_purusa->cacah_krama_mipil_id != $perkawinan->purusa->id){
                            $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                            $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                            $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                            $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                            $anggota_krama_mipil_purusa_baru->status = '1';
                            $anggota_krama_mipil_purusa_baru->save();
                        }else{
                            $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                            $anggota_lama_purusa->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                        }
                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama_purusa->status = '0';
                        $anggota_lama_purusa->update();
                    }

                    //3. LEBUR KK purusa LAMA
                    $krama_mipil_purusa_lama->status = '0';
                    $krama_mipil_purusa_lama->update();
                }

                //COPY DATA KK LAMA PRADANA
                $krama_mipil_pradana_baru = new KramaMipil();
                $krama_mipil_pradana_baru->nomor_krama_mipil = $krama_mipil_pradana_lama->nomor_krama_mipil;
                $krama_mipil_pradana_baru->banjar_adat_id = $krama_mipil_pradana_lama->banjar_adat_id;
                $krama_mipil_pradana_baru->cacah_krama_mipil_id = $krama_mipil_pradana_lama->cacah_krama_mipil_id;
                $krama_mipil_pradana_baru->kedudukan_krama_mipil = $krama_mipil_pradana_lama->kedudukan_krama_mipil;
                $krama_mipil_pradana_baru->jenis_krama_mipil = $krama_mipil_pradana_lama->jenis_krama_mipil;
                $krama_mipil_pradana_baru->kedudukan_krama_mipil = $krama_mipil_pradana_lama->kedudukan_krama_mipil;
                $krama_mipil_pradana_baru->jenis_krama_mipil = $krama_mipil_pradana_lama->jenis_krama_mipil;
                $krama_mipil_pradana_baru->status = '1';
                $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                $krama_mipil_pradana_baru->tanggal_registrasi = $krama_mipil_pradana_lama->tanggal_registrasi;
                $krama_mipil_pradana_baru->save();

                //COPY DATA ANGGOTA LAMA PRADANA
                foreach($anggota_krama_mipil_pradana_lama as $anggota_lama_pradana){
                    if($anggota_lama_pradana->cacah_krama_mipil_id != $perkawinan->pradana->id){
                        $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                        $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                        $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                        $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                        $anggota_krama_mipil_pradana_baru->status = '1';
                        $anggota_krama_mipil_pradana_baru->save();
                    }else{
                        $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                        $anggota_lama_pradana->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                    }

                    //NONAKTIFKAN ANGGOTA LAMA
                    $anggota_lama_pradana->status = '0';
                    $anggota_lama_pradana->update();
                }

                //LEBUR KK PURUSA LAMA
                $krama_mipil_pradana_lama->status = '0';
                $krama_mipil_pradana_lama->update();

                //GENERATE NOMOR KK BARU
                $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
                $banjar_adat = BanjarAdat::find($banjar_adat_id);
                $tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
                $curr_year = Carbon::parse($tanggal_registrasi)->year;
                $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
                $curr_year = Carbon::now()->format('y');
                $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
                $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
                if($jumlah_krama_bulan_regis_sama < 10){
                    $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
                }else if($jumlah_krama_bulan_regis_sama < 100){
                    $nomor_krama_mipil = $nomor_krama_mipil.'0'.$jumlah_krama_bulan_regis_sama;
                }else if($jumlah_krama_bulan_regis_sama < 1000){
                    $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
                }

                //8. PEMBENTUKAN KK PRADANA BARU
                $krama_mipil_pradana_baru = new KramaMipil();
                $krama_mipil_pradana_baru->nomor_krama_mipil = $nomor_krama_mipil;
                $krama_mipil_pradana_baru->banjar_adat_id = $krama_mipil_pradana_lama->banjar_adat_id;
                $krama_mipil_pradana_baru->cacah_krama_mipil_id = $perkawinan->pradana->id;
                $krama_mipil_pradana_baru->status = '1';
                $krama_mipil_pradana_baru->kedudukan_krama_mipil = 'pradana';
                $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                $krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                $krama_mipil_pradana_baru->save();

                //9. MASUKKAN PURUSA KE KK PRADANA
                $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $perkawinan->purusa->id;

                if($purusa->penduduk->jenis_kelamin == 'perempuan'){
                    $anggota_krama_mipil_pradana_baru->status_hubungan = 'istri';
                }else{
                    $anggota_krama_mipil_pradana_baru->status_hubungan = 'suami';
                }

                $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                $anggota_krama_mipil_pradana_baru->status = '1';
                $anggota_krama_mipil_pradana_baru->save();
            }
        }

        //Alamat dan Status Kawin
        $purusa = CacahKramaMipil::find($perkawinan->purusa->id);
        $pradana = CacahKramaMipil::find($perkawinan->pradana->id);
        $penduduk_purusa = Penduduk::find($purusa->penduduk_id);
        $penduduk_pradana = Penduduk::find($pradana->penduduk_id);

        //Status Kawin
        $penduduk_purusa->status_perkawinan = 'kawin';
        $penduduk_pradana->status_perkawinan = 'kawin';

        //Alamat
        $penduduk_pradana->alamat = $penduduk_purusa->alamat;
        $penduduk_pradana->koordinat_alamat = $penduduk_purusa->koordinat_alamat;
        $penduduk_pradana->desa_id = $penduduk_purusa->desa_id;

        //Update
        $penduduk_purusa->update();
        $penduduk_pradana->update();

        $perkawinan->save();

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan,
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => $perkawinan,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function store_beda_banjar_adat(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
        //     'banjar_adat_pradana' => 'required',
        //     'purusa' => 'required',
        //     'pradana' => 'required',
        //     'file_bukti_serah_terima_perkawinan' => 'required',
        //     'tanggal_perkawinan' => 'required',
        //     'status_kekeluargaan' => 'required',
        //     'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
        //     'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',
        // ],[
        //     'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
        //     'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
        //     'banjar_adat_pradana.required' => "Banjar Adat Pradana wajib dipilih",
        //     'purusa.required' => "Purusa wajib dipilih",
        //     'pradana.required' => "Pradana wajib dipilih",
        //     'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
        //     'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
        //     'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
        //     'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
        //     'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
        //     'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
        //     'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        error_log($request->banjar_adat_pradana);

        $banjar_adat_pradana = BanjarAdat::find($request->banjar_adat_pradana);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);


        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        //STATUS PERKAWINAN DRAFT/SAH
        $perkawinan = new Perkawinan();
        $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
        $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
        $perkawinan->jenis_perkawinan = 'beda_banjar_adat';
        $perkawinan->purusa_id = $request->purusa;
        $perkawinan->pradana_id = $request->pradana;
        $perkawinan->banjar_adat_purusa_id = $banjar_adat_id;
        $perkawinan->banjar_adat_pradana_id = $request->banjar_adat_pradana;
        $perkawinan->desa_adat_purusa_id = $desa_adat_purusa->id;
        $perkawinan->desa_adat_pradana_id = $desa_adat_pradana->id;
        $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
        $perkawinan->status_kekeluargaan = $request->status_kekeluargaan;
        $perkawinan->status_perkawinan = 0;
        $perkawinan->keterangan = $request->keterangan;
        if($request->status_kekeluargaan == 'baru'){
            $perkawinan->calon_krama_id = $request->calon_kepala_keluarga;
        }
        $perkawinan->nama_pemuput = $request->nama_pemuput;
        if($request->file('file_bukti_serah_terima_perkawinan')!=""){
            $file = $request->file('file_bukti_serah_terima_perkawinan');
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perkawinan->file_bukti_serah_terima_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_perkawinan')!=""){
            $file = $request->file('file_akta_perkawinan');
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perkawinan->file_akta_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $perkawinan->save();

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();
        $notifikasi->notif_create_perkawinan_beda_banjar_adat($banjar_adat_pradana->id, $banjar_adat_purusa->id, $perkawinan->id);

        $userNotif = new User();
        error_log('ngirim notif perkawinan');

        $banjarAdatPurusa = BanjarAdat::find($banjar_adat_purusa->id);
        $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perkawinan->tanggal_perkawinan);

        $kontenNotif = "Terdapat ajuan perkawinan dari Banjar Adat ".$banjarAdatPurusa->nama_banjar_adat." Desa Adat ".$desaAdatPurusa->desadat_nama." pada tanggal ".$tanggal_kawin.".";


        $userNotif->sendNotifPendataan(
                                $kontenNotif,
                                null,
                                "Ajuan Perkawinan baru.",
                                $banjar_adat_pradana->id,
                                $perkawinan->id,
                                2,
                            );

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perkawinan::where('id', $perkawinan->id)->with('purusa.penduduk', 'pradana.penduduk')->first(),
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function editBedaBanjar(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
        //     'banjar_adat_pradana' => 'required',
        //     'purusa' => 'required',
        //     'pradana' => 'required',
        //     'file_bukti_serah_terima_perkawinan' => 'required',
        //     'tanggal_perkawinan' => 'required',
        //     'status_kekeluargaan' => 'required',
        //     'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
        //     'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',
        // ],[
        //     'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
        //     'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
        //     'banjar_adat_pradana.required' => "Banjar Adat Pradana wajib dipilih",
        //     'purusa.required' => "Purusa wajib dipilih",
        //     'pradana.required' => "Pradana wajib dipilih",
        //     'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
        //     'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
        //     'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
        //     'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
        //     'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
        //     'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
        //     'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        error_log($request->banjar_adat_pradana);

        $banjar_adat_pradana = BanjarAdat::find($request->banjar_adat_pradana);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);


        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        //STATUS PERKAWINAN DRAFT/SAH
        $perkawinan = Perkawinan::where('id', $request->id)->first();
        $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
        $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
        $perkawinan->jenis_perkawinan = 'beda_banjar_adat';
        $perkawinan->purusa_id = $request->purusa;
        $perkawinan->pradana_id = $request->pradana;
        $perkawinan->banjar_adat_purusa_id = $banjar_adat_id;
        $perkawinan->banjar_adat_pradana_id = $request->banjar_adat_pradana;
        $perkawinan->desa_adat_purusa_id = $desa_adat_purusa->id;
        $perkawinan->desa_adat_pradana_id = $desa_adat_pradana->id;
        $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
        $perkawinan->status_kekeluargaan = $request->status_kekeluargaan;
        $perkawinan->status_perkawinan = 0;
        $perkawinan->keterangan = $request->keterangan;
        if($request->status_kekeluargaan == 'baru'){
            $perkawinan->calon_krama_id = $request->calon_kepala_keluarga;
        }
        $perkawinan->nama_pemuput = $request->nama_pemuput;
        if($request->file('file_bukti_serah_terima_perkawinan')!=""){
            $file = $request->file('file_bukti_serah_terima_perkawinan');
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perkawinan->file_bukti_serah_terima_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_perkawinan')!=""){
            $file = $request->file('file_akta_perkawinan');
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perkawinan->file_akta_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $perkawinan->save();

        /**
         * create notif baru
         */

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perkawinan::where('id', $perkawinan->id)->with('purusa.penduduk', 'pradana.penduduk')->first(),
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    //di pake untuk sahkan perkawinan beda banjar ketika di banjar adat pradana udh setuju
    public function konfirmasi_perkawinan_masuk(Request $req){
        $perkawinan = Perkawinan::find($req->id_perkawinan);

        //GET DATA PURUSA
        $purusa = CacahKramaMipil::find($perkawinan->purusa_id);
        $banjar_adat_purusa = BanjarAdat::find($perkawinan->banjar_adat_purusa_id);
        $desa_adat_purusa_id = DesaAdat::find($perkawinan->desa_adat_purusa_id);

        //GET DATA PRADANA
        $pradana = CacahKramaMipil::find($perkawinan->pradana_id);
        $penduduk_pradana = Penduduk::find($pradana->penduduk_id);
        $banjar_adat_pradana = BanjarAdat::find($perkawinan->banjar_adat_pradana_id);
        $desa_adat_pradana_id = DesaAdat::find($perkawinan->desa_adat_pradana_id);

        //TRANSAKSI PERKAWINAN
        //2. PINDAHKAN PRADANA DARI CACAH ASAL KE CACAH TUJUAN
        //NOMOR CACAH KRAMA
        $banjar_adat = $banjar_adat_purusa;
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat->id)->pluck('penduduk_id')->toArray();
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk_pradana->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $nomor_cacah_krama_mipil = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'01'.Carbon::parse($penduduk_pradana->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.$jumlah_penduduk_tanggal_sama;
        }

        //BENTUK CACAH BARU PRADANA
        $cacah_krama_mipil = new CacahKramaMipil();
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_purusa->id;
        $cacah_krama_mipil->tempekan_id = $purusa->tempekan_id;
        $cacah_krama_mipil->penduduk_id = $penduduk_pradana->id;
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
        $cacah_krama_mipil->jenis_kependudukan = $purusa->jenis_kependudukan;
        $cacah_krama_mipil->status = '1';
        if($purusa->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $purusa->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();

        //GET PURUSA DAN PRADANA
        $purusa = CacahKramaMipil::with('penduduk')->find($perkawinan->purusa_id);
        $pradana = $cacah_krama_mipil;

        //3. JENIS KEKELUARGAAN
        if($perkawinan->status_kekeluargaan == 'tetap'){
            //PINDAHKAN PRADANA KE KELUARGA PURUSA
            $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $purusa->id)->where('status', '1')->first();
            if(!$krama_mipil_purusa_lama){
                $is_kk = 0;
                $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $purusa->id)->where('status', '1')->first();
                $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
            }else{
                $is_kk = 1;
                $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
            }

            //COPY DATA KK PURUSA
            $krama_mipil_purusa_baru = new KramaMipil();
            $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
            $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
            $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
            $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
            $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
            $krama_mipil_purusa_baru->status = '1';
            $krama_mipil_purusa_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru (Perkawinan)';
            $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
            $krama_mipil_purusa_baru->save();

            //5. COPY ANGGOTA LAMA PURUSA
            foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                $anggota_krama_mipil_purusa_baru->status = '1';
                $anggota_krama_mipil_purusa_baru->save();

                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama_purusa->status = '0';
                $anggota_lama_purusa->update();
            }

            //6. MASUKKAN PRADANA KE KK PURUSA
            $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
            $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
            $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            if($is_kk){
                if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                    $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                }else{
                    $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                }
            }else{
                $anggota_krama_mipil_purusa_baru->status_hubungan = 'menantu';
            }
            $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
            $anggota_krama_mipil_purusa_baru->status = '1';
            $anggota_krama_mipil_purusa_baru->save();

            //7. LEBUR KK PURUSA LAMA
            $krama_mipil_purusa_lama->status = '0';
            $krama_mipil_purusa_lama->update();
        }else if($perkawinan->status_kekeluargaan == 'baru'){
            //GET CALON KK
            if($perkawinan->calon_krama_id == $perkawinan->purusa_id){
                $calon_kk = 'purusa';
            }else if($perkawinan->calon_krama_id == $perkawinan->pradana_id){
                $calon_kk = 'pradana';
            }

            //IF CALON KK IS PURUSA/PRADANA
            if($calon_kk == 'purusa'){
                //GET KK LAMA PURUSA
                $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
                if(!$krama_mipil_purusa_lama){
                    $is_kk = 0;
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
                    $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }else{
                    $is_kk = 1;
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }

                //IF PURUSA KK/ANGGOTA
                if($is_kk){
                    //COPY DATA KK PURUSA
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                    $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                    $krama_mipil_purusa_baru->save();

                    //COPY DATA ANGGOTA KK PURUSA
                    foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                        $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                            $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                            $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                            $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                            $anggota_krama_mipil_purusa_baru->status = '1';
                            $anggota_krama_mipil_purusa_baru->save();
                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama_purusa->status = '0';
                        $anggota_lama_purusa->update();
                    }

                    //LEBUR KK PURUSA LAMA
                    $krama_mipil_purusa_lama->status = '0';
                    $krama_mipil_purusa_lama->update();

                    //MASUKKAN PRADANA KE KK PURUSA
                    $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                    $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $pradana->id;
                    if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $anggota_krama_mipil_purusa_baru->status = '1';
                    $anggota_krama_mipil_purusa_baru->save();
                }else{
                    //COPY DATA KK LAMA PURUSA
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                    $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                    $krama_mipil_purusa_baru->save();

                    //COPY DATA ANGGOTA LAMA PURUSA
                    foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                        if($anggota_lama_purusa->cacah_krama_mipil_id != $perkawinan->purusa_id){
                            $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                            $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                            $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                            $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                            $anggota_krama_mipil_purusa_baru->status = '1';
                            $anggota_krama_mipil_purusa_baru->save();
                        }else{
                            $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                            $anggota_lama_purusa->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                        }
                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama_purusa->status = '0';
                        $anggota_lama_purusa->update();
                    }

                    //LEBUR KK PURUSA LAMA
                    $krama_mipil_purusa_lama->status = '0';
                    $krama_mipil_purusa_lama->update();

                    //GENERATE NOMOR KK BARU
                    $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
                    $banjar_adat = BanjarAdat::find($banjar_adat_id);
                    $tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
                    $curr_year = Carbon::parse($tanggal_registrasi)->year;
                    $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
                    $curr_year = Carbon::now()->format('y');
                    $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
                    $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
                    if($jumlah_krama_bulan_regis_sama < 10){
                        $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
                    }else if($jumlah_krama_bulan_regis_sama < 100){
                        $nomor_krama_mipil = $nomor_krama_mipil.'0'.$jumlah_krama_bulan_regis_sama;
                    }else if($jumlah_krama_bulan_regis_sama < 1000){
                        $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
                    }

                    //PEMBENTUKAN KK PURUSA BARU
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $perkawinan->purusa_id;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $krama_mipil_purusa_baru->save();

                    //MASUKKAN PRADANA KE KK PURUSA
                    $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                    $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $pradana->id;
                    if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $anggota_krama_mipil_purusa_baru->status = '1';
                    $anggota_krama_mipil_purusa_baru->save();
                }
            }else if($calon_kk == 'pradana'){
                //GET KK LAMA PURUSA
                $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
                if($purusa_sebagai_anggota){
                    $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();

                    //1. COPY DATA KK purusa
                    $krama_mipil_purusa_baru = new KramaMipil();
                    $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                    $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                    $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                    $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                    $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                    $krama_mipil_purusa_baru->status = '1';
                    $krama_mipil_purusa_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perkawinan)';
                    $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                    $krama_mipil_purusa_baru->save();

                    //2. COPY ANGGOTA LAMA purusa
                    foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                        if($anggota_lama_purusa->cacah_krama_mipil_id != $perkawinan->purusa_id){
                            $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                            $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                            $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                            $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                            $anggota_krama_mipil_purusa_baru->status = '1';
                            $anggota_krama_mipil_purusa_baru->save();
                        }else{
                            $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                            $anggota_lama_purusa->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                        }
                        //NONAKTIFKAN ANGGOTA LAMA
                        $anggota_lama_purusa->status = '0';
                        $anggota_lama_purusa->update();
                    }

                    //3. LEBUR KK purusa LAMA
                    $krama_mipil_purusa_lama->status = '0';
                    $krama_mipil_purusa_lama->update();
                }

                //GENERATE NOMOR KK BARU
                $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
                $banjar_adat = BanjarAdat::find($banjar_adat_id);
                $tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
                $curr_year = Carbon::parse($tanggal_registrasi)->year;
                $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
                $curr_year = Carbon::now()->format('y');
                $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
                $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
                if($jumlah_krama_bulan_regis_sama < 10){
                    $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
                }else if($jumlah_krama_bulan_regis_sama < 100){
                    $nomor_krama_mipil = $nomor_krama_mipil.'0'.$jumlah_krama_bulan_regis_sama;
                }else if($jumlah_krama_bulan_regis_sama < 1000){
                    $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
                }

                //8. PEMBENTUKAN KK PRADANA BARU
                $krama_mipil_pradana_baru = new KramaMipil();
                $krama_mipil_pradana_baru->nomor_krama_mipil = $nomor_krama_mipil;
                $krama_mipil_pradana_baru->banjar_adat_id = $perkawinan->banjar_adat_purusa_id;
                $krama_mipil_pradana_baru->cacah_krama_mipil_id = $pradana->id;
                $krama_mipil_pradana_baru->status = '1';
                $krama_mipil_pradana_baru->kedudukan_krama_mipil = 'pradana';
                $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                $krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                $krama_mipil_pradana_baru->save();

                //9. MASUKKAN PURUSA KE KK PRADANA
                $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $perkawinan->purusa_id;
                if($purusa->penduduk->jenis_kelamin == 'perempuan'){
                    $anggota_krama_mipil_pradana_baru->status_hubungan = 'istri';
                }else{
                    $anggota_krama_mipil_pradana_baru->status_hubungan = 'suami';
                }
                $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                $anggota_krama_mipil_pradana_baru->status = '1';
                $anggota_krama_mipil_pradana_baru->save();
            }
        }

        //UPDATE ALAMAT DAN STATUS KAWIN
        //Alamat dan Status Kawin
        $purusa = $purusa;
        $pradana = $cacah_krama_mipil;
        $penduduk_purusa = Penduduk::find($purusa->penduduk_id);
        $penduduk_pradana = Penduduk::find($pradana->penduduk_id);

        //Status Kawin
        $penduduk_purusa->status_perkawinan = 'kawin';
        $penduduk_pradana->status_perkawinan = 'kawin';

        //Alamat
        $penduduk_pradana->alamat = $penduduk_purusa->alamat;
        $penduduk_pradana->koordinat_alamat = $penduduk_purusa->koordinat_alamat;
        $penduduk_pradana->desa_id = $penduduk_purusa->desa_id;

        //Update
        $penduduk_purusa->update();
        $penduduk_pradana->update();

        //UPDATE PERKAWINAN
        $perkawinan->status_perkawinan = '3';
        $perkawinan->update();

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan,
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => $perkawinan,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function destroy_satu_banjar(Request $request) {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $perkawinan = Perkawinan::find($request->query('perkawinan'));

        if($perkawinan->status_perkawinan == '0' || $perkawinan->status_perkawinan == '2'){
            if($perkawinan->jenis_perkawinan == 'campuran_masuk'){
                $cacah_pradana = CacahKramaMipil::find($perkawinan->pradana_id);
                $penduduk_pradana = Penduduk::find($cacah_pradana->penduduk_id);

                //DELETE SEMUA
                $cacah_pradana->delete();
                $penduduk_pradana->delete();
            }
        }
        $perkawinan->delete();
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => null,
            'sisi_pradana' => null,
            'message' => 'data perkawinan sukses'
        ], 200);
    }

    //di pake buat nolak
    public function tolak_perkawinan_keluar(Request $request){

        $perkawinan = Perkawinan::find($request->id_perkawinan);
        $perkawinan->alasan_penolakan = $request->alasan_penolakan;
        $perkawinan->status_perkawinan = '2';
        $perkawinan->update();

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();
        $notifikasi->notif_tolak_perkawinan_beda_banjar_adat($perkawinan->id);

        $userNotif = new User();
        error_log('ngirim notif perkawinan');

        $banjarAdatPradana = BanjarAdat::find($perkawinan->banjar_adat_pradana_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perkawinan->tanggal_perkawinan);

        $kontenNotif = "Perkawinan dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_kawin." belum dapat dikonfirmasi.";

        $userNotif->sendNotifPendataan(
                                $kontenNotif,
                                null,
                                "Penolakan Ajuan Perkawinan.",
                                $perkawinan->banjar_adat_purusa_id,
                                $perkawinan->id,
                                2,
                            );

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan,
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => $perkawinan,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function konfirmasi_perkawinan_keluar(Request $req){
        //UPDATE DATA PERKAWINAN
        $perkawinan = Perkawinan::find($req->id_perkawinan);
        $perkawinan->status_perkawinan = '1';
        $perkawinan->update();

        //NONAKTIFKAN CACAH
        $pradana = CacahKramaMipil::find($perkawinan->pradana_id);
        $pradana->status = '0';
        $pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
        $pradana->alasan_keluar = 'Perkawinan (Keluar Banjar Adat)';
        $pradana->update();

        //KELUARKAN DARI KELUARGA
        $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $pradana->id)->where('status', '1')->first();
        if($pradana_sebagai_anggota){
            $krama_mipil_pradana_lama = KramaMipil::find($pradana_sebagai_anggota->krama_mipil_id);
            $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();

            //COPY DATA KK PRADANA
            $krama_mipil_pradana_baru = new KramaMipil();
            $krama_mipil_pradana_baru->nomor_krama_mipil = $krama_mipil_pradana_lama->nomor_krama_mipil;
            $krama_mipil_pradana_baru->banjar_adat_id = $krama_mipil_pradana_lama->banjar_adat_id;
            $krama_mipil_pradana_baru->cacah_krama_mipil_id = $krama_mipil_pradana_lama->cacah_krama_mipil_id;
            $krama_mipil_pradana_baru->kedudukan_krama_mipil = $krama_mipil_pradana_lama->kedudukan_krama_mipil;
            $krama_mipil_pradana_baru->jenis_krama_mipil = $krama_mipil_pradana_lama->jenis_krama_mipil;
            $krama_mipil_pradana_baru->status = '1';
            $krama_mipil_pradana_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perkawinan)';
            $krama_mipil_pradana_baru->tanggal_registrasi = $krama_mipil_pradana_lama->tanggal_registrasi;
            $krama_mipil_pradana_baru->save();

            //COPY ANGGOTA LAMA PRADANA
            foreach($anggota_krama_mipil_pradana_lama as $anggota_lama_pradana){
                if($anggota_lama_pradana->cacah_krama_mipil_id != $pradana->id){
                    $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                    $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                    $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                    $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                    $anggota_krama_mipil_pradana_baru->status = '1';
                    $anggota_krama_mipil_pradana_baru->save();
                }else{
                    $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $anggota_lama_pradana->alasan_keluar = 'Perkawinan (Keluar Banjar Adat)';
                }
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama_pradana->status = '0';
                $anggota_lama_pradana->update();
            }

            //LEBUR KK PRADANA LAMA
            $krama_mipil_pradana_lama->status = '0';
            $krama_mipil_pradana_lama->update();
        }

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();

        $notifikasi->notif_konfirmasi_perkawinan_beda_banjar_adat($perkawinan->id);

        $userNotif = new User();
        error_log('ngirim notif perkawinan');

        $banjarAdatPradana = BanjarAdat::find($perkawinan->banjar_adat_pradana_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perkawinan->tanggal_perkawinan);
        $kontenNotif = "Perkawinan dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_kawin." telah dikonfirmasi.";

        error_log('udah kebawah gan');
        $userNotif->sendNotifPendataan(
                                $kontenNotif,
                                null,
                                "Ajuan Perkawinan telah dikonfirmasi.",
                                $perkawinan->banjar_adat_purusa_id,
                                $perkawinan->id,
                                2,
                            );

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan,
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => $perkawinan,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }
    //KELUAR BANJAR ADAT HANDLER

    public function store_campuran_masuk(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
        //     'purusa' => 'required',
        //     'file_bukti_serah_terima_perkawinan' => 'required',
        //     'tanggal_perkawinan' => 'required',
        //     'status_kekeluargaan' => 'required',
        //     'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
        //     'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',

        //     'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
        //     'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
        //     'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
        //     'tanggal_lahir' => 'required',
        //     'jenis_kelamin' => 'required',
        //     'pekerjaan' => 'required',
        //     'pendidikan' => 'required',
        //     'golongan_darah' => 'required',
        //     'alamat' => 'required',
        //     'provinsi' => 'required',
        //     'kabupaten' => 'required',
        //     'kecamatan' => 'required',
        //     'desa' => 'required',
        // ],[
        //     'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
        //     'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
        //     'purusa.required' => "Purusa wajib dipilih",
        //     'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
        //     'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
        //     'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
        //     'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
        //     'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
        //     'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
        //     'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",

        //     'nik.regex' => "NIK hanya boleh mengandung angka",
        //     'nik.unique' => "NIK yang dimasukkan telah terdaftar",
        //     'nama.required' => "Nama wajib diisi",
        //     'nama.regex' => "Nama hanya boleh mengandung huruf",
        //     'tempat_lahir.required' => "Tempat Lahir wajib diisi",
        //     'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
        //     'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
        //     'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
        //     'pekerjaan.required' => "Pekerjaan wajib dipilih",
        //     'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
        //     'golongan_darah.required' => "Golongan Darah wajib dipilih",
        //     'status_perkawinan.required' => "Status Perkawinan wajib dipilih",
        //     'alamat.required' => "Alamat Asal wajib diisi",
        //     'provinsi.required' => "Provinsi Asal wajib dipilih",
        //     'kabupaten.required' => "Kabupaten Asal wajib dipilih",
        //     'kecamatan.required' => "Kecamatan Asal wajib dipilih",
        //     'desa.required' => "Desa/Kelurahan Asal wajib dipilih",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        $perkawinanObject = json_decode($request->perkawinan_json);
        $pradanaObject = json_decode($request->pradana_json);

        //GET DATA PURUSA
        $purusa = CacahKramaMipil::find($request->purusa);
        $penduduk_purusa = Penduduk::find($purusa->penduduk_id);

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        error_log(json_encode($pradanaObject));

        //INSERT PENDUDUK
        $penduduk = new Penduduk();
        $penduduk->nik = $pradanaObject->penduduk->nik;
        $penduduk->gelar_depan = $pradanaObject->penduduk->gelar_depan;
        $penduduk->nama = $pradanaObject->penduduk->nama;
        $penduduk->gelar_belakang =$pradanaObject->penduduk->gelar_belakang;
        $penduduk->nama_alias = $pradanaObject->penduduk->nama_alias;
        $penduduk->tempat_lahir =$pradanaObject->penduduk->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($pradanaObject->penduduk->tanggal_lahir));
        $penduduk->agama = 'hindu';
        $penduduk->jenis_kelamin = $pradanaObject->penduduk->jenis_kelamin;
        $penduduk->golongan_darah = $pradanaObject->penduduk->golongan_darah;
        $penduduk->profesi_id = $pradanaObject->penduduk->pekerjaan->id;
        $penduduk->pendidikan_id = $pradanaObject->penduduk->pendidikan->id;
        $penduduk->telepon = $pradanaObject->penduduk->telepon;
        if($request->foto != ''){
            $file = $request->file('foto');
            $filename = uniqid().'.png';
            $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
            $path = $fileLocation."/".$filename;
            $penduduk->foto = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $penduduk->koordinat_alamat = $penduduk_purusa->koordinat_alamat;
        $penduduk->alamat = $penduduk_purusa->alamat;
        $penduduk->desa_id = $penduduk_purusa->desa_id;
        $penduduk->save();

        //NOMOR CACAH KRAMA
        $banjar_adat_id =  Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $nomor_cacah_krama_mipil = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'01'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.$jumlah_penduduk_tanggal_sama;
        }
        $penduduk->update();

        //INSERT CACAH KRAMA MIPIL
        $cacah_krama_mipil = new CacahKramaMipil();
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_mipil->tempekan_id = $purusa->tempekan_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
        $cacah_krama_mipil->jenis_kependudukan = $purusa->jenis_kependudukan;
        $cacah_krama_mipil->status = '0';
        if($purusa->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $purusa->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();
        $pradana = $cacah_krama_mipil;

            //INSERT DRAFT PERKAWINAN
            $perkawinan = new Perkawinan();
            $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
            $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
            $perkawinan->jenis_perkawinan = 'campuran_masuk';
            $perkawinan->purusa_id = $request->purusa;
            $perkawinan->pradana_id = $cacah_krama_mipil->id;
            $perkawinan->banjar_adat_purusa_id = $banjar_adat_id;
            $perkawinan->desa_adat_purusa_id = $desa_adat_purusa->id;
            $perkawinan->banjar_adat_pradana_id = $banjar_adat_id;
            $perkawinan->desa_adat_pradana_id = $desa_adat_purusa->id;
            $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
            $perkawinan->keterangan = $request->keterangan;
            $perkawinan->status_kekeluargaan = $request->status_kekeluargaan;
            if($request->status_kekeluargaan == 'baru'){
                if($request->calon_kepala_keluarga == 'pradana'){
                    $perkawinan->calon_krama_id = $cacah_krama_mipil->id;
                }else{
                    $perkawinan->calon_krama_id = $request->calon_kepala_keluarga;
                }
            }
            $perkawinan->nama_pemuput = $request->nama_pemuput;
            $perkawinan->status_perkawinan = '0';
            if($request->file('file_bukti_serah_terima_perkawinan')!=""){
                $file = $request->file('file_bukti_serah_terima_perkawinan');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $perkawinan->file_bukti_serah_terima_perkawinan = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_akta_perkawinan')!=""){
                $file = $request->file('file_akta_perkawinan');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $perkawinan->file_akta_perkawinan = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }

            //DATA + PRADANA
            $perkawinan->nik_ayah_pradana = $perkawinanObject->nik_ayah_pradana;
            $perkawinan->nama_ayah_pradana = $perkawinanObject->nama_ayah_pradana;
            $perkawinan->nik_ibu_pradana = $perkawinanObject->nik_ibu_pradana;
            $perkawinan->nama_ibu_pradana = $perkawinanObject->nama_ibu_pradana;
            $perkawinan->nik_ayah_pradana = $perkawinanObject->nik_ayah_pradana;
            $perkawinan->agama_asal_pradana = $perkawinanObject->agama_asal_pradana;
            $perkawinan->alamat_asal_pradana = $perkawinanObject->alamat_asal_pradana;
            $perkawinan->desa_asal_pradana_id = $perkawinanObject->desa_asal_pradana_id;
            if($request->file('file_sudhi_wadhani')!=""){
                $file = $request->file('file_sudhi_wadhani');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_sudhi_wadhani';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $perkawinan->file_sudhi_wadhani = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $perkawinan->save();


            if ($perkawinan) {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => Perkawinan::where('id', $perkawinan->id)->with('purusa.penduduk', 'pradana.penduduk')->first(),
                    'message' => 'data perkawinan sukses'
                ], 200);
            }

            else {
                return response()->json([
                    'statusCode' => 500,
                    'status' => false,
                    'data' => $perkawinan,
                    'message' => 'data perkawinan fail'
                ], 500);
            }
    }

    public function sah_campuran_masuk(Request $req)
    {
        $perkawinan = Perkawinan::with('purusa', 'pradana')->where('id', $req->id_perkawinan)->first();
        $perkawinan->status_perkawinan = '3';
        $perkawinan->save();

        $cacah_krama_mipil = CacahKramaMipil::where('id', $perkawinan->pradana_id)->with('penduduk')->first();
         //AKTIFKAN CACAH
         $cacah_krama_mipil->status = '1';
         $cacah_krama_mipil->update();
         $pradana = $cacah_krama_mipil;


         //Kekeluargaan
         if($perkawinan->status_kekeluargaan == 'tetap'){
             //GET KK LAMA PURUSA
             $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
             if(!$krama_mipil_purusa_lama){
                 $is_kk = 0;
                 $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
                 $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                 $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
             }else{
                 $is_kk = 1;
                 $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
             }

             //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PRADANA KE PURUSA)
             //4. COPY DATA KK PURUSA
             $krama_mipil_purusa_baru = new KramaMipil();
             $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
             $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
             $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
             $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
             $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
             $krama_mipil_purusa_baru->status = '1';
             $krama_mipil_purusa_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru (Perkawinan)';
             $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
             $krama_mipil_purusa_baru->save();

             //5. COPY ANGGOTA LAMA PURUSA
             foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                 $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                 $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                 $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                 $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                 $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                 $anggota_krama_mipil_purusa_baru->status = '1';
                 $anggota_krama_mipil_purusa_baru->save();

                 //NONAKTIFKAN ANGGOTA LAMA
                 $anggota_lama_purusa->status = '0';
                 $anggota_lama_purusa->update();
             }

             //6. MASUKKAN PRADANA KE KK PURUSA
             $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
             $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
             $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $cacah_krama_mipil->id;
             if($is_kk){
                 if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                     $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                 }else{
                     $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                 }
             }else{
                 $anggota_krama_mipil_purusa_baru->status_hubungan = 'menantu';
             }
             $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
             $anggota_krama_mipil_purusa_baru->status = '1';
             $anggota_krama_mipil_purusa_baru->save();

             //7. LEBUR KK PURUSA LAMA
             $krama_mipil_purusa_lama->status = '0';
             $krama_mipil_purusa_lama->update();

         }else if($perkawinan->status_kekeluargaan == 'baru'){
             //GET CALON KK
             if($perkawinan->calon_krama_id == $perkawinan->purusa_id){
                 $calon_kk = 'purusa';
             }else if($perkawinan->calon_krama_id == 'pradana'){
                 $calon_kk = 'pradana';
             }

             //IF CALON KK IS PURUSA/PRADANA
             if($calon_kk == 'purusa'){
                 //GET KK LAMA PURUSA
                 $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
                 if(!$krama_mipil_purusa_lama){
                     $is_kk = 0;
                     $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
                     $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                     $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                 }else{
                     $is_kk = 1;
                     $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                 }

                 //IF PURUSA KK/ANGGOTA
                 if($is_kk){
                     //COPY DATA KK PURUSA
                     $krama_mipil_purusa_baru = new KramaMipil();
                     $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                     $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                     $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                     $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                     $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                     $krama_mipil_purusa_baru->status = '1';
                     $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                     $krama_mipil_purusa_baru->alasan_perubahan = 'Penambahan Anggota Keluarga Baru (Perkawinan)';
                     $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                     $krama_mipil_purusa_baru->save();

                     //COPY DATA ANGGOTA KK PURUSA
                     foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                         $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                             $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                             $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                             $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                             $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                             $anggota_krama_mipil_purusa_baru->status = '1';
                             $anggota_krama_mipil_purusa_baru->save();
                         //NONAKTIFKAN ANGGOTA LAMA
                         $anggota_lama_purusa->status = '0';
                         $anggota_lama_purusa->update();
                     }

                     //LEBUR KK PURUSA LAMA
                     $krama_mipil_purusa_lama->status = '0';
                     $krama_mipil_purusa_lama->update();

                     //MASUKKAN PRADANA KE KK PURUSA
                     $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                     $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                     $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $pradana->id;
                     if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                         $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                     }else{
                         $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                     }
                     $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                     $anggota_krama_mipil_purusa_baru->status = '1';
                     $anggota_krama_mipil_purusa_baru->save();
                 }else{
                     //COPY DATA KK LAMA PURUSA
                     $krama_mipil_purusa_baru = new KramaMipil();
                     $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                     $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                     $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                     $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                     $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                     $krama_mipil_purusa_baru->status = '1';
                     $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                     $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                     $krama_mipil_purusa_baru->save();

                     //COPY DATA ANGGOTA LAMA PURUSA
                     foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                         if($anggota_lama_purusa->cacah_krama_mipil_id != $perkawinan->purusa_id){
                             $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                             $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                             $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                             $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                             $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                             $anggota_krama_mipil_purusa_baru->status = '1';
                             $anggota_krama_mipil_purusa_baru->save();
                         }else{
                             $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                             $anggota_lama_purusa->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                         }
                         //NONAKTIFKAN ANGGOTA LAMA
                         $anggota_lama_purusa->status = '0';
                         $anggota_lama_purusa->update();
                     }

                     //LEBUR KK PURUSA LAMA
                     $krama_mipil_purusa_lama->status = '0';
                     $krama_mipil_purusa_lama->update();

                     //GENERATE NOMOR KK BARU
                     $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
                     $banjar_adat = BanjarAdat::find($banjar_adat_id);
                     $tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                     $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
                     $curr_year = Carbon::parse($tanggal_registrasi)->year;
                     $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
                     $curr_year = Carbon::now()->format('y');
                     $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
                     $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
                     if($jumlah_krama_bulan_regis_sama < 10){
                         $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
                     }else if($jumlah_krama_bulan_regis_sama < 100){
                         $nomor_krama_mipil = $nomor_krama_mipil.'0'.$jumlah_krama_bulan_regis_sama;
                     }else if($jumlah_krama_bulan_regis_sama < 1000){
                         $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
                     }

                     //PEMBENTUKAN KK PURUSA BARU
                     $krama_mipil_purusa_baru = new KramaMipil();
                     $krama_mipil_purusa_baru->nomor_krama_mipil = $nomor_krama_mipil;
                     $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                     $krama_mipil_purusa_baru->cacah_krama_mipil_id = $perkawinan->purusa_id;
                     $krama_mipil_purusa_baru->status = '1';
                     $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                     $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                     $krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                     $krama_mipil_purusa_baru->save();

                     //9. MASUKKAN PRADANA KE KK PURUSA
                     $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                     $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                     $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $pradana->id;
                     if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                         $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                     }else{
                         $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                     }                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                     $anggota_krama_mipil_purusa_baru->status = '1';
                     $anggota_krama_mipil_purusa_baru->save();
                 }
             }else if($calon_kk == 'pradana'){
                 //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PURUSA KE PRADANA)
                 //GET KK LAMA PURUSA
                 $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->purusa_id)->where('status', '1')->first();
                 if($purusa_sebagai_anggota){
                     $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                     $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();

                     //1. COPY DATA KK purusa
                     $krama_mipil_purusa_baru = new KramaMipil();
                     $krama_mipil_purusa_baru->nomor_krama_mipil = $krama_mipil_purusa_lama->nomor_krama_mipil;
                     $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                     $krama_mipil_purusa_baru->cacah_krama_mipil_id = $krama_mipil_purusa_lama->cacah_krama_mipil_id;
                     $krama_mipil_purusa_baru->kedudukan_krama_mipil = $krama_mipil_purusa_lama->kedudukan_krama_mipil;
                     $krama_mipil_purusa_baru->jenis_krama_mipil = $krama_mipil_purusa_lama->jenis_krama_mipil;
                     $krama_mipil_purusa_baru->status = '1';
                     $krama_mipil_purusa_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perkawinan)';
                     $krama_mipil_purusa_baru->tanggal_registrasi = $krama_mipil_purusa_lama->tanggal_registrasi;
                     $krama_mipil_purusa_baru->save();

                     //2. COPY ANGGOTA LAMA purusa
                     foreach($anggota_krama_mipil_purusa_lama as $anggota_lama_purusa){
                         if($anggota_lama_purusa->cacah_krama_mipil_id != $perkawinan->purusa_id){
                             $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                             $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                             $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                             $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                             $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                             $anggota_krama_mipil_purusa_baru->status = '1';
                             $anggota_krama_mipil_purusa_baru->save();
                         }else{
                             $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                             $anggota_lama_purusa->alasan_keluar = 'Perkawinan (Satu Banjar Adat)';
                         }
                         //NONAKTIFKAN ANGGOTA LAMA
                         $anggota_lama_purusa->status = '0';
                         $anggota_lama_purusa->update();
                     }

                     //3. LEBUR KK purusa LAMA
                     $krama_mipil_purusa_lama->status = '0';
                     $krama_mipil_purusa_lama->update();
                 }

                 //GENERATE NOMOR KK BARU
                 $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
                 $banjar_adat = BanjarAdat::find($banjar_adat_id);
                 $tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                 $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
                 $curr_year = Carbon::parse($tanggal_registrasi)->year;
                 $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->withTrashed()->count();
                 $curr_year = Carbon::now()->format('y');
                 $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
                 $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
                 if($jumlah_krama_bulan_regis_sama < 10){
                     $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
                 }else if($jumlah_krama_bulan_regis_sama < 100){
                     $nomor_krama_mipil = $nomor_krama_mipil.'0'.$jumlah_krama_bulan_regis_sama;
                 }else if($jumlah_krama_bulan_regis_sama < 1000){
                     $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
                 }

                 //8. PEMBENTUKAN KK PRADANA BARU
                 $krama_mipil_pradana_baru = new KramaMipil();
                 $krama_mipil_pradana_baru->nomor_krama_mipil = $nomor_krama_mipil;
                 $krama_mipil_pradana_baru->banjar_adat_id = $purusa->banjar_adat_id;
                 $krama_mipil_pradana_baru->cacah_krama_mipil_id = $pradana->id;
                 $krama_mipil_pradana_baru->status = '1';
                 $krama_mipil_pradana_baru->kedudukan_krama_mipil = 'pradana';
                 $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                 $krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                 $krama_mipil_pradana_baru->save();

                 //9. MASUKKAN PURUSA KE KK PRADANA
                 $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                 $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                 $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $perkawinan->purusa_id;
                 if($purusa->penduduk->jenis_kelamin == 'perempuan'){
                     $anggota_krama_mipil_pradana_baru->status_hubungan = 'istri';
                 }else{
                     $anggota_krama_mipil_pradana_baru->status_hubungan = 'suami';
                 }
                 $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                 $anggota_krama_mipil_pradana_baru->status = '1';
                 $anggota_krama_mipil_pradana_baru->save();
             }
         }
         //Alamat dan Status Kawin
         $purusa = CacahKramaMipil::find($perkawinan->purusa_id);
         $pradana = $cacah_krama_mipil;
         $penduduk_purusa = Penduduk::find($purusa->penduduk_id);
         $penduduk_pradana = Penduduk::find($pradana->penduduk_id);

         //Status Kawin
         $penduduk_purusa->status_perkawinan = 'kawin';
         $penduduk_pradana->status_perkawinan = 'kawin';

         //Alamat
         $penduduk_pradana->alamat = $penduduk_purusa->alamat;
         $penduduk_pradana->koordinat_alamat = $penduduk_purusa->koordinat_alamat;
         $penduduk_pradana->desa_id = $penduduk_purusa->desa_id;

         //Update
         $penduduk_purusa->update();
         $penduduk_pradana->update();

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan,
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => $perkawinan,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function store_campuran_keluar(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
        //     'pradana' => 'required',
        //     'file_bukti_serah_terima_perkawinan' => 'required',
        //     'tanggal_perkawinan' => 'required',
        //     'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
        //     'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',

        //     'nik_pasangan' => 'required|unique:tb_penduduk,nik|regex:/^[0-9]*$/',
        //     'nama_pasangan' => 'required|regex:/^[a-zA-Z\s]*$/',
        //     'alamat_pasangan' => 'required',
        // ],[
        //     'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
        //     'nomor_bukti_serah_terima_perkawinan.unique' => "Nomor Bukti Serah Terima Perkawinan telah terdaftar",
        //     'pradana.required' => "Purusa wajib dipilih",
        //     'file_bukti_serah_terima_perkawinan.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
        //     'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
        //     'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
        //     'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
        //     'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
        //     'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",

        //     'nik_pasangan.regex' => "NIK hanya boleh mengandung angka",
        //     'nik_pasangan.unique' => "NIK yang dimasukkan telah terdaftar",
        //     'nama_pasangan.required' => "Nama wajib diisi",
        //     'nama_pasangan.regex' => "Nama hanya boleh mengandung huruf",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        //GET CACAH/MEMPELAI PRADANA

        $perkawinanObject = json_decode($request->perkawinan_json);

        $pradana = CacahKramaMipil::find($perkawinanObject->pradana_id);
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $banjar_adat_pradana = BanjarAdat::find($banjar_adat_id);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$perkawinanObject->nomor_perkawinan);

        $perkawinan = new Perkawinan();
        $perkawinan->nomor_perkawinan = $perkawinanObject->nomor_perkawinan;
        $perkawinan->nomor_akta_perkawinan = $perkawinanObject->nomor_akta_perkawinan;
        $perkawinan->jenis_perkawinan = 'campuran_keluar';
        $perkawinan->pradana_id = $perkawinanObject->pradana_id;
        $perkawinan->banjar_adat_pradana_id = $banjar_adat_pradana->id;
        $perkawinan->desa_adat_pradana_id = $desa_adat_pradana->id;
        $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($perkawinanObject->tanggal_perkawinan));
        $perkawinan->keterangan = $perkawinanObject->keterangan;

        //DATA PASANGAN
        $perkawinan->nik_pasangan = $perkawinanObject->nik_pasangan;
        $perkawinan->nama_pasangan = $perkawinanObject->nama_pasangan;
        $perkawinan->alamat_asal_pasangan = $perkawinanObject->alamat_asal_pasangan;
        $perkawinan->agama_pasangan = $perkawinanObject->agama_pasangan;
        $perkawinan->desa_asal_pasangan_id = $perkawinanObject->desa_asal_pasangan_id;

        if($request->file('file_bukti_serah_terima_perkawinan')!=""){
            $file = $request->file('file_bukti_serah_terima_perkawinan');
            $fileLocation = '/file/'.$desa_adat_pradana->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($perkawinan->file_bukti_serah_terima_perkawinan != NULL){
                $old_path = str_replace("/storage","",$perkawinan->file_bukti_serah_terima_perkawinan);
                Storage::disk('public')->delete($old_path);
            }
            $perkawinan->file_bukti_serah_terima_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_perkawinan')!=""){
            $file = $request->file('file_akta_perkawinan');
            $fileLocation = '/file/'.$desa_adat_pradana->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($perkawinan->file_akta_perkawinan != NULL){
                $old_path = str_replace("/storage","",$perkawinan->file_akta_perkawinan);
                Storage::disk('public')->delete($old_path);
            }
            $perkawinan->file_akta_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        $perkawinan->status_perkawinan = '0';
        $perkawinan->save();

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perkawinan::where('id', $perkawinan->id)->with('pradana.penduduk')->first(),
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }

    public function sah_campuran_keluar(Request $request)
    {
        $perkawinan = Perkawinan::with('purusa', 'pradana')->where('id', $request->id_perkawinan)->first();
        $perkawinan->status_perkawinan = '3';
        $perkawinan->save();
        $pradana = CacahKramaMipil::find($perkawinan->pradana_id);
        //NON AKTIFKAN CACAH
        $pradana->status = '0';
        $pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
        $pradana->alasan_keluar = 'Perkawinan (Campuran Keluar)';
        $pradana->update();

        //KELUARKAN CACAH DARI KELUARGA IF EXIST
        //GET KK LAMA PRADANA
        $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $perkawinan->pradana_id)->where('status', '1')->first();
        if($pradana_sebagai_anggota){
            $krama_mipil_pradana_lama = KramaMipil::find($pradana_sebagai_anggota->krama_mipil_id);
            $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();

            //1. COPY DATA KK PRADANA
            $krama_mipil_pradana_baru = new KramaMipil();
            $krama_mipil_pradana_baru->nomor_krama_mipil = $krama_mipil_pradana_lama->nomor_krama_mipil;
            $krama_mipil_pradana_baru->banjar_adat_id = $krama_mipil_pradana_lama->banjar_adat_id;
            $krama_mipil_pradana_baru->cacah_krama_mipil_id = $krama_mipil_pradana_lama->cacah_krama_mipil_id;
            $krama_mipil_pradana_baru->kedudukan_krama_mipil = $krama_mipil_pradana_lama->kedudukan_krama_mipil;
            $krama_mipil_pradana_baru->jenis_krama_mipil = $krama_mipil_pradana_lama->jenis_krama_mipil;
            $krama_mipil_pradana_baru->status = '1';
            $krama_mipil_pradana_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perkawinan)';
            $krama_mipil_pradana_baru->tanggal_registrasi = $krama_mipil_pradana_lama->tanggal_registrasi;
            $krama_mipil_pradana_baru->save();

            //2. COPY ANGGOTA LAMA PRADANA
            foreach($anggota_krama_mipil_pradana_lama as $anggota_lama_pradana){
                if($anggota_lama_pradana->cacah_krama_mipil_id != $perkawinan->pradana_id){
                    $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                    $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                    $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                    $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                    $anggota_krama_mipil_pradana_baru->status = '1';
                    $anggota_krama_mipil_pradana_baru->save();
                }else{
                    $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
                    $anggota_lama_pradana->alasan_keluar = 'Perkawinan (Campuran Keluar)';
                }
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama_pradana->status = '0';
                $anggota_lama_pradana->update();
            }

            //3. LEBUR KK PRADANA LAMA
            $krama_mipil_pradana_lama->status = '0';
            $krama_mipil_pradana_lama->update();
        }
        $perkawinan->status_perkawinan = '3';
        $perkawinan->save();

        if ($perkawinan) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perkawinan,
                'message' => 'data perkawinan sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => $perkawinan,
                'message' => 'data perkawinan fail'
            ], 500);
        }
    }
}
