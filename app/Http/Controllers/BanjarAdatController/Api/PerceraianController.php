<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\CacahTamiu;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\KramaMipil;
use App\Models\Notifikasi;
use App\Models\Penduduk;
use App\Models\Perceraian;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Auth;
use App\Models\User;
use App\Helper\Helper;


class PerceraianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get(Request $req) {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;

        $perceraian = Perceraian::query();

        if ($req->query('status') == '3') {
            $perceraian->where('status_perceraian', '3');
        } else if ($req->query('status') == '2') {
            $perceraian->where('status_perceraian', '2');
        } else if ($req->query('status') == '1') {
            $perceraian->where('status_perceraian', '1');
        } else if ($req->query('status') == '0') {
            $perceraian->where('status_perceraian', '0');
        }


        if ( $req->query('start_date') ) {
            // $rentang_waktu = explode(' - ', $request->rentang_waktu);
            // $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            // $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));

            $perceraian->whereBetween('tanggal_perceraian', [$req->query('start_date'), $req->query('end_date')]);
        }

        $perceraian->where(function ($query) use ($banjar_adat_id) {
                                $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                                    ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
                            })
                            ->with('purusa.penduduk', 'pradana.penduduk')
                            ->orderBy('tanggal_perceraian', 'desc');

        if ($perceraian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perceraian->paginate(10),
                'message' => 'data perceraian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perceraian fail'
            ], 500);
        }
    }

    public function detail(Request $req) {
        $perceraian = Perceraian::with('purusa.penduduk', 'pradana.penduduk')->find($req->query('perceraian'));
        $daftar_status_anggota = (array)json_decode($perceraian->status_anggota);
        $arr_id_anggota = array_keys($daftar_status_anggota);
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->whereIn('id', $arr_id_anggota)
        ->get()->map(function($item) use ($daftar_status_anggota){
            //SET NAMA
            $nama = '';
            if($item->cacah_krama_mipil->penduduk->gelar_depan != NULL){
                $nama = $nama.$item->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$item->cacah_krama_mipil->penduduk->nama;
            if($item->cacah_krama_mipil->penduduk->gelar_belakang != NULL){
                $nama = $nama.', '.$item->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $item->cacah_krama_mipil->penduduk->nama = $nama;

            //SET STATUS HUBUNGAN
            $item->status_hubungan = ucwords(str_replace('_', ' ', $item->status_hubungan));

            //SET STATUS IKUT PURUSA/PRADANA
            foreach($daftar_status_anggota as $key=>$value){
                if($item->id == $key){
                    $item->status_baru = ucwords(str_replace('_', ' ', $value));
                }
            }
            return $item;
        });

        $sisiPradana = false;
        if (Auth::user()->prajuru_banjar_adat->banjar_adat_id == $perceraian->banjar_adat_pradana_id) {
            $sisiPradana = true;
        }

        if ($perceraian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $perceraian,
                'anggota_krama_mipil' => $anggota_krama_mipil,
                'sisi_pradana' => $sisiPradana,
                'message' => 'data perceraian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perceraian fail'
            ], 500);
        }
    }

    public function getKramaMipil() {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')
                                    ->where('banjar_adat_id', $banjar_adat_id)
                                    ->where('status', '1')
                                    ->where('jenis_krama_mipil', 'krama_penuh')
                                    ->where('kedudukan_krama_mipil', '!=', NULL)
                                    ->orderBy('tanggal_registrasi', 'DESC')->get()
        ->filter(function ($item){
            $pasangan = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->where(function ($query) {
                $query->where('status_hubungan', 'istri')
                    ->orWhere('status_hubungan', 'suami');
            })->count();
            if($pasangan>0){
                return $item;
            }
        })->pluck('id');

        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                    ->whereIn('id', $kramas)
                                    ->where('status', '1')
                                    ->orderBy('tanggal_registrasi', 'DESC')
                                    ->paginate(100);

        if ($kramas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
                'message' => 'data krama sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama fail'
            ], 500);
        }
    }

    public function getKramaMipilSelectedWithAnggota(Request $req) {
        // pake ini untuk ngambil krama mipil + anggota keluarga + pasangannya setelah krama yang mau cerai di pilih
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($req->query('krama'));
        $krama_mipil->kedudukan_krama_mipil = ucwords($krama_mipil->kedudukan_krama_mipil);

        $pasangan = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where(function ($query) {
            $query->where('status_hubungan', 'istri')
                ->orWhere('status_hubungan', 'suami');
        })->get()->map(function($item){
            $item->status_hubungan = ucwords(str_replace('_', ' ', $item->status_hubungan));
            return $item;
        });

        $anggota_krama_mipil =  AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
                                    ->where('krama_mipil_id', $krama_mipil->id)
                                    ->where('status_hubungan', '!=', 'istri')
                                    ->where('status_hubungan', '!=', 'suami')
        ->get()->map(function($item){
            $item->status_hubungan = ucwords(str_replace('_', ' ', $item->status_hubungan));
            return $item;
        });
        if ($krama_mipil) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $krama_mipil,
                'pasangan' => $pasangan,
                'anggota_krama_mipil' => $anggota_krama_mipil,
                'message' => 'data krama sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama fail'
            ], 500);
        }
    }

    public function getKramaMipilBaruForKrama(Request $request) {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        if (isset($request->banjar_adat_krama_mipil)) {
            // kirim banjar adat id klo Krama mipil yang jadi pradana dan milih untuk keluar banjar
            // $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')
            //                     ->where('banjar_adat_id', $request->banjar_adat_krama_mipil)
            //                     ->where('status', '1')->where('jenis_krama_mipil', 'krama_penuh')
            //                     ->where('kedudukan_krama_mipil', '!=', NULL)

            //                     ->orderBy('tanggal_registrasi', 'DESC')->get()
            // ->map(function ($item){
            //     $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
            //     return $item;
            // });

            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                        ->where('banjar_adat_id', $request->banjar_adat_krama_mipil)
                                        ->where('id', '!=', $request->krama_mipil_pasangan)
                                        ->where('status', '1')
                                        ->where('kedudukan_krama_mipil', '!=', NULL)
                                        ->orderBy('tanggal_registrasi', 'DESC')
                                        ->paginate(100);
        }else{
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                        ->where('banjar_adat_id', $banjar_adat_id)
                                        ->where('id', '!=', $request->krama_mipil_saat_ini)
                                        ->where('status', '1')
                                        ->where('kedudukan_krama_mipil', '!=', NULL)
                                        ->orderBy('tanggal_registrasi', 'DESC')
                                        ->paginate(100);

            // $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')
            //                         ->where('banjar_adat_id', $banjar_adat_id)
            //                         ->where('status', '1')->where('jenis_krama_mipil', 'krama_penuh')
            //                         ->where('kedudukan_krama_mipil', '!=', NULL)
            //                         ->where('id', '!=', $request->krama_mipil_pasangan)->orderBy('tanggal_registrasi', 'DESC')->get()
            // ->filter(function ($item) use ($request){
            //     // kirim krama yang di pilih untuk cerai semisal tetap di banjar adat
            //     if (isset($request->krama_mipil_saat_ini)) {
            //         if($item->id != $request->krama_mipil_saat_ini){
            //             return $item;
            //         }
            //     }
            // })
            // ->map(function ($item){
            //     $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
            //     return $item;
            // });
        }

        if ($kramas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
                'message' => 'data krama sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama fail'
            ], 500);
        }
    }

    public function getKramaMipilBaruForPasangan(Request $request) {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        if (isset($request->banjar_adat_pasangan)) {
            // kirim banjar adat id klo pasangannya yang jadi pradana dan milih keluar banjar

            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                        ->where('banjar_adat_id', $request->banjar_adat_pasangan)
                                        ->where('id', '!=', $request->krama_mipil_saat_ini)
                                        ->where('status', '1')
                                        ->where('id', '!=', $request->krama_mipil_krama_mipil)
                                        ->where('kedudukan_krama_mipil', '!=', NULL)
                                        ->orderBy('tanggal_registrasi', 'DESC')
                                        ->paginate(100);
            // $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')
            // ->where('banjar_adat_id', $request->banjar_adat_pasangan)->where('status', '1')
            // ->where('jenis_krama_mipil', 'krama_penuh')->where('kedudukan_krama_mipil', '!=', NULL)
            // ->where('id', '!=', $request->krama_mipil_krama_mipil)->orderBy('tanggal_registrasi', 'DESC')->get()
            // ->map(function ($item){
            //     $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
            //     return $item;
            // });
        }else{
            error_log($request->krama_mipil_krama_mipil);

            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                ->where('banjar_adat_id', $banjar_adat_id)
                                ->where('id', '!=', $request->krama_mipil_saat_ini)
                                ->where('status', '1')
                                ->where('id', '!=', $request->krama_mipil_krama_mipil)
                                ->where('kedudukan_krama_mipil', '!=', NULL)
                                ->orderBy('tanggal_registrasi', 'DESC')
                                ->paginate(100);

            // $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')
            //         ->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')
            //         ->where('jenis_krama_mipil', 'krama_penuh')->where('kedudukan_krama_mipil', '!=', NULL)
            //         ->where('id', '!=', $request->krama_mipil_krama_mipil)
            //         ->orderBy('tanggal_registrasi', 'DESC')->get()
            // ->filter(function ($item) use ($request){
            //     if (isset($request->krama_mipil_saat_ini)) {
            //         // kirim krama yang di pilih untuk cerai semisal tetap di banjar adat
            //         //krama_mipil_k
            //         if($item->id != $request->krama_mipil_saat_ini){
            //             return $item;
            //         }
            //     }
            // })
            // ->map(function ($item){
            //     $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
            //     return $item;
            // });
        }
        if ($kramas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
                'message' => 'data krama sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama fail'
            ], 500);
        }
    }

    public function store(Request $request){
        //Validator
        // $validator = Validator::make($request->all(), [
        //     'tanggal_perceraian' => 'required',
        //     'nama_pemuput' => 'required|regex:/^[a-zA-Z\s]*$/|max:100',
        //     'nomor_bukti_perceraian' => 'required|unique:tb_perceraian,nomor_perceraian|nullable|max:50',
        //     'file_bukti_perceraian' => 'required',
        //     'nomor_akta_perceraian' => 'unique:tb_perceraian|nullable|max:21',
        //     'file_akta_perceraian' => 'required_with:nomor_akta_perceraian',

        //     'krama_mipil' => 'required',
        //     'status_krama_mipil' => 'required',
        //     'pasangan' => 'required',
        //     'status_pasangan' => 'required'
        // ],[
        //     'tanggal_perceraian.regex' => "Tanggal Perceraian wajib diisi",
        //     'nama_pemuput.required' => "Nama Pemuput wajib diisi",
        //     'nama_pemuput.regex' => "Nama Pemuput hanya boleh mengandung huruf",
        //     'nomor_bukti_perceraian.required' => "Nomor Bukti Perceraian wajib diisi",
        //     'nomor_bukti_perceraian.unique' => "Nomor Bukti Perceraian telah terdaftar",
        //     'nomor_bukti_perceraian.max' => "Nomor Bukti Perceraian maksimal terdiri dari 50 karakter",
        //     'file_bukti_perceraian.required' => "File Bukti Perceraian wajib diisi",
        //     'nomor_akta_perceraian.unique' => "Nomor Akta Perceraian telah terdaftar",
        //     'nomor_akta_perceraian.max' => "Nomor Akta Perceraian maksimal terdiri dari 21 karakter",
        //     'file_akta_perceraian.required_with' => "File Akta Perceraian wajib diisi",
        //     'krama_mipil.required' => "Krama Mipil wajib dipilih",
        //     'status_krama_mipil.required' => "Status Krama Mipil wajib dipilih",
        //     'pasangan.required' => "Pasangan wajib dipilih",
        //     'status_pasangan.required' => "Status Pasangan wajib dipilih",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        $perceraianObject = json_decode($request->perceraian_json);
        $anggotaObject = json_decode($request->anggota_keluarga_json, true);


        //Validasi Tanggal
        $tanggal_cerai = date("Y-m-d", strtotime($perceraianObject->tanggal_perceraian));
        $tanggal_sekarang = Carbon::now()->toDateString();
        // if($tanggal_cerai > $tanggal_sekarang){
        //     return back()->withInput()->withErrors(['tanggal_perceraian' => 'Tanggal perceraian tidak boleh melebihi tanggal sekarang']);
        // }

        //Get Master Data
        $krama_mipil = KramaMipil::find($perceraianObject->krama_mipil_id);
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        //Convert Nomor Perceraian
        $convert_nomor_perceraian = str_replace("/","-",$perceraianObject->nomor_perceraian);

        //Generate Array Anggota
        $arrAnggota = array();
        $stringAnggotaJson;
        if ($anggotaObject == null) {
            $arr_status_anggota = NULL;
        } else {
            // set counter iterasinya
            $i = 0;
            // hitung gede array yang di itterate
            $len = count($anggotaObject);
            foreach ($anggotaObject as $anggota) {
                if ($i == 0) {
                    // $stringJson = '{\".$anggota['id'].":".$anggota['status_baru'].",';
                    // {"145":"ikut_purusa","146":"ikut_purusa","147":"ikut_pradana"};

                    /**
                     * first iteration, bentuk pembuka string jsonnya
                     */
                    $format = '{"%s":"%s"';
                    $stringJson = sprintf($format, $anggota['id'], $anggota['status_baru']);
                    $stringAnggotaJson = $stringJson;
                    if ($i == $len - 1) {
                        /**
                         * klo cmn ad 1 anggota, tutup jsonnya
                         */
                        $stringAnggotaJson = $stringAnggotaJson."}";
                    }
                } else if ($i == $len - 1) {
                    /**
                     * klo cmn iterasi terakhir, append item terakhir, terus tutup json
                     */

                    // $stringJson = "".$anggota['id'].":".$anggota['status_baru']."}";
                    $format = ',"%s":"%s"}';
                    $stringJson = sprintf($format, $anggota['id'], $anggota['status_baru']);
                    $stringAnggotaJson = $stringAnggotaJson.$stringJson;
                } else {
                    // $stringJson = ",".$anggota['id'].":".$anggota['status_baru'].",";

                    /**
                     * klo blm iterasi terakhir, append item nya ke stirng json yg udh di bentuk
                     */
                    $format = ',"%s":"%s"';
                    $stringJson = sprintf($format, $anggota['id'], $anggota['status_baru']);
                    $stringAnggotaJson = $stringAnggotaJson.$stringJson;
                }
                // +1 iterasinya
                $i++;

                // $arrTemp = [ $anggota['id'] => $anggota['status_baru'] ];
                // array_push($arrAnggota, $arrTemp);
            }
            // $arr_status_anggota = json_encode((object)$arrAnggota, JSON_FORCE_OBJECT);
            error_log($stringAnggotaJson);
            $arr_status_anggota = $stringAnggotaJson;
        }

        //Mencari tau siapa purusa (Krama atau Pasangannya)
        if($krama_mipil->kedudukan_krama_mipil == 'purusa'){
            $is_purusa = 'krama_mipil';
            $purusa = CacahKramaMipil::find($perceraianObject->purusa_id);
            $pradana = CacahKramaMipil::find($perceraianObject->pradana_id);
        }else{
            $is_purusa = 'pasangan';
            $purusa = CacahKramaMipil::find($perceraianObject->purusa_id);
            $pradana = CacahKramaMipil::find($perceraianObject->pradana_id);
        }

        //Insert Data Perceraian
        $perceraian = new Perceraian();
        $perceraian->nomor_perceraian = $perceraianObject->nomor_perceraian;
        $perceraian->nomor_akta_perceraian = $perceraianObject->nomor_akta_perceraian;
        $perceraian->krama_mipil_id = $perceraianObject->krama_mipil_id;
        $perceraian->tanggal_perceraian = date("Y-m-d", strtotime($perceraianObject->tanggal_perceraian));
        $perceraian->nama_pemuput = $perceraianObject->nama_pemuput;
        $perceraian->keterangan = $perceraianObject->keterangan;
        $perceraian->status_perceraian = '0';
        if($request->file('file_bukti_perceraian')!=""){
            $file = $request->file('file_bukti_perceraian');
            $fileLocation = '/file/'.$desa_adat->id.'/perceraian/'.$convert_nomor_perceraian.'/file_bukti_perceraian';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perceraian->file_bukti_perceraian = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_perceraian')!=""){
            $file = $request->file('file_akta_perceraian');
            $fileLocation = '/file/'.$desa_adat->id.'/perceraian/'.$convert_nomor_perceraian.'/file_akta_perceraian';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $perceraian->file_akta_perceraian = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $perceraian->purusa_id = $purusa->id;
        $perceraian->pradana_id = $pradana->id;
        $perceraian->status_anggota = $arr_status_anggota;
        $perceraian->banjar_adat_purusa_id = $banjar_adat->id;
        $perceraian->desa_adat_purusa_id = $desa_adat->id;

        $perceraian->status_purusa = $perceraianObject->status_purusa;
        $perceraian->status_pradana = $perceraianObject->status_pradana;

        //Status Purusa
        if($perceraianObject->status_purusa == 'tetap_di_banjar_dan_kk_baru'){
            $perceraian->krama_mipil_baru_purusa_id = $perceraianObject->krama_mipil_baru_purusa_id;
            $perceraian->status_hubungan_baru_purusa = $perceraianObject->status_hubungan_baru_purusa;
        }

        //Status Pradana
        if($perceraianObject->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
            $perceraian->banjar_adat_pradana_id = $banjar_adat->id;
            $perceraian->desa_adat_pradana_id = $desa_adat->id;
            $perceraian->krama_mipil_baru_pradana_id = $perceraianObject->krama_mipil_baru_pradana_id;
            $perceraian->status_hubungan_baru_pradana = $perceraianObject->status_hubungan_baru_pradana;
        }else if($perceraianObject->status_pradana == 'keluar_banjar'){
            //Get Asal Pasangan/Pradana
            $banjar_adat_pasangan = BanjarAdat::find($perceraianObject->banjar_adat_pradana_id);
            $desa_adat_pasangan = DesaAdat::find($banjar_adat_pasangan->desa_adat_id);
            $perceraian->banjar_adat_pradana_id = $banjar_adat_pasangan->id;
            $perceraian->desa_adat_pradana_id = $desa_adat_pasangan->id;
            $perceraian->krama_mipil_baru_pradana_id = $perceraianObject->krama_mipil_baru_pradana_id;
            $perceraian->status_hubungan_baru_pradana = $perceraianObject->status_hubungan_baru_pradana;
        }else{
            $perceraian->desa_baru_pradana_id = $perceraianObject->desa_baru_pradana_id;
        }

        $perceraian->save();

        if ($perceraian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perceraian::with('purusa.penduduk', 'pradana.penduduk')->where('id', $perceraian->id)->first(),
                'message' => 'data perceraian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perceraian fail'
            ], 500);
        }
    }

    public function sahPerceraian(Request $request) {
        $perceraian = Perceraian::with('purusa.penduduk', 'pradana.penduduk')->find($request->query('perceraian'));
        //Update Data Perceraian Terutama Status!
        if($perceraian->status_pradana == 'keluar_banjar'){
            $perceraian->status_perceraian = '1';
        }else{
            $perceraian->status_perceraian = '3';
            //klo tetep di banjar sama/keluar bali, sahkan langsung
        }
        $perceraian->update();

        if($perceraian->status_pradana == 'keluar_banjar'){
            // klo di sahkan, set di konfirmasi dan di bawa ke banjar adat pradana
            $perceraian->status_perceraian = '1';
            $perceraian->update();
            $notifikasi = new Notifikasi();
            $notifikasi->notif_create_perceraian_beda_banjar_adat($perceraian->id);
            $userNotif = new User();
            error_log('ngirim notif perceraian');

            $banjarAdatPurusa = BanjarAdat::find($perceraian->banjar_adat_purusa_id);
            $desaAdatPurusa = DesaAdat::find($banjarAdatPurusa->desa_adat_id);
            $tanggal_kawin = Helper::convert_date_to_locale_id($perceraian->tanggal_perceraian);

            $kontenNotif = "Terdapat ajuan perceraian dari Banjar Adat ".$banjarAdatPurusa->nama_banjar_adat." Desa Adat ".$desaAdatPurusa->desadat_nama." pada tanggal ".$tanggal_kawin.".";


            $userNotif->sendNotifPendataan(
                                    $kontenNotif,
                                    null,
                                    "Ajuan Perceraian baru.",
                                    $perceraian->banjar_adat_pradana_id,
                                    $perceraian->id,
                                    4,
                                );
        } else{
            $krama_mipil = KramaMipil::find($perceraian->krama_mipil_id);
            if($krama_mipil->kedudukan_krama_mipil == 'purusa'){
                $is_purusa = 'krama_mipil';
            }else{
                $is_purusa = 'pasangan';
            }
            if($is_purusa == 'krama_mipil'){
                $this->krama_mipil_purusa($perceraian);

            }else{
                $this->krama_mipil_pradana($perceraian);
            }
        }

        if ($perceraian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perceraian::with('purusa.penduduk', 'pradana.penduduk')->where('id', $perceraian->id)->first(),
                'message' => 'data perceraian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perceraian fail'
            ], 500);
        }
    }

    public function tolak_perceraian(Request $request){
        $perceraian = Perceraian::with('purusa.penduduk', 'pradana.penduduk')->find($request->query('perceraian'));
        $perceraian->alasan_penolakan = $request->alasan_penolakan;
        $perceraian->status_perceraian = '2';
        $perceraian->update();

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();
        $notifikasi->notif_tolak_perceraian_beda_banjar_adat($perceraian->id);

        $userNotif = new User();
        error_log('ngirim notif perkawinan');

        $banjarAdatPradana = BanjarAdat::find($perceraian->banjar_adat_pradana_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perceraian->tanggal_perceraian);

        $kontenNotif = "Perceraian dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_kawin." belum dapat dikonfirmasi.";

        $userNotif->sendNotifPendataan(
                                $kontenNotif,
                                null,
                                "Penolakan Perceraian.",
                                $perceraian->banjar_adat_purusa_id,
                                $perceraian->id,
                                4,
                            );


        if ($perceraian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perceraian::with('purusa.penduduk', 'pradana.penduduk')->where('id', $perceraian->id)->first(),
                'message' => 'data perceraian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perceraian fail'
            ], 500);
        }
    }

    public function konfirmasi_perceraian(Request $request){
        $perceraian = Perceraian::with('purusa.penduduk', 'pradana.penduduk')->find($request->query('perceraian'));
        //Get Data
        $krama_mipil = KramaMipil::find($perceraian->krama_mipil_id);

        //Mencari tau siapa purusa (Krama atau Pasangannya)
        if($krama_mipil->kedudukan_krama_mipil == 'purusa'){
            $is_purusa = 'krama_mipil';
        }else{
            $is_purusa = 'pasangan';
        }

        //Update Data Perceraian
        $perceraian->status_perceraian = '3';
        $perceraian->update();

        //Insert Notifikasi
        $notifikasi = new Notifikasi();
        $notifikasi->notif_konfirmasi_perceraian_beda_banjar_adat($perceraian->id);

        $userNotif = new User();
        error_log('ngirim notif perceraian');

        $banjarAdatPradana = BanjarAdat::find($perceraian->banjar_adat_pradana_id);
        $desaAdatPradana = DesaAdat::find($banjarAdatPradana->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($perceraian->tanggal_perceraian);
        $kontenNotif = "Perceraian dari Banjar Adat ".$banjarAdatPradana->nama_banjar_adat." Desa Adat ".$desaAdatPradana->desadat_nama." pada tanggal ".$tanggal_kawin." telah dikonfirmasi.";

        error_log('udah kebawah gan');
        $userNotif->sendNotifPendataan(
                                $kontenNotif,
                                null,
                                "Perceraian telah dikonfirmasi.",
                                $perceraian->banjar_adat_purusa_id,
                                $perceraian->id,
                                4,
                            );

        //Logic Perkawinan
        if($is_purusa == 'krama_mipil'){
            $this->krama_mipil_purusa($perceraian);
        }else{
            $this->krama_mipil_pradana($perceraian);
        }

        if ($perceraian) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Perceraian::with('purusa.penduduk', 'pradana.penduduk')->where('id', $perceraian->id)->first(),
                'message' => 'data perceraian sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data perceraian fail'
            ], 500);
        }
    }




        //Fungsi Helper Untuk Purusa Pradana
        private function krama_mipil_purusa($perceraian){
            //Get Master Data
            $krama_mipil = KramaMipil::find($perceraian->krama_mipil_id);
            $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
            $banjar_adat = BanjarAdat::find($banjar_adat_id);
            $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

            //Count Jumlah Pasangan (Penentu apakah akan balu atau tidak!)
            $jumlah_pasangan = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where(function ($query) {
                $query->where('status_hubungan', 'istri')
                    ->orWhere('status_hubungan', 'suami');
            })->count();

            //If Status Purusa (Apakah tetap di KK lama atau pindah KK)
            if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_lama'){
                //Copy Data Krama Mipil
                $krama_mipil_baru_purusa = new KramaMipil();
                $krama_mipil_baru_purusa->nomor_krama_mipil = $krama_mipil->nomor_krama_mipil;
                $krama_mipil_baru_purusa->banjar_adat_id = $krama_mipil->banjar_adat_id;
                $krama_mipil_baru_purusa->cacah_krama_mipil_id = $krama_mipil->cacah_krama_mipil_id;
                $krama_mipil_baru_purusa->kedudukan_krama_mipil = $krama_mipil->kedudukan_krama_mipil;
                if($jumlah_pasangan == 1){
                    $krama_mipil_baru_purusa->jenis_krama_mipil = 'krama_balu';
                }else{
                    $krama_mipil_baru_purusa->jenis_krama_mipil = $krama_mipil->jenis_krama_mipil;
                }
                $krama_mipil_baru_purusa->status = '1';
                $krama_mipil_baru_purusa->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perceraian)';
                $krama_mipil_baru_purusa->tanggal_registrasi = $krama_mipil->tanggal_registrasi;
                $krama_mipil_baru_purusa->save();

                //Nonaktifkan Data Anggota Keluarga Krama Mipil dan Keluarkan Pasangan
                $anggota_keluarga_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->where('status', '1')->get();
                foreach($anggota_keluarga_krama_mipil as $anggota_lama){
                    if($anggota_lama->cacah_krama_mipil_id == $perceraian->pradana_id){
                        $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                        $anggota_lama->alasan_keluar = 'Perceraian';
                    }
                    //Nonaktifkan Anggota Lama Krama Mipil
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }

                //Nonaktifkan Data Lama Krama Mipil
                $krama_mipil->status = '0';
                $krama_mipil->update();
            }else{
                //Nonaktifkan Krama Mipil dan Anggota Keluarga
                $anggota_keluarga_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->where('status', '1')->get();
                foreach($anggota_keluarga_krama_mipil as $anggota_lama){
                    if($anggota_lama->cacah_krama_mipil_id == $perceraian->pradana_id){
                        $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                        $anggota_lama->alasan_keluar = 'Perceraian';
                    }
                    //Nonaktifkan Anggota Lama Krama Mipil
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }
                $krama_mipil->status = '0';
                $krama_mipil->tanggal_nonaktif = $perceraian->tanggal_perceraian;
                $krama_mipil->alasan_keluar = 'Perceraian';
                $krama_mipil->update();

                //Masukkan Krama Mipil ke Krama Mipil Barunya
                $krama_mipil_tujuan_purusa = KramaMipil::find($perceraian->krama_mipil_baru_purusa_id);
                $anggota_krama_mipil_tujuan_purusa = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_tujuan_purusa->id)->where('status', '1')->get();

                $krama_mipil_baru_purusa = new KramaMipil();
                $krama_mipil_baru_purusa->nomor_krama_mipil = $krama_mipil_tujuan_purusa->nomor_krama_mipil;
                $krama_mipil_baru_purusa->banjar_adat_id = $krama_mipil_tujuan_purusa->banjar_adat_id;
                $krama_mipil_baru_purusa->cacah_krama_mipil_id = $krama_mipil_tujuan_purusa->cacah_krama_mipil_id;
                $krama_mipil_baru_purusa->kedudukan_krama_mipil = $krama_mipil_tujuan_purusa->kedudukan_krama_mipil;
                $krama_mipil_baru_purusa->jenis_krama_mipil = $krama_mipil_tujuan_purusa->jenis_krama_mipil;
                $krama_mipil_baru_purusa->status = '1';
                $krama_mipil_baru_purusa->alasan_perubahan = 'Penambahan Anggota Keluarga (Perceraian atau Mulih Bajang/Truna)';
                $krama_mipil_baru_purusa->tanggal_registrasi = $krama_mipil_tujuan_purusa->tanggal_registrasi;
                $krama_mipil_baru_purusa->save();

                foreach($anggota_krama_mipil_tujuan_purusa as $anggota_lama){
                    //Copy Data Anggota
                    $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_purusa->id;
                    $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_tujuan_baru->status_hubungan = $anggota_lama->status_hubungan;
                    $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                    $anggota_krama_mipil_tujuan_baru->status = '1';
                    $anggota_krama_mipil_tujuan_baru->save();

                    //Nonaktifkan data lama
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }

                //Masukkan Purusa Sebagai Anggota Keluarganya
                $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_purusa->id;
                $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $krama_mipil->cacah_krama_mipil_id;
                $anggota_krama_mipil_tujuan_baru->status_hubungan = $perceraian->status_hubungan_baru_purusa;
                $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $anggota_krama_mipil_tujuan_baru->status = '1';
                $anggota_krama_mipil_tujuan_baru->save();

                //Lebur Data Krama Mipil Tujuan Purusa Lama
                $krama_mipil_tujuan_purusa->status = '0';
                $krama_mipil_tujuan_purusa->update();
            }

            //If Status Pradana (Apakah tetap di banjar, keluar banjar, atau keluar bali)
            if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
                //Masukkan Krama Mipil ke Krama Mipil Barunya
                $krama_mipil_tujuan_pradana = KramaMipil::find($perceraian->krama_mipil_baru_pradana_id);
                $anggota_krama_mipil_tujuan_pradana = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_tujuan_pradana->id)->where('status', '1')->get();

                $krama_mipil_baru_pradana = new KramaMipil();
                $krama_mipil_baru_pradana->nomor_krama_mipil = $krama_mipil_tujuan_pradana->nomor_krama_mipil;
                $krama_mipil_baru_pradana->banjar_adat_id = $krama_mipil_tujuan_pradana->banjar_adat_id;
                $krama_mipil_baru_pradana->cacah_krama_mipil_id = $krama_mipil_tujuan_pradana->cacah_krama_mipil_id;
                $krama_mipil_baru_pradana->kedudukan_krama_mipil = $krama_mipil_tujuan_pradana->kedudukan_krama_mipil;
                $krama_mipil_baru_pradana->jenis_krama_mipil = $krama_mipil_tujuan_pradana->jenis_krama_mipil;
                $krama_mipil_baru_pradana->status = '1';
                $krama_mipil_baru_pradana->alasan_perubahan = 'Penambahan Anggota Keluarga (Perceraian atau Mulih Bajang/Truna)';
                $krama_mipil_baru_pradana->tanggal_registrasi = $krama_mipil_tujuan_pradana->tanggal_registrasi;
                $krama_mipil_baru_pradana->save();

                foreach($anggota_krama_mipil_tujuan_pradana as $anggota_lama){
                    //Copy Data Anggota
                    $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                    $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_tujuan_baru->status_hubungan = $anggota_lama->status_hubungan;
                    $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                    $anggota_krama_mipil_tujuan_baru->status = '1';
                    $anggota_krama_mipil_tujuan_baru->save();

                    //Nonaktifkan data lama
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }

                //Masukkan pradana Sebagai Anggota Keluarganya
                $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $perceraian->pradana_id;
                $anggota_krama_mipil_tujuan_baru->status_hubungan = $perceraian->status_hubungan_baru_pradana;
                $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $anggota_krama_mipil_tujuan_baru->status = '1';
                $anggota_krama_mipil_tujuan_baru->save();

                //Lebur Data Krama Mipil Tujuan pradana Lama
                $krama_mipil_tujuan_pradana->status = '0';
                $krama_mipil_tujuan_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $krama_mipil_tujuan_pradana->alasan_keluar = 'Perceraian';
                $krama_mipil_tujuan_pradana->update();
            }else{
                $pradana = CacahKramaMipil::find($perceraian->pradana_id);
                $pradana->status = '0';
                $pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $pradana->alasan_keluar = 'Perceraian';
                $pradana->update();

                if($perceraian->status_pradana == 'keluar_banjar'){
                    $krama_mipil_pradana = KramaMipil::with('cacah_krama_mipil.penduduk')->find($perceraian->krama_mipil_baru_pradana_id);
                    $pradana_baru = CacahKramaMipil::where('penduduk_id', $pradana->penduduk_id)->where('banjar_adat_id', $perceraian->banjar_adat_pradana_id)->first();
                    if(!$pradana_baru){
                        $nomor_cacah_krama_mipil_pradana_baru = Helper::generate_nomor_cacah_krama_mipil($pradana->penduduk_id, $perceraian->banjar_adat_pradana_id);
                        $pradana_baru = new CacahKramaMipil();
                        $pradana_baru->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil_pradana_baru;
                        $pradana_baru->banjar_adat_id = $perceraian->banjar_adat_pradana_id;
                        $pradana_baru->tempekan_id = $krama_mipil_pradana->cacah_krama_mipil->tempekan_id;
                        $pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                        $pradana_baru->penduduk_id = $pradana->penduduk_id;
                        $pradana_baru->status = '1';
                        $pradana_baru->jenis_kependudukan = $krama_mipil->cacah_krama_mipil->jenis_kependudukan;
                        if($krama_mipil_pradana->cacah_krama_mipil->jenis_kependudukan == 'adat_&_dinas'){
                            $pradana_baru->banjar_dinas_id = $krama_mipil_pradana->cacah_krama_mipil->banjar_dinas_id;
                        }
                        $pradana_baru->save();
                    }else{
                        $pradana_baru->status = '1';
                        $pradana_baru->tanggal_nonaktif = NULL;
                        $pradana_baru->alasan_keluar = NULL;
                        $pradana_baru->update();
                    }

                    //Masukkan Krama Mipil ke Krama Mipil Barunya
                    $krama_mipil_tujuan_pradana = KramaMipil::find($perceraian->krama_mipil_baru_pradana_id);
                    $anggota_krama_mipil_tujuan_pradana = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_tujuan_pradana->id)->where('status', '1')->get();

                    $krama_mipil_baru_pradana = new KramaMipil();
                    $krama_mipil_baru_pradana->nomor_krama_mipil = $krama_mipil_tujuan_pradana->nomor_krama_mipil;
                    $krama_mipil_baru_pradana->banjar_adat_id = $krama_mipil_tujuan_pradana->banjar_adat_id;
                    $krama_mipil_baru_pradana->cacah_krama_mipil_id = $krama_mipil_tujuan_pradana->cacah_krama_mipil_id;
                    $krama_mipil_baru_pradana->kedudukan_krama_mipil = $krama_mipil_tujuan_pradana->kedudukan_krama_mipil;
                    $krama_mipil_baru_pradana->jenis_krama_mipil = $krama_mipil_tujuan_pradana->jenis_krama_mipil;
                    $krama_mipil_baru_pradana->status = '1';
                    $krama_mipil_baru_pradana->alasan_perubahan = 'Penambahan Anggota Keluarga (Perceraian atau Mulih Bajang/Truna)';
                    $krama_mipil_baru_pradana->tanggal_registrasi = $krama_mipil_tujuan_pradana->tanggal_registrasi;
                    $krama_mipil_baru_pradana->save();

                    foreach($anggota_krama_mipil_tujuan_pradana as $anggota_lama){
                        //Copy Data Anggota
                        $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                        $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                        $anggota_krama_mipil_tujuan_baru->status_hubungan = $anggota_lama->status_hubungan;
                        $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                        $anggota_krama_mipil_tujuan_baru->status = '1';
                        $anggota_krama_mipil_tujuan_baru->save();

                        //Nonaktifkan data lama
                        $anggota_lama->status = '0';
                        $anggota_lama->update();
                    }

                    //Masukkan pradana Sebagai Anggota Keluarganya
                    $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                    $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $pradana_baru->id;
                    $anggota_krama_mipil_tujuan_baru->status_hubungan = $perceraian->status_hubungan_baru_pradana;
                    $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                    $anggota_krama_mipil_tujuan_baru->status = '1';
                    $anggota_krama_mipil_tujuan_baru->save();

                    //Lebur Data Krama Mipil Tujuan pradana Lama
                    $krama_mipil_tujuan_pradana->status = '0';
                    $krama_mipil_tujuan_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                    $krama_mipil_tujuan_pradana->alasan_keluar = 'Perceraian';
                    $krama_mipil_tujuan_pradana->update();

                    //Update Data Alamat Pradana Baru
                    $penduduk_baru_pradana = Penduduk::find($pradana_baru->penduduk_id);
                    $penduduk_baru_pradana->alamat = $krama_mipil_pradana->cacah_krama_mipil->penduduk->alamat;
                    $penduduk_baru_pradana->koordinat_alamat = $krama_mipil_pradana->cacah_krama_mipil->penduduk->koordinat_alamat;
                    $penduduk_baru_pradana->desa_id = $krama_mipil_pradana->cacah_krama_mipil->penduduk->desa_id;
                    $penduduk_baru_pradana->update();

                    //Update di Perceraian
                    $perceraian->pradana_id = $pradana_baru->id;
                    $perceraian->update();
                }
            }

            //Set Status Anggota Keluarga Lainnya
            if($perceraian->status_anggota != 'null'){
                $daftar_status_anggota = json_decode($perceraian->status_anggota);
                foreach($anggota_keluarga_krama_mipil as $anggota_lama){
                    foreach($daftar_status_anggota as $key=>$value){
                        if($anggota_lama->id == $key){
                            if($value == 'ikut_purusa'){
                                $anggota_baru_purusa = new AnggotaKramaMipil();
                                $anggota_baru_purusa->krama_mipil_id = $krama_mipil_baru_purusa->id;
                                $anggota_baru_purusa->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                                if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_lama'){
                                    $anggota_baru_purusa->status_hubungan = $anggota_lama->status_hubungan;
                                }else{
                                    if($perceraian->status_hubungan_baru_purusa == 'anak'){
                                        $anggota_baru_purusa->status_hubungan = 'cucu';
                                    }else{
                                        $anggota_baru_purusa->status_hubungan = 'famili_lain';
                                    }
                                }
                                $anggota_baru_purusa->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                $anggota_baru_purusa->status = '1';
                                $anggota_baru_purusa->save();
                            }else if($value == 'ikut_pradana'){
                                if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
                                    $anggota_baru_pradana = new AnggotaKramaMipil();
                                    $anggota_baru_pradana->krama_mipil_id = $krama_mipil_baru_pradana->id;
                                    $anggota_baru_pradana->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                                    if($perceraian->status_hubungan_baru_pradana == 'anak'){
                                        $anggota_baru_pradana->status_hubungan = 'cucu';
                                    }else{
                                        $anggota_baru_pradana->status_hubungan = 'famili_lain';
                                    }
                                    $anggota_baru_pradana->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                    $anggota_baru_pradana->status = '1';
                                    $anggota_baru_pradana->save();
                                }
                                //Keluarkan Anggota
                                $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                $anggota_lama->alasan_keluar = 'Perceraian (Ikut Pradana)';
                                $anggota_lama->update();

                                //Keluarkan Cacah
                                $cacah_anggota_lama = CacahKramaMipil::find($anggota_lama->cacah_krama_mipil_id);
                                $cacah_anggota_lama->status = '0';
                                $cacah_anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                $cacah_anggota_lama->alasan_keluar = 'Perceraian (Ikut Pradana)';
                                $cacah_anggota_lama->update();
                            }
                        }
                    }
                }
            }

            //Set Status Perkawinan Purusa dan Pradana
            $purusa = CacahKramaMipil::find($perceraian->purusa_id);
            $penduduk_purusa = Penduduk::find($purusa->penduduk_id);
            if($jumlah_pasangan == 1){
                $penduduk_purusa->status_perkawinan = 'cerai_hidup';
                $penduduk_purusa->update();
            }

            $pradana = CacahKramaMipil::find($perceraian->pradana_id);
            $penduduk_pradana = Penduduk::find($pradana->penduduk_id);
            $penduduk_pradana->status_perkawinan = 'cerai_hidup';
            $penduduk_pradana->update();
        }

        private function krama_mipil_pradana($perceraian){
            //Get Master Data
            $krama_mipil = KramaMipil::find($perceraian->krama_mipil_id);
            $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
            $banjar_adat = BanjarAdat::find($banjar_adat_id);
            $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

            //Count Jumlah Pasangan (Penentu apakah akan balu atau tidak!)
            $jumlah_pasangan = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where(function ($query) {
                $query->where('status_hubungan', 'istri')
                    ->orWhere('status_hubungan', 'suami');
            })->count();

            //If Status Purusa (Apakah tetap di KK lama atau pindah KK)
            if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_lama'){
                //Copy Data Krama Mipil
                $krama_mipil_baru_purusa = new KramaMipil();
                $krama_mipil_baru_purusa->nomor_krama_mipil = $krama_mipil->nomor_krama_mipil;
                $krama_mipil_baru_purusa->banjar_adat_id = $krama_mipil->banjar_adat_id;
                $krama_mipil_baru_purusa->cacah_krama_mipil_id = $perceraian->purusa_id;
                $krama_mipil_baru_purusa->kedudukan_krama_mipil = $krama_mipil->kedudukan_krama_mipil;
                $krama_mipil_baru_purusa->jenis_krama_mipil = 'krama_balu';
                $krama_mipil_baru_purusa->status = '1';
                $krama_mipil_baru_purusa->alasan_perubahan = 'Pengurangan Anggota Keluarga (Perceraian)';
                $krama_mipil_baru_purusa->tanggal_registrasi = $perceraian->tanggal_perceraian;
                $krama_mipil_baru_purusa->save();

                //Nonaktifkan Data Anggota Keluarga Krama Mipil dan Keluarkan Pasangan
                $anggota_keluarga_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->where('status', '1')->get();
                foreach($anggota_keluarga_krama_mipil as $anggota_lama){
                    //Nonaktifkan Anggota Lama Krama Mipil
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }

                //Nonaktifkan Data Lama Krama Mipil
                $krama_mipil->status = '0';
                $krama_mipil->update();
            }else{
                //Nonaktifkan Krama Mipil dan Anggota Keluarga
                $anggota_keluarga_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->where('status', '1')->get();
                foreach($anggota_keluarga_krama_mipil as $anggota_lama){
                    //Nonaktifkan Anggota Lama Krama Mipil
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }
                $krama_mipil->status = '0';
                $krama_mipil->tanggal_nonaktif = $perceraian->tanggal_perceraian;
                $krama_mipil->alasan_keluar = 'Perceraian';
                $krama_mipil->update();

                //Masukkan Krama Mipil ke Krama Mipil Barunya
                $krama_mipil_tujuan_purusa = KramaMipil::find($perceraian->krama_mipil_baru_purusa_id);
                $anggota_krama_mipil_tujuan_purusa = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_tujuan_purusa->id)->where('status', '1')->get();

                $krama_mipil_baru_purusa = new KramaMipil();
                $krama_mipil_baru_purusa->nomor_krama_mipil = $krama_mipil_tujuan_purusa->nomor_krama_mipil;
                $krama_mipil_baru_purusa->banjar_adat_id = $krama_mipil_tujuan_purusa->banjar_adat_id;
                $krama_mipil_baru_purusa->cacah_krama_mipil_id = $krama_mipil_tujuan_purusa->cacah_krama_mipil_id;
                $krama_mipil_baru_purusa->kedudukan_krama_mipil = $krama_mipil_tujuan_purusa->kedudukan_krama_mipil;
                $krama_mipil_baru_purusa->jenis_krama_mipil = $krama_mipil_tujuan_purusa->jenis_krama_mipil;
                $krama_mipil_baru_purusa->status = '1';
                $krama_mipil_baru_purusa->alasan_perubahan = 'Penambahan Anggota Keluarga (Perceraian atau Mulih Bajang/Truna)';
                $krama_mipil_baru_purusa->tanggal_registrasi = $krama_mipil_tujuan_purusa->tanggal_registrasi;
                $krama_mipil_baru_purusa->save();

                foreach($anggota_krama_mipil_tujuan_purusa as $anggota_lama){
                    //Copy Data Anggota
                    $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_purusa->id;
                    $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_tujuan_baru->status_hubungan = $anggota_lama->status_hubungan;
                    $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                    $anggota_krama_mipil_tujuan_baru->status = '1';
                    $anggota_krama_mipil_tujuan_baru->save();

                    //Nonaktifkan data lama
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }

                //Masukkan Purusa Sebagai Anggota Keluarganya
                $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_purusa->id;
                $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $perceraian->purusa_id;
                $anggota_krama_mipil_tujuan_baru->status_hubungan = $perceraian->status_hubungan_baru_purusa;
                $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $anggota_krama_mipil_tujuan_baru->status = '1';
                $anggota_krama_mipil_tujuan_baru->save();

                //Lebur Data Krama Mipil Tujuan Purusa Lama
                $krama_mipil_tujuan_purusa->status = '0';
                $krama_mipil_tujuan_purusa->update();
            }

            //If Status Pradana (Apakah tetap di banjar, keluar banjar, atau keluar bali)
            if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
                //Masukkan Krama Mipil ke Krama Mipil Barunya
                $krama_mipil_tujuan_pradana = KramaMipil::find($perceraian->krama_mipil_baru_pradana_id);
                $anggota_krama_mipil_tujuan_pradana = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_tujuan_pradana->id)->where('status', '1')->get();

                $krama_mipil_baru_pradana = new KramaMipil();
                $krama_mipil_baru_pradana->nomor_krama_mipil = $krama_mipil_tujuan_pradana->nomor_krama_mipil;
                $krama_mipil_baru_pradana->banjar_adat_id = $krama_mipil_tujuan_pradana->banjar_adat_id;
                $krama_mipil_baru_pradana->cacah_krama_mipil_id = $krama_mipil_tujuan_pradana->cacah_krama_mipil_id;
                $krama_mipil_baru_pradana->kedudukan_krama_mipil = $krama_mipil_tujuan_pradana->kedudukan_krama_mipil;
                $krama_mipil_baru_pradana->jenis_krama_mipil = $krama_mipil_tujuan_pradana->jenis_krama_mipil;
                $krama_mipil_baru_pradana->status = '1';
                $krama_mipil_baru_pradana->alasan_perubahan = 'Penambahan Anggota Keluarga (Perceraian atau Mulih Bajang/Truna)';
                $krama_mipil_baru_pradana->tanggal_registrasi = $krama_mipil_tujuan_pradana->tanggal_registrasi;
                $krama_mipil_baru_pradana->save();

                foreach($anggota_krama_mipil_tujuan_pradana as $anggota_lama){
                    //Copy Data Anggota
                    $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                    $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_tujuan_baru->status_hubungan = $anggota_lama->status_hubungan;
                    $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                    $anggota_krama_mipil_tujuan_baru->status = '1';
                    $anggota_krama_mipil_tujuan_baru->save();

                    //Nonaktifkan data lama
                    $anggota_lama->status = '0';
                    $anggota_lama->update();
                }

                //Masukkan pradana Sebagai Anggota Keluarganya
                $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $perceraian->pradana_id;
                $anggota_krama_mipil_tujuan_baru->status_hubungan = $perceraian->status_hubungan_baru_pradana;
                $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $anggota_krama_mipil_tujuan_baru->status = '1';
                $anggota_krama_mipil_tujuan_baru->save();

                //Lebur Data Krama Mipil Tujuan pradana Lama
                $krama_mipil_tujuan_pradana->status = '0';
                $krama_mipil_tujuan_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $krama_mipil_tujuan_pradana->alasan_keluar = 'Perceraian';
                $krama_mipil_tujuan_pradana->update();
            }else{
                $pradana = CacahKramaMipil::find($perceraian->pradana_id);
                $pradana->status = '0';
                $pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                $pradana->alasan_keluar = 'Perceraian';
                $pradana->update();

                if($perceraian->status_pradana == 'keluar_banjar'){
                    $krama_mipil_pradana = KramaMipil::with('cacah_krama_mipil.penduduk')->find($perceraian->krama_mipil_baru_pradana_id);
                    $pradana_baru = CacahKramaMipil::where('penduduk_id', $pradana->penduduk_id)->where('banjar_adat_id', $perceraian->banjar_adat_pradana_id)->first();
                    if(!$pradana_baru){
                        $nomor_cacah_krama_mipil_pradana_baru = Helper::generate_nomor_cacah_krama_mipil($pradana->penduduk_id, $perceraian->banjar_adat_pradana_id);
                        $pradana_baru = new CacahKramaMipil();
                        $pradana_baru->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil_pradana_baru;
                        $pradana_baru->banjar_adat_id = $perceraian->banjar_adat_pradana_id;
                        $pradana_baru->tempekan_id = $krama_mipil_pradana->cacah_krama_mipil->tempekan_id;
                        $pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                        $pradana_baru->penduduk_id = $pradana->penduduk_id;
                        $pradana_baru->status = '1';
                        $pradana_baru->jenis_kependudukan = $krama_mipil->cacah_krama_mipil->jenis_kependudukan;
                        if($krama_mipil_pradana->cacah_krama_mipil->jenis_kependudukan == 'adat_&_dinas'){
                            $pradana_baru->banjar_dinas_id = $krama_mipil_pradana->cacah_krama_mipil->banjar_dinas_id;
                        }
                        $pradana_baru->save();
                    }else{
                        $pradana_baru->status = '1';
                        $pradana_baru->tanggal_nonaktif = NULL;
                        $pradana_baru->alasan_keluar = NULL;
                        $pradana_baru->update();
                    }
                    //Masukkan Krama Mipil ke Krama Mipil Barunya
                    $krama_mipil_tujuan_pradana = KramaMipil::find($perceraian->krama_mipil_baru_pradana_id);
                    $anggota_krama_mipil_tujuan_pradana = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_tujuan_pradana->id)->where('status', '1')->get();

                    $krama_mipil_baru_pradana = new KramaMipil();
                    $krama_mipil_baru_pradana->nomor_krama_mipil = $krama_mipil_tujuan_pradana->nomor_krama_mipil;
                    $krama_mipil_baru_pradana->banjar_adat_id = $krama_mipil_tujuan_pradana->banjar_adat_id;
                    $krama_mipil_baru_pradana->cacah_krama_mipil_id = $krama_mipil_tujuan_pradana->cacah_krama_mipil_id;
                    $krama_mipil_baru_pradana->kedudukan_krama_mipil = $krama_mipil_tujuan_pradana->kedudukan_krama_mipil;
                    $krama_mipil_baru_pradana->jenis_krama_mipil = $krama_mipil_tujuan_pradana->jenis_krama_mipil;
                    $krama_mipil_baru_pradana->status = '1';
                    $krama_mipil_baru_pradana->alasan_perubahan = 'Penambahan Anggota Keluarga (Perceraian atau Mulih Bajang/Truna)';
                    $krama_mipil_baru_pradana->tanggal_registrasi = $krama_mipil_tujuan_pradana->tanggal_registrasi;
                    $krama_mipil_baru_pradana->save();

                    foreach($anggota_krama_mipil_tujuan_pradana as $anggota_lama){
                        //Copy Data Anggota
                        $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                        $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                        $anggota_krama_mipil_tujuan_baru->status_hubungan = $anggota_lama->status_hubungan;
                        $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                        $anggota_krama_mipil_tujuan_baru->status = '1';
                        $anggota_krama_mipil_tujuan_baru->save();

                        //Nonaktifkan data lama
                        $anggota_lama->status = '0';
                        $anggota_lama->update();
                    }

                    //Masukkan pradana Sebagai Anggota Keluarganya
                    $anggota_krama_mipil_tujuan_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_tujuan_baru->krama_mipil_id = $krama_mipil_baru_pradana->id;
                    $anggota_krama_mipil_tujuan_baru->cacah_krama_mipil_id = $pradana_baru->id;
                    $anggota_krama_mipil_tujuan_baru->status_hubungan = $perceraian->status_hubungan_baru_pradana;
                    $anggota_krama_mipil_tujuan_baru->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                    $anggota_krama_mipil_tujuan_baru->status = '1';
                    $anggota_krama_mipil_tujuan_baru->save();

                    //Lebur Data Krama Mipil Tujuan pradana Lama
                    $krama_mipil_tujuan_pradana->status = '0';
                    $krama_mipil_tujuan_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                    $krama_mipil_tujuan_pradana->alasan_keluar = 'Perceraian';
                    $krama_mipil_tujuan_pradana->update();

                    //Update Data Alamat Pradana Baru
                    $penduduk_baru_pradana = Penduduk::find($pradana_baru->penduduk_id);
                    $penduduk_baru_pradana->alamat = $krama_mipil_pradana->cacah_krama_mipil->penduduk->alamat;
                    $penduduk_baru_pradana->koordinat_alamat = $krama_mipil_pradana->cacah_krama_mipil->penduduk->koordinat_alamat;
                    $penduduk_baru_pradana->desa_id = $krama_mipil_pradana->cacah_krama_mipil->penduduk->desa_id;
                    $penduduk_baru_pradana->update();

                    //Update di Perceraian
                    $perceraian->pradana_id = $pradana_baru->id;
                    $perceraian->update();
                }
            }

            //Set Status Anggota Keluarga Lainnya
            if($perceraian->status_anggota != 'null'){
                $daftar_status_anggota = json_decode($perceraian->status_anggota);
                foreach($anggota_keluarga_krama_mipil as $anggota_lama){
                    foreach($daftar_status_anggota as $key=>$value){
                        if($anggota_lama->id == $key){
                            if($value == 'ikut_purusa'){
                                $anggota_baru_purusa = new AnggotaKramaMipil();
                                $anggota_baru_purusa->krama_mipil_id = $krama_mipil_baru_purusa->id;
                                $anggota_baru_purusa->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                                if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_lama'){
                                    $anggota_baru_purusa->status_hubungan = $anggota_lama->status_hubungan;
                                }else{
                                    if($perceraian->status_hubungan_baru_purusa == 'anak'){
                                        $anggota_baru_purusa->status_hubungan = 'cucu';
                                    }else{
                                        $anggota_baru_purusa->status_hubungan = 'famili_lain';
                                    }
                                }
                                $anggota_baru_purusa->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                $anggota_baru_purusa->status = '1';
                                $anggota_baru_purusa->save();
                            }else if($value == 'ikut_pradana'){
                                if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
                                    $anggota_baru_pradana = new AnggotaKramaMipil();
                                    $anggota_baru_pradana->krama_mipil_id = $krama_mipil_baru_pradana->id;
                                    $anggota_baru_pradana->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                                    if($perceraian->status_hubungan_baru_pradana == 'anak'){
                                        $anggota_baru_pradana->status_hubungan = 'cucu';
                                    }else{
                                        $anggota_baru_pradana->status_hubungan = 'famili_lain';
                                    }
                                    $anggota_baru_pradana->tanggal_registrasi = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                    $anggota_baru_pradana->status = '1';
                                    $anggota_baru_pradana->save();
                                }
                                //Keluarkan Anggota
                                $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                $anggota_lama->alasan_keluar = 'Perceraian (Ikut Pradana)';
                                $anggota_lama->update();

                                //Keluarkan Cacah
                                $cacah_anggota_lama = CacahKramaMipil::find($anggota_lama->cacah_krama_mipil_id);
                                $cacah_anggota_lama->status = '0';
                                $cacah_anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($perceraian->tanggal_perceraian));
                                $cacah_anggota_lama->alasan_keluar = 'Perceraian (Ikut Pradana)';
                                $cacah_anggota_lama->update();
                            }
                        }
                    }
                }
            }

            //Set Status Perkawinan Purusa dan Pradana
            $purusa = CacahKramaMipil::find($perceraian->purusa_id);
            $penduduk_purusa = Penduduk::find($purusa->penduduk_id);
            $penduduk_purusa->status_perkawinan = 'cerai_hidup';

            $pradana = CacahKramaMipil::find($perceraian->pradana_id);
            $penduduk_pradana = Penduduk::find($pradana->penduduk_id);
            $penduduk_pradana->status_perkawinan = 'cerai_hidup';
            $penduduk_pradana->update();
        }
        //Fungsi Helper Untuk Purusa Pradana
}
