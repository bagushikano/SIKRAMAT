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
use App\Models\Maperas;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Models\User;
use App\Helper\Helper;

class MaperasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function get(Request $req)
    {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;

        $maperas = Maperas::query();

        if ($req->query('status') == '3') {
            $maperas->where('status_maperas', '3');
        } else if ($req->query('status') == '2') {
            $maperas->where('status_maperas', '2');
        } else if ($req->query('status') == '1') {
            $maperas->where('status_maperas', '1');
        } else if ($req->query('status') == '0') {
            $maperas->where('status_maperas', '0');
        }

        if ( $req->query('type') ) {
            $maperas->where('jenis_maperas', $req->query('type'));
        }

        if ( $req->query('start_date') ) {
            // $rentang_waktu = explode(' - ', $request->rentang_waktu);
            // $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            // $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));

            $maperas->whereBetween('tanggal_maperas', [$req->query('start_date'), $req->query('end_date')]);
        }

        $maperas->where(function ($query) use ($banjar_adat_id) {
                                $query->where('banjar_adat_lama_id', $banjar_adat_id)
                                    ->orWhere('banjar_adat_baru_id', $banjar_adat_id);
                            })
                            ->with('cacah_krama_mipil_baru.penduduk', 'cacah_krama_mipil_lama.penduduk')
                            ->orderBy('tanggal_maperas', 'desc');

        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $maperas->paginate(10),
                'message' => 'data maperas sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    public function detail(Request $req)
    {
        $maperas = Maperas::where('id', $req->query('maperas'))
                                    ->with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_lama.tempekan',
                                            'cacah_krama_mipil_baru.penduduk', 'cacah_krama_mipil_baru.tempekan', 'krama_mipil_lama.banjar_adat.desa_adat',
                                            'krama_mipil_baru.banjar_adat.desa_adat',
                                            'krama_mipil_lama.banjar_adat.desa_adat.kecamatan.kabupaten',
                                            'krama_mipil_lama.cacah_krama_mipil.penduduk',
                                            'krama_mipil_baru.cacah_krama_mipil.penduduk',
                                            'ayah_lama.penduduk', 'ayah_lama.tempekan',
                                            'ayah_baru.penduduk', 'ayah_baru.tempekan',
                                            'ibu_lama.penduduk', 'ibu_lama.tempekan', 'ibu_baru.penduduk', 'ibu_baru.tempekan',
                                            'desa_dinas_asal.kecamatan.kabupaten.provinsi')
                                    ->first();
        $sisiBanjarAdatKeluar = false;
        if (Auth::user()->prajuru_banjar_adat->banjar_adat_id == $maperas->banjar_adat_lama_id) {
            $sisiBanjarAdatKeluar = true;
        }
        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $maperas,
                'sisi_banjar_adat_keluar' => $sisiBanjarAdatKeluar,
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }


    public function getKramaMipilLama(Request $request){
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;

        // pake ini untuk beda banjar
        if ($request->query('banjar_adat_id') != null) {
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                    ->where('banjar_adat_id', $request->query('banjar_adat_id'))
                                    ->where('status', '1')
                                    ->orderBy('tanggal_registrasi', 'DESC')
                                    ->paginate(100);
        }
        //ini untku yg satu banjar
        else{
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                    ->where('banjar_adat_id', $banjar_adat_id)
                                    ->where('status', '1')
                                    ->orderBy('tanggal_registrasi', 'DESC')
                                    ->paginate(100);
        }
        if ($kramas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
                'message' => 'data krama maperas sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama maperas fail'
            ], 500);
        }
    }

    public function getKramaMipilBaru(Request $request){
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat', 'anggota.cacah_krama_mipil.penduduk')
                                ->where('banjar_adat_id', $banjar_adat_id)
                                ->where('status', '1')
                                ->where('id', '!=', $request->query('krama_mipil_lama_id'))
                                ->orderBy('tanggal_registrasi', 'DESC')
                                ->paginate(100);

        if ($kramas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $kramas,
                'message' => 'data krama maperas sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama maperas fail'
            ], 500);
        }
    }

    public function getDaftarAnak(Request $req){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($req->query('krama_mipil_id'));
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
                                ->where('krama_mipil_id', $krama_mipil->id)->where('status', '1')
                                ->where('status_hubungan', '!=', 'istri')->where('status_hubungan', '!=', 'suami')
                                ->where('status_hubungan', '!=', 'menantu')->where('status_hubungan', '!=', 'mertua')
                                ->where('status_hubungan', '!=', 'ayah')->where('status_hubungan', '!=', 'ibu')
                                ->get();

        if ($anggota_krama_mipil) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $anggota_krama_mipil,
                'message' => 'data krama maperas sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama maperas fail'
            ], 500);
        }
    }

    public function getOrtuLama(Request $req){
        $cacah_krama_mipil = CacahKramaMipil::with('penduduk.ayah', 'penduduk.ibu')->find($req->query('cacah_anak_id'));
        if ($cacah_krama_mipil) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $cacah_krama_mipil,
                'message' => 'data krama maperas sukses'
            ], 200);
        }

        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data krama maperas fail'
            ], 500);
        }
    }

    public function getOrtuBaru(Request $req){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($req->query('krama_baru_id'));
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
                                        ->where('krama_mipil_id', $krama_mipil->id)
                                        ->where('status', '1')
                                        ->get();
        $ayah = new Collection();
        $ibu = new Collection();

        // determine klo yg jadi krama mipil laki/cewek, klo laki, masukin ke ayah, klo cewek masukin ke collection cewk
        if($krama_mipil->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
            $ayah->push($krama_mipil->cacah_krama_mipil);
        }else{
            $ibu->push($krama_mipil->cacah_krama_mipil);
        }

        foreach($anggota_krama_mipil as $anggota){
            //determine setiap anggota laki/cewek, klo laki, masukin ke ayah, klo cewek masukin ke collection cewk
            if($anggota->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                $ayah->push($anggota->cacah_krama_mipil);
            }else{
                $ibu->push($anggota->cacah_krama_mipil);
            }
        }

        // $ortu = new Collection();
        // $ortu->push($ayah);
        // $ortu->push($ibu);
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'ayah' => $ayah,
            'ibu' => $ibu,
            'message' => 'data krama maperas sukses'
        ], 200);
    }

    public function storeSatuBanjar(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
        //     'krama_mipil_lama' => 'required',
        //     'krama_mipil_baru' => 'required',
        //     'anak' => 'required',
        //     'ayah_baru' => 'required',
        //     'ibu_baru' => 'required',
        //     'nama_pemuput' => 'required',
        //     'file_bukti_maperas' => 'required',
        //     'tanggal_maperas' => 'required',
        //     'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
        //     'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',
        // ],[
        //     'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
        //     'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
        //     'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
        //     'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
        //     'anak.required' => "Anak wajib dipilih",
        //     'ayah_baru.required' => "Ayah Baru wajib dipilih",
        //     'ibu_baru.required' => "Ibu Baru wajib dipilih",
        //     'nama_pemuput.required' => "Nama Pemuput wajib diisi",
        //     'file_bukti_maperas.required' => "Bukti Maperas wajib diunggah",
        //     'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
        //     'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
        //     'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        //     'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
        // ]);

        /**
         * form data
         * maperas_json -> json data maperas
         * file_bukti_maperas -> file
         * file_akta_pengangkatan_anak -> file
         */

        $maperasObject = json_decode($request->maperas_json);

        /// get krama mipil lama + baru
        $krama_mipil_lama = KramaMipil::find($maperasObject->krama_mipil_lama_id);
        $krama_mipil_baru = KramaMipil::find($maperasObject->krama_mipil_baru_id);

        //get banjar lama baru + desadat lama baru, klo satu banjar kedua2 nya di assign yg sama
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //get cacah krama mipil anak, klo satu banjar, yg lama sama yg baru di assign sama
        $cacah_krama_mipil_lama = CacahKramaMipil::find($maperasObject->cacah_krama_mipil_lama_id);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);
        $cacah_krama_mipil_baru = CacahKramaMipil::find($maperasObject->cacah_krama_mipil_baru_id);

        //get ortu lama anak dari penduduk
        $ayah_lama = Penduduk::find($penduduk_anak->ayah_kandung_id);
        if($ayah_lama){
            $ayah_lama = CacahKramaMipil::where('penduduk_id', $ayah_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }
        $ibu_lama = Penduduk::find($penduduk_anak->ibu_kandung_id);
        if($ibu_lama){
            $ibu_lama = CacahKramaMipil::where('penduduk_id', $ibu_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }

        //get cacah krama ortu baru
        $ayah_baru = CacahKramaMipil::find($maperasObject->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperasObject->ibu_baru_id);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-", $maperasObject->nomor_maperas);

        $maperas = new Maperas();
        $maperas->jenis_maperas = 'satu_banjar_adat';
        $maperas->nomor_maperas = $maperasObject->nomor_maperas;
        $maperas->nomor_akta_pengangkatan_anak = $maperasObject->nomor_akta_pengangkatan_anak;
        $maperas->krama_mipil_lama_id = $maperasObject->krama_mipil_lama_id;
        $maperas->krama_mipil_baru_id = $maperasObject->krama_mipil_baru_id;
        $maperas->cacah_krama_mipil_lama_id = $cacah_krama_mipil_lama->id;
        $maperas->cacah_krama_mipil_baru_id = $cacah_krama_mipil_baru->id;
        $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
        $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
        $maperas->desa_adat_lama_id = $desa_adat_lama->id;
        $maperas->desa_adat_baru_id = $desa_adat_baru->id;
        $maperas->keterangan = $maperasObject->keterangan;
        if($ayah_lama){
            $maperas->ayah_lama_id = $ayah_lama->id;
        }
        if($ibu_lama){
            $maperas->ibu_lama_id = $ibu_lama->id;
        }
        $maperas->ayah_baru_id = $maperasObject->ayah_baru_id;
        $maperas->ibu_baru_id = $maperasObject->ibu_baru_id;
        $maperas->tanggal_maperas = date("Y-m-d", strtotime($maperasObject->tanggal_maperas));
        $maperas->nama_pemuput = $maperasObject->nama_pemuput;
        $maperas->status_maperas = '0';
        if($request->file('file_bukti_maperas')!=""){
            $file = $request->file('file_bukti_maperas');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_bukti_maperas = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_pengangkatan_anak')!=""){
            $file = $request->file('file_akta_pengangkatan_anak');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $maperas->save();

        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')->where('id', $maperas->id)->first(),
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    public function sahSatuBanjar(Request $request) {
        $maperas = Maperas::where('id', $request->query("maperas"))->first();
        $krama_mipil_baru = KramaMipil::where('id', $maperas->krama_mipil_baru_id)->first();
        $krama_mipil_lama = KramaMipil::where('id', $maperas->krama_mipil_lama_id)->first();


        //get cacah krama ortu baru
        $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);

        $ayah_baru = CacahKramaMipil::where('id', $maperas->ayah_baru_id)->first();
        $cacah_krama_mipil_lama = CacahKramaMipil::find($maperas->cacah_krama_mipil_lama_id);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);
        $cacah_krama_mipil_baru = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);

        if($krama_mipil_lama->id != $krama_mipil_baru->id){
            //COPY DATA KRAMA MIPIL LAMA & KELUARKAN ANAK
            $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->where('status', '1')->get();
            $krama_mipil_lama_copy = new KramaMipil();
            $krama_mipil_lama_copy->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
            $krama_mipil_lama_copy->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
            $krama_mipil_lama_copy->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
            $krama_mipil_lama_copy->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
            $krama_mipil_lama_copy->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
            $krama_mipil_lama_copy->status = '1';
            $krama_mipil_lama_copy->alasan_perubahan = 'Pengurangan Anggota Keluarga (Maperas)';
            $krama_mipil_lama_copy->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
            $krama_mipil_lama_copy->save();

            //COPY DATA ANGGOTA LAMA
            foreach($anggota_krama_mipil_lama as $anggota_lama){
                if($anggota_lama->cacah_krama_mipil_id != $cacah_krama_mipil_lama->id){
                    $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
                    $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_lama_copy->id;
                    $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_lama_copy->status_hubungan = $anggota_lama->status_hubungan;
                    $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                    $anggota_krama_mipil_lama_copy->status = '1';
                    $anggota_krama_mipil_lama_copy->save();
                }else{
                    $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($maperas->tanggal_maperas));
                    $anggota_lama->alasan_keluar = 'Maperas (Satu Banjar Adat)';
                }
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }

            //LEBUR KK LAMA
            $krama_mipil_lama->status = '0';
            $krama_mipil_lama->update();

            //COPY DATA KRAMA MIPIL BARU
            $anggota_krama_mipil_baru = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_baru->id)->where('status', '1')->get();
            $krama_mipil_baru_copy = new KramaMipil();
            $krama_mipil_baru_copy->nomor_krama_mipil = $krama_mipil_baru->nomor_krama_mipil;
            $krama_mipil_baru_copy->banjar_adat_id = $krama_mipil_baru->banjar_adat_id;
            $krama_mipil_baru_copy->cacah_krama_mipil_id = $krama_mipil_baru->cacah_krama_mipil_id;
            $krama_mipil_baru_copy->kedudukan_krama_mipil = $krama_mipil_baru->kedudukan_krama_mipil;
            $krama_mipil_baru_copy->jenis_krama_mipil = $krama_mipil_baru->jenis_krama_mipil;
            $krama_mipil_baru_copy->status = '1';
            $krama_mipil_baru_copy->alasan_perubahan = 'Penambahan Anggota Keluarga (Maperas)';
            $krama_mipil_baru_copy->tanggal_registrasi = $krama_mipil_baru->tanggal_registrasi;
            $krama_mipil_baru_copy->save();

            //COPY DATA ANGGOTA LAMA
            foreach($anggota_krama_mipil_baru as $anggota_baru){
                $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
                $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_baru_copy->id;
                $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $anggota_baru->cacah_krama_mipil_id;
                $anggota_krama_mipil_lama_copy->status_hubungan = $anggota_baru->status_hubungan;
                $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($anggota_baru->tanggal_registrasi));
                $anggota_krama_mipil_lama_copy->status = '1';
                $anggota_krama_mipil_lama_copy->save();
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_baru->status = '0';
                $anggota_baru->update();
            }

            //LEBUR KK LAMA
            $krama_mipil_baru->status = '0';
            $krama_mipil_baru->update();

            //MASUKKAN ANAK KE KRAMA MIPIL BARU
            $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
            $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_baru_copy->id;
            $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $cacah_krama_mipil_baru->id;
            if($ayah_baru->id == $krama_mipil_baru->cacah_krama_mipil->id){
                $anggota_krama_mipil_lama_copy->status_hubungan = 'anak';
            }else{
                $anggota_krama_mipil_lama_copy->status_hubungan = 'famili_lain';
            }
            $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($maperas->tanggal_maperas));
            $anggota_krama_mipil_lama_copy->status = '1';
            $anggota_krama_mipil_lama_copy->save();
        }
        //UBAH ORANG TUA PENDUDUK ANAK
        $penduduk_anak->ayah_kandung_id = $ayah_baru->penduduk->id;
        $penduduk_anak->ibu_kandung_id = $ibu_baru->penduduk->id;

        //UBAH ALAMAT ANAK
        $penduduk_anak->alamat = $ayah_baru->penduduk->alamat;
        $penduduk_anak->koordinat_alamat = $ayah_baru->penduduk->koordinat_alamat;
        $penduduk_anak->desa_id = $ayah_baru->penduduk->desa_id;
        $penduduk_anak->update();

        //UBAH BANJAR DINAS
        $cacah_krama_mipil_baru->jenis_kependudukan = $ayah_baru->jenis_kependudukan;
        $cacah_krama_mipil_baru->banjar_dinas_id = $ayah_baru->banjar_dinas_id;
        $cacah_krama_mipil_baru->update();

        //update data krama mipil baru di maperas
        $maperas->krama_mipil_baru_id = $krama_mipil_baru_copy->id;
        $maperas->status_maperas = '3';
        $maperas->update();

        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $maperas,
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    public function storeBedaBanjar(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
        //     'krama_mipil_lama' => 'required',
        //     'krama_mipil_baru' => 'required',
        //     'anak' => 'required',
        //     'ayah_baru' => 'required',
        //     'ibu_baru' => 'required',
        //     'nama_pemuput' => 'required',
        //     'file_bukti_maperas' => 'required',
        //     'tanggal_maperas' => 'required',
        //     'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
        //     'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',
        // ],[
        //     'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
        //     'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
        //     'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
        //     'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
        //     'anak.required' => "Anak wajib dipilih",
        //     'ayah_baru.required' => "Ayah Baru wajib dipilih",
        //     'ibu_baru.required' => "Ibu Baru wajib dipilih",
        //     'nama_pemuput.required' => "Nama Pemuput wajib diisi",
        //     'file_bukti_maperas.required' => "File Bukti Maperas wajib diunggah",
        //     'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
        //     'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
        //     'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        //     'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        /**
         * form data
         * maperas_json -> json data maperas
         * file_bukti_maperas -> file
         * file_akta_pengangkatan_anak -> file
         */

        $maperasObject = json_decode($request->maperas_json);

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($maperasObject->krama_mipil_lama_id);
        $krama_mipil_baru = KramaMipil::find($maperasObject->krama_mipil_baru_id);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($maperasObject->cacah_krama_mipil_lama_id);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);

        //GET ORTU LAMA
        $ayah_lama = Penduduk::find($penduduk_anak->ayah_kandung_id);
        if($ayah_lama){
            $ayah_lama = CacahKramaMipil::where('penduduk_id', $ayah_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }
        $ibu_lama = Penduduk::find($penduduk_anak->ibu_kandung_id);
        if($ibu_lama){
            $ibu_lama = CacahKramaMipil::where('penduduk_id', $ibu_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }

        //GET ORTU BARU
        $ayah_baru = CacahKramaMipil::find($maperasObject->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperasObject->ibu_baru_id);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$maperasObject->nomor_maperas);

        $maperas = new Maperas();
            $maperas->jenis_maperas = 'beda_banjar_adat';
            $maperas->nomor_maperas = $maperasObject->nomor_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $maperasObject->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $maperasObject->krama_mipil_lama_id;
            $maperas->krama_mipil_baru_id = $maperasObject->krama_mipil_baru_id;
            $maperas->cacah_krama_mipil_lama_id = $maperasObject->cacah_krama_mipil_lama_id;
            $maperas->cacah_krama_mipil_baru_id = $maperasObject->cacah_krama_mipil_baru_id;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $maperasObject->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $maperasObject->ayah_baru_id;
            $maperas->ibu_baru_id = $maperasObject->ibu_baru_id;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($maperasObject->tanggal_maperas));
            $maperas->nama_pemuput = $maperasObject->nama_pemuput;
            $maperas->status_maperas = '0';
            if($request->file('file_bukti_maperas')!=""){
                $file = $request->file('file_bukti_maperas');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $maperas->file_bukti_maperas = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_akta_pengangkatan_anak')!=""){
                $file = $request->file('file_akta_pengangkatan_anak');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $maperas->save();

            /**
             * create notif baru
             */

            $notifikasi = new Notifikasi();
            $notifikasi->notif_create_maperas_beda_banjar_adat($maperas->id);

            $userNotif = new User();
            error_log('ngirim notif perkawinan');

            $banjarAdatLama = BanjarAdat::find($maperas->banjar_adat_lama_id);
            $desaAdatLama = DesaAdat::find($banjarAdatLama->desa_adat_id);
            $banjarAdatBaru = BanjarAdat::find($maperas->banjar_adat_baru_id);
            $desaAdatBaru = DesaAdat::find($banjarAdatBaru->desa_adat_id);
            $tanggal_kawin = Helper::convert_date_to_locale_id($maperas->tanggal_maperas);

            $kontenNotif = "Terdapat ajuan maperas dari Banjar Adat ".$banjarAdatBaru->nama_banjar_adat." Desa Adat ".$desaAdatBaru->desadat_nama." pada tanggal ".$tanggal_kawin.".";


            $userNotif->sendNotifPendataan(
                                    $kontenNotif,
                                    null,
                                    "Ajuan Maperas baru.",
                                    $banjarAdatLama->id,
                                    $maperas->id,
                                    3,
                                );


            if ($maperas) {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')->where('id', $maperas->id)->first(),
                    'message' => 'data maperas sukses'
                ], 200);
            }
            else {
                return response()->json([
                    'statusCode' => 500,
                    'status' => false,
                    'data' => null,
                    'message' => 'data maperas fail'
                ], 500);
            }
    }

    public function editBedaBanjar(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
        //     'krama_mipil_lama' => 'required',
        //     'krama_mipil_baru' => 'required',
        //     'anak' => 'required',
        //     'ayah_baru' => 'required',
        //     'ibu_baru' => 'required',
        //     'nama_pemuput' => 'required',
        //     'file_bukti_maperas' => 'required',
        //     'tanggal_maperas' => 'required',
        //     'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
        //     'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',
        // ],[
        //     'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
        //     'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
        //     'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
        //     'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
        //     'anak.required' => "Anak wajib dipilih",
        //     'ayah_baru.required' => "Ayah Baru wajib dipilih",
        //     'ibu_baru.required' => "Ibu Baru wajib dipilih",
        //     'nama_pemuput.required' => "Nama Pemuput wajib diisi",
        //     'file_bukti_maperas.required' => "File Bukti Maperas wajib diunggah",
        //     'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
        //     'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
        //     'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        //     'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        /**
         * form data
         * maperas_json -> json data maperas
         * file_bukti_maperas -> file
         * file_akta_pengangkatan_anak -> file
         */

        $maperasObject = json_decode($request->maperas_json);

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($maperasObject->krama_mipil_lama_id);
        $krama_mipil_baru = KramaMipil::find($maperasObject->krama_mipil_baru_id);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($maperasObject->cacah_krama_mipil_lama_id);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);

        //GET ORTU LAMA
        $ayah_lama = Penduduk::find($penduduk_anak->ayah_kandung_id);
        if($ayah_lama){
            $ayah_lama = CacahKramaMipil::where('penduduk_id', $ayah_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }
        $ibu_lama = Penduduk::find($penduduk_anak->ibu_kandung_id);
        if($ibu_lama){
            $ibu_lama = CacahKramaMipil::where('penduduk_id', $ibu_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }

        //GET ORTU BARU
        $ayah_baru = CacahKramaMipil::find($maperasObject->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperasObject->ibu_baru_id);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$maperasObject->nomor_maperas);

        $maperas = Maperas::where('id', $maperasObject->id)->first();
            $maperas->jenis_maperas = 'beda_banjar_adat';
            $maperas->nomor_maperas = $maperasObject->nomor_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $maperasObject->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $maperasObject->krama_mipil_lama_id;
            $maperas->krama_mipil_baru_id = $maperasObject->krama_mipil_baru_id;
            $maperas->cacah_krama_mipil_lama_id = $maperasObject->cacah_krama_mipil_lama_id;
            $maperas->cacah_krama_mipil_baru_id = $maperasObject->cacah_krama_mipil_baru_id;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $maperasObject->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $maperasObject->ayah_baru_id;
            $maperas->ibu_baru_id = $maperasObject->ibu_baru_id;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($maperasObject->tanggal_maperas));
            $maperas->nama_pemuput = $maperasObject->nama_pemuput;
            $maperas->status_maperas = '0';
            if($request->file('file_bukti_maperas')!=""){
                $file = $request->file('file_bukti_maperas');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $maperas->file_bukti_maperas = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_akta_pengangkatan_anak')!=""){
                $file = $request->file('file_akta_pengangkatan_anak');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $maperas->save();

            /**
             * create notif baru
             */

            if ($maperas) {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')->where('id', $maperas->id)->first(),
                    'message' => 'data maperas sukses'
                ], 200);
            }
            else {
                return response()->json([
                    'statusCode' => 500,
                    'status' => false,
                    'data' => null,
                    'message' => 'data maperas fail'
                ], 500);
            }
    }

    public function tolakBedaBanjar(Request $request){
        // $validator = Validator::make($request->all(), [
        //     'alasan_penolakan' => 'required',
        // ],[
        //     'alasan_penolakan.required' => "Alasan wajib diisi",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        $maperas = Maperas::find($request->query("maperas"));
        $maperas->alasan_penolakan = $request->alasan_penolakan;
        $maperas->status_maperas = '2';
        $maperas->update();

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();
        $notifikasi->notif_tolak_maperas_beda_banjar_adat($maperas->id);

        $userNotif = new User();
        error_log('ngirim notif perkawinan');

        $banjarAdatLama = BanjarAdat::find($maperas->banjar_adat_lama_id);
        $desaAdatLama = DesaAdat::find($banjarAdatLama->desa_adat_id);
        $banjarAdatBaru = BanjarAdat::find($maperas->banjar_adat_baru_id);
        $desaAdatBaru = DesaAdat::find($banjarAdatBaru->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($maperas->tanggal_maperas);

        $kontenNotif = "Maperas dari Banjar Adat ".$banjarAdatLama->nama_banjar_adat." Desa Adat ".$desaAdatLama->desadat_nama." pada tanggal ".$tanggal_kawin." belum dapat dikonfirmasi.";

        $userNotif->sendNotifPendataan(
                                $kontenNotif,
                                null,
                                "Penolakan Ajuan Maperas.",
                                $banjarAdatBaru->id,
                                $maperas->id,
                                3,
                            );

        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $maperas,
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    // acc maperas beda banjar
    public function konfirmasiKeluarBanjar(Request $req){
        //UPDATE MAPERAS
        $maperas = Maperas::find($req->query("maperas"));
        $maperas->status_maperas = '1';
        $maperas->update();

        //NONAKTIFKAN CACAH
        $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_lama_id);
        $anak->status = '0';
        $anak->tanggal_nonaktif = date("Y-m-d", strtotime($maperas->tanggal_maperas));
        $anak->alasan_keluar = 'Maperas (Keluar Banjar Adat)';
        $anak->update();

        //KELUARKAN DARI KELUARGA
        $anak_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $anak->id)->where('status', '1')->first();
        $krama_mipil_lama = KramaMipil::find($anak_sebagai_anggota->krama_mipil_id);
        $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->where('status', '1')->get();

        //COPY DATA KK PRADANA
        $krama_mipil_baru = new KramaMipil();
        $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
        $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
        $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
        $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
        $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
        $krama_mipil_baru->status = '1';
        $krama_mipil_baru->alasan_perubahan = 'Pengurangan Anggota Keluarga (Maperas)';
        $krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
        $krama_mipil_baru->save();

        //COPY ANGGOTA LAMA
        foreach($anggota_krama_mipil_lama as $anggota_lama){
            if($anggota_lama->cacah_krama_mipil_id != $anak->id){
                $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                $anggota_krama_mipil_baru->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                $anggota_krama_mipil_baru->status_hubungan = $anggota_lama->status_hubungan;
                $anggota_krama_mipil_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                $anggota_krama_mipil_baru->status = '1';
                $anggota_krama_mipil_baru->save();
            }else{
                $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($maperas->tanggal_maperas));
                $anggota_lama->alasan_keluar = 'Maperas (Keluar Banjar Adat)';
            }
            //NONAKTIFKAN ANGGOTA LAMA
            $anggota_lama->status = '0';
            $anggota_lama->update();
        }

        //LEBUR KK LAMA
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();
        $notifikasi->notif_konfirmasi_maperas_beda_banjar_adat($maperas->id);

        $userNotif = new User();
        error_log('ngirim notif perkawinan');

        $banjarAdatLama = BanjarAdat::find($maperas->banjar_adat_lama_id);
        $desaAdatLama = DesaAdat::find($banjarAdatLama->desa_adat_id);
        $banjarAdatBaru = BanjarAdat::find($maperas->banjar_adat_baru_id);
        $desaAdatBaru = DesaAdat::find($banjarAdatBaru->desa_adat_id);
        $tanggal_kawin = Helper::convert_date_to_locale_id($maperas->tanggal_maperas);
        $kontenNotif = "Maperas dari Banjar Adat ".$banjarAdatLama->nama_banjar_adat." Desa Adat ".$desaAdatLama->desadat_nama." pada tanggal ".$tanggal_kawin." telah dikonfirmasi.";

        error_log('udah kebawah gan');
        $userNotif->sendNotifPendataan(
                                $kontenNotif,
                                null,
                                "Ajuan Maperas telah dikonfirmasi.",
                                $banjarAdatBaru->id,
                                $maperas->id,
                                3,
                            );


        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $maperas,
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    // sah beda banjar
    public function konfirmasiMasukBanjar(Request $req){
        $maperas = Maperas::find($req->query("maperas"));

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($maperas->krama_mipil_lama_id);
        $krama_mipil_baru = KramaMipil::find($maperas->krama_mipil_baru_id);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($maperas->cacah_krama_mipil_lama_id);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);

        //GET ORTU LAMA
        $ayah_lama = Penduduk::find($penduduk_anak->ayah_kandung_id);
        if($ayah_lama){
            $ayah_lama = CacahKramaMipil::where('penduduk_id', $ayah_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }
        $ibu_lama = Penduduk::find($penduduk_anak->ibu_kandung_id);
        if($ibu_lama){
            $ibu_lama = CacahKramaMipil::where('penduduk_id', $ibu_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }

        //GET ORTU BARU
        $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);

        //NOMOR CACAH KRAMA
        $banjar_adat = $banjar_adat_baru;
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat->id)->pluck('penduduk_id')->toArray();
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk_anak->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $nomor_cacah_krama_mipil = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'01'.Carbon::parse($penduduk_anak->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil.$jumlah_penduduk_tanggal_sama;
        }

        //BENTUK CACAH BARU ANAK
        $cacah_krama_mipil_baru = new CacahKramaMipil();
        $cacah_krama_mipil_baru->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil_baru->banjar_adat_id = $banjar_adat_baru->id;
        $cacah_krama_mipil_baru->tempekan_id = $ayah_baru->tempekan_id;
        $cacah_krama_mipil_baru->penduduk_id = $penduduk_anak->id;
        $cacah_krama_mipil_baru->tanggal_registrasi = date("Y-m-d", strtotime($maperas->tanggal_maperas));
        $cacah_krama_mipil_baru->jenis_kependudukan = $ayah_baru->jenis_kependudukan;
        $cacah_krama_mipil_baru->status = '1';
        if($ayah_baru->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil_baru->banjar_dinas_id = $ayah_baru->banjar_dinas_id;
        }
        $cacah_krama_mipil_baru->save();

        //COPY DATA KRAMA MIPIL BARU
        $krama_mipil_baru_copy = new KramaMipil();
        $krama_mipil_baru_copy->nomor_krama_mipil = $krama_mipil_baru->nomor_krama_mipil;
        $krama_mipil_baru_copy->banjar_adat_id = $krama_mipil_baru->banjar_adat_id;
        $krama_mipil_baru_copy->cacah_krama_mipil_id = $krama_mipil_baru->cacah_krama_mipil_id;
        $krama_mipil_baru_copy->kedudukan_krama_mipil = $krama_mipil_baru->kedudukan_krama_mipil;
        $krama_mipil_baru_copy->jenis_krama_mipil = $krama_mipil_baru->jenis_krama_mipil;
        $krama_mipil_baru_copy->status = '1';
        $krama_mipil_baru_copy->alasan_perubahan = 'Penambahan Anggota Keluarga (Maperas)';
        $krama_mipil_baru_copy->tanggal_registrasi = $krama_mipil_baru->tanggal_registrasi;
        $krama_mipil_baru_copy->save();

        $anggota_krama_mipil_baru = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_baru->id)->where('status', '1')->get();
        //COPY DATA ANGGOTA LAMA
        foreach($anggota_krama_mipil_baru as $anggota_baru){
            $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
            $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_baru_copy->id;
            $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $anggota_baru->cacah_krama_mipil_id;
            $anggota_krama_mipil_lama_copy->status_hubungan = $anggota_baru->status_hubungan;
            $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($anggota_baru->tanggal_registrasi));
            $anggota_krama_mipil_lama_copy->status = '1';
            $anggota_krama_mipil_lama_copy->save();
            //NONAKTIFKAN ANGGOTA LAMA
            $anggota_baru->status = '0';
            $anggota_baru->update();
        }

        //LEBUR KK LAMA
        $krama_mipil_baru->status = '0';
        $krama_mipil_baru->update();

        //MASUKKAN ANAK KE KRAMA MIPIL BARU
        $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
        $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_baru_copy->id;
        $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $cacah_krama_mipil_baru->id;
        if($ayah_baru->id == $krama_mipil_baru->cacah_krama_mipil->id){
            $anggota_krama_mipil_lama_copy->status_hubungan = 'anak';
        }else{
            $anggota_krama_mipil_lama_copy->status_hubungan = 'famili_lain';
        }
        $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($maperas->tanggal_maperas));
        $anggota_krama_mipil_lama_copy->status = '1';
        $anggota_krama_mipil_lama_copy->save();

        //UBAH ORANG TUA PENDUDUK ANAK
        $penduduk_anak->ayah_kandung_id = $ayah_baru->penduduk->id;
        $penduduk_anak->ibu_kandung_id = $ibu_baru->penduduk->id;

        //UBAH ALAMAT ANAK
        $penduduk_anak->alamat = $ayah_baru->penduduk->alamat;
        $penduduk_anak->koordinat_alamat = $ayah_baru->penduduk->koordinat_alamat;
        $penduduk_anak->desa_id = $ayah_baru->penduduk->desa_id;
        $penduduk_anak->update();

        //UBAH MAPERAS
        $maperas->krama_mipil_baru_id = $krama_mipil_baru_copy->id;
        $maperas->cacah_krama_mipil_baru_id = $cacah_krama_mipil_baru->id;
        $maperas->status_maperas = '3';
        $maperas->update();

        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $maperas,
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    public function destroy_satu_banjar(Request $request) {
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
        $maperas = Maperas::find($request->query('maperas'));

        $maperas->delete();
        return response()->json([
            'statusCode' => 200,
            'status' => true,
            'data' => null,
            'sisi_pradana' => null,
            'message' => 'data maperas sukses'
        ], 200);
    }

    public function storeCampuranMasuk(Request $request) {
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
        //     'krama_mipil_baru' => 'required',
        //     'ayah_baru' => 'required',
        //     'ibu_baru' => 'required',
        //     'nama_pemuput' => 'required',
        //     'file_bukti_maperas' => 'required',
        //     'tanggal_maperas' => 'required',
        //     'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
        //     'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',

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
        //     'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
        //     'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
        //     'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
        //     'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
        //     'anak.required' => "Anak wajib dipilih",
        //     'ayah_baru.required' => "Ayah Baru wajib dipilih",
        //     'ibu_baru.required' => "Ibu Baru wajib dipilih",
        //     'nama_pemuput.required' => "Nama Pemuput wajib diisi",
        //     'file_bukti_maperas.required' => "Bukti Maperas wajib diunggah",
        //     'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
        //     'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
        //     'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        //     'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
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
        //     'alamat.required' => "Alamat Asal wajib diisi",
        //     'provinsi.required' => "Provinsi Asal wajib dipilih",
        //     'kabupaten.required' => "Kabupaten Asal wajib dipilih",
        //     'kecamatan.required' => "Kecamatan Asal wajib dipilih",
        //     'desa.required' => "Desa/Kelurahan Asal wajib dipilih",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        $anakObject = json_decode($request->anak_json);
        $maperasObject = json_decode($request->maperas_json);

        //GET KRAMA MIPIL
        $krama_mipil_baru = KramaMipil::find($maperasObject->krama_mipil_baru_id);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ORTU BARU
        $ayah_baru = CacahKramaMipil::find($maperasObject->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperasObject->ibu_baru_id);
        $penduduk_ayah_baru = Penduduk::find($ayah_baru->penduduk_id);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$maperasObject->nomor_maperas);

        //INSERT PENDUDUK
        $penduduk = new Penduduk();
        $penduduk->nik = $anakObject->penduduk->nik;
        $penduduk->gelar_depan =  $anakObject->penduduk->gelar_depan;
        $penduduk->nama =  $anakObject->penduduk->nama;
        $penduduk->gelar_belakang =  $anakObject->penduduk->gelar_belakang;
        $penduduk->nama_alias =  $anakObject->penduduk->nama_alias;
        $penduduk->tempat_lahir =  $anakObject->penduduk->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($anakObject->penduduk->tanggal_lahir));
        $penduduk->agama = 'hindu';
        $penduduk->jenis_kelamin =  $anakObject->penduduk->jenis_kelamin;
        $penduduk->golongan_darah =  $anakObject->penduduk->golongan_darah;
        $penduduk->profesi_id =  $anakObject->penduduk->pekerjaan->id;
        $penduduk->pendidikan_id =  $anakObject->penduduk->pendidikan->id;
        $penduduk->telepon =  $anakObject->penduduk->telepon;
        if($request->foto != ''){
            $file = $request->file('foto');
            $filename = uniqid().'.png';
            $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
            $path = $fileLocation."/".$filename;
            $penduduk->foto = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $penduduk->koordinat_alamat = $penduduk_ayah_baru->koordinat_alamat;
        $penduduk->alamat = $penduduk_ayah_baru->alamat;
        $penduduk->desa_id = $penduduk_ayah_baru->desa_id;
        $penduduk->ayah_kandung_id = $ayah_baru->penduduk->id;
        $penduduk->ibu_kandung_id = $ibu_baru->penduduk->id;
        $penduduk->save();

        //NOMOR CACAH KRAMA
        $banjar_adat_id = Auth::user()->prajuru_banjar_adat->banjar_adat_id;
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

        //INSERT CACAH KRAMA MIPIL
        $cacah_krama_mipil = new CacahKramaMipil();
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_mipil->tempekan_id = $ayah_baru->tempekan_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($maperasObject->tanggal_maperas));
        $cacah_krama_mipil->jenis_kependudukan = $ayah_baru->jenis_kependudukan;
        $cacah_krama_mipil->status = '0';
        if($ayah_baru->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $ayah_baru->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();

        $maperas = new Maperas();
        $maperas->jenis_maperas = 'campuran_masuk';
        $maperas->nomor_maperas = $maperasObject->nomor_maperas;
        $maperas->nomor_akta_pengangkatan_anak = $maperasObject->nomor_akta_pengangkatan_anak;
        $maperas->krama_mipil_baru_id = $maperasObject->krama_mipil_baru_id;
        $maperas->cacah_krama_mipil_baru_id = $cacah_krama_mipil->id;
        $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
        $maperas->desa_adat_baru_id = $desa_adat_baru->id;
        $maperas->ayah_baru_id = $maperasObject->ayah_baru_id;
        $maperas->ibu_baru_id = $maperasObject->ibu_baru_id;
        $maperas->tanggal_maperas = date("Y-m-d", strtotime($maperasObject->tanggal_maperas));
        $maperas->nama_pemuput = $maperasObject->nama_pemuput;
        $maperas->keterangan = $maperasObject->keterangan;
        $maperas->status_maperas = '0';
        if($request->file('file_bukti_maperas')!=""){
            $file = $request->file('file_bukti_maperas');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_bukti_maperas = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_pengangkatan_anak')!=""){
            $file = $request->file('file_akta_pengangkatan_anak');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        //INSERT ASAL
        $maperas->nik_ayah_lama = $maperasObject->nik_ayah_lama;
        $maperas->nik_ibu_lama = $maperasObject->nik_ibu_lama;
        $maperas->nama_ayah_lama = $maperasObject->nama_ayah_lama;
        $maperas->nama_ibu_lama = $maperasObject->nama_ibu_lama;
        $maperas->alamat_asal = $maperasObject->alamat_asal;
        $maperas->desa_asal_id = $maperasObject->desa_asal_id;
        $maperas->agama_lama = $maperasObject->agama_lama;
        if($request->file('file_sudhi_wadhani')!=""){
            $file = $request->file('file_sudhi_wadhani');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_sudhi_wadhani';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_sudhi_wadhani = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $maperas->save();

        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Maperas::with('cacah_krama_mipil_baru.penduduk')->where('id', $maperas->id)->first(),
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    public function sahCampuranMasuk(Request $req) {

        $maperas = Maperas::where('id', $req->query("maperas"))->first();
        $cacah_krama_mipil = CacahKramaMipil::where('id', $maperas->cacah_krama_mipil_baru_id)->first();
        $krama_mipil_baru = KramaMipil::where('id', $maperas->krama_mipil_baru_id)->first();

                //GET ORTU BARU
                $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
                $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);

        //SAHKAN MAPERAS
        $maperas->status_maperas = '3';

        //AKTIFKAN CACAH
        $cacah_krama_mipil->status = '1';
        $cacah_krama_mipil->update();

        //COPY DATA KRAMA MIPIL BARU
        $anggota_krama_mipil_baru = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_baru->id)->where('status', '1')->get();
        $krama_mipil_baru_copy = new KramaMipil();
        $krama_mipil_baru_copy->nomor_krama_mipil = $krama_mipil_baru->nomor_krama_mipil;
        $krama_mipil_baru_copy->banjar_adat_id = $krama_mipil_baru->banjar_adat_id;
        $krama_mipil_baru_copy->cacah_krama_mipil_id = $krama_mipil_baru->cacah_krama_mipil_id;
        $krama_mipil_baru_copy->kedudukan_krama_mipil = $krama_mipil_baru->kedudukan_krama_mipil;
        $krama_mipil_baru_copy->jenis_krama_mipil = $krama_mipil_baru->jenis_krama_mipil;
        $krama_mipil_baru_copy->status = '1';
        $krama_mipil_baru_copy->alasan_perubahan = 'Penambahan Anggota Keluarga (Maperas)';
        $krama_mipil_baru_copy->tanggal_registrasi = $krama_mipil_baru->tanggal_registrasi;
        $krama_mipil_baru_copy->save();

        //COPY DATA ANGGOTA LAMA
        foreach($anggota_krama_mipil_baru as $anggota_baru){
            $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
            $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_baru_copy->id;
            $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $anggota_baru->cacah_krama_mipil_id;
            $anggota_krama_mipil_lama_copy->status_hubungan = $anggota_baru->status_hubungan;
            $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($anggota_baru->tanggal_registrasi));
            $anggota_krama_mipil_lama_copy->status = '1';
            $anggota_krama_mipil_lama_copy->save();
            //NONAKTIFKAN ANGGOTA LAMA
            $anggota_baru->status = '0';
            $anggota_baru->update();
        }

        //LEBUR KK LAMA
        $krama_mipil_baru->status = '0';
        $krama_mipil_baru->update();

        //MASUKKAN ANAK KE KRAMA MIPIL BARU
        $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
        $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_baru_copy->id;
        $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        if($ayah_baru->id == $krama_mipil_baru->cacah_krama_mipil->id){
            $anggota_krama_mipil_lama_copy->status_hubungan = 'anak';
        }else{
            $anggota_krama_mipil_lama_copy->status_hubungan = 'famili_lain';
        }
        $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($maperas->tanggal_maperas));
        $anggota_krama_mipil_lama_copy->status = '1';
        $anggota_krama_mipil_lama_copy->save();

        //UBAH MAPERAS
        $maperas->krama_mipil_baru_id = $krama_mipil_baru_copy->id;
        $maperas->update();

        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => $maperas,
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }


    public function storeCampuranKeluar(Request $request) {
        // $validator = Validator::make($request->all(), [
        //     'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
        //     'krama_mipil_lama' => 'required',
        //     'anak' => 'required',
        //     'file_bukti_maperas' => 'required',
        //     'tanggal_maperas' => 'required',
        //     'nik_ayah' => 'required',
        //     'nik_ibu' => 'required',
        //     'nama_ayah' => 'required',
        //     'nama_ibu' => 'required',
        //     'agama' => 'required',
        //     'alamat' => 'required',
        //     'desa_asal' => 'required',
        //     'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
        //     'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',
        // ],[
        //     'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
        //     'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
        //     'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
        //     'anak.required' => "Anak wajib dipilih",
        //     'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
        //     'nik_ayah.required' => "NIK Ayah Baru wajib diisi",
        //     'nik_ibu.required' => "NIK Ibu Baru wajib diisi",
        //     'nama_ayah.required' => "Nama Ayah Baru wajib diisi",
        //     'nama_ibu.required' => "Nama Ibu Baru wajib diisi",
        //     'alamat.required' => "Alamat Asal wajib diisi",
        //     'agama.required' => "Agama wajib dipilih",
        //     'desa_asal.required' => "Desa/Kelurahan Asal wajib dipilih",
        //     'file_bukti_maperas.required' => "Bukti Maperas wajib diunggah",
        //     'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
        //     'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
        //     'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        //     'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
        // ]);

        // if($validator->fails()){
        //     return back()->withInput()->withErrors($validator);
        // }

        $maperasObject = json_decode($request->maperas_json);

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($maperasObject->krama_mipil_lama_id);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($maperasObject->cacah_krama_mipil_lama_id);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);

        //GET ORTU LAMA
        $ayah_lama = Penduduk::find($penduduk_anak->ayah_kandung_id);
        if($ayah_lama){
            $ayah_lama = CacahKramaMipil::where('penduduk_id', $ayah_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }
        $ibu_lama = Penduduk::find($penduduk_anak->ibu_kandung_id);
        if($ibu_lama){
            $ibu_lama = CacahKramaMipil::where('penduduk_id', $ibu_lama->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_lama->id)->first();
        }

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$maperasObject->nomor_maperas);

        $maperas = new Maperas();
        $maperas->jenis_maperas = 'campuran_keluar';
        $maperas->nomor_maperas = $maperasObject->nomor_maperas;
        $maperas->nomor_akta_pengangkatan_anak = $maperasObject->nomor_akta_pengangkatan_anak;
        $maperas->krama_mipil_lama_id = $maperasObject->krama_mipil_lama_id;
        $maperas->cacah_krama_mipil_lama_id = $maperasObject->cacah_krama_mipil_lama_id;
        $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
        $maperas->desa_adat_lama_id = $desa_adat_lama->id;
        $maperas->keterangan = $maperasObject->keterangan;
        if($ayah_lama){
            $maperas->ayah_lama_id = $ayah_lama->id;
        }
        if($ibu_lama){
            $maperas->ibu_lama_id = $ibu_lama->id;
        }
        $maperas->tanggal_maperas = date("Y-m-d", strtotime($maperasObject->tanggal_maperas));
        $maperas->status_maperas = '0';
        if($request->file('file_bukti_maperas')!=""){
            $file = $request->file('file_bukti_maperas');
            $fileLocation = '/file/'.$desa_adat_lama->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_bukti_maperas = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_pengangkatan_anak')!=""){
            $file = $request->file('file_akta_pengangkatan_anak');
            $fileLocation = '/file/'.$desa_adat_lama->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        //DATA ORANG TUA BARU
        $maperas->nik_ayah_baru = $maperasObject->nik_ayah_baru;
        $maperas->nik_ibu_baru = $maperasObject->nik_ibu_baru;
        $maperas->nama_ayah_baru = $maperasObject->nama_ayah_baru;
        $maperas->nama_ibu_baru = $maperasObject->nama_ibu_baru;
        $maperas->agama_baru = $maperasObject->agama_baru;
        $maperas->alamat_asal = $maperasObject->alamat_asal;
        $maperas->desa_asal_id = $maperasObject->desa_asal_id;
        $maperas->save();


        if ($maperas) {
            return response()->json([
                'statusCode' => 200,
                'status' => true,
                'data' => Maperas::where('id', $maperas->id)->with('cacah_krama_mipil_lama.penduduk')->first(),
                'message' => 'data maperas sukses'
            ], 200);
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data maperas fail'
            ], 500);
        }
    }

    public function sahCampuranKeluar(Request $request) {

        $maperas = Maperas::where('id', $request->query("maperas"))->first();
        $cacah_krama_mipil_lama = CacahKramaMipil::where('id', $maperas->cacah_krama_mipil_lama_id)->first();
        $krama_mipil_lama = KramaMipil::where('id', $maperas->krama_mipil_lama_id)->first();

        $maperas->status_maperas = '3';
            $maperas->update();

            //NONAKTIFKAN CACAH
            $cacah_krama_mipil_lama->status = '0';
            $cacah_krama_mipil_lama->tanggal_nonaktif = date("Y-m-d", strtotime($maperas->tanggal_maperas));
            $cacah_krama_mipil_lama->alasan_keluar = 'Maperas (Campuran Keluar)';
            $cacah_krama_mipil_lama->update();

            //COPY DATA KRAMA MIPIL LAMA & KELUARKAN ANAK
            $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->where('status', '1')->get();
            $krama_mipil_lama_copy = new KramaMipil();
            $krama_mipil_lama_copy->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
            $krama_mipil_lama_copy->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
            $krama_mipil_lama_copy->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
            $krama_mipil_lama_copy->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
            $krama_mipil_lama_copy->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
            $krama_mipil_lama_copy->status = '1';
            $krama_mipil_lama_copy->alasan_perubahan = 'Pengurangan Anggota Keluarga (Maperas)';
            $krama_mipil_lama_copy->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
            $krama_mipil_lama_copy->save();

            //COPY DATA ANGGOTA LAMA
            foreach($anggota_krama_mipil_lama as $anggota_lama){
                if($anggota_lama->cacah_krama_mipil_id != $cacah_krama_mipil_lama->id){
                    $anggota_krama_mipil_lama_copy = new AnggotaKramaMipil();
                    $anggota_krama_mipil_lama_copy->krama_mipil_id = $krama_mipil_lama_copy->id;
                    $anggota_krama_mipil_lama_copy->cacah_krama_mipil_id = $anggota_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_lama_copy->status_hubungan = $anggota_lama->status_hubungan;
                    $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama->tanggal_registrasi));
                    $anggota_krama_mipil_lama_copy->status = '1';
                    $anggota_krama_mipil_lama_copy->save();
                }else{
                    $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($maperas->tanggal_maperas));
                    $anggota_lama->alasan_keluar = 'Maperas (Campuran Keluar)';
                }
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }

            //LEBUR KK LAMA
            $krama_mipil_lama->status = '0';
            $krama_mipil_lama->update();


            if ($maperas) {
                return response()->json([
                    'statusCode' => 200,
                    'status' => true,
                    'data' => $maperas,
                    'message' => 'data maperas sukses'
                ], 200);
            }
            else {
                return response()->json([
                    'statusCode' => 500,
                    'status' => false,
                    'data' => null,
                    'message' => 'data maperas fail'
                ], 500);
            }
    }
}
