<?php

namespace App\Http\Controllers\KramaController\Kematian;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\DesaAdat;
use App\Models\KramaMipil;
use App\Models\Kelahiran;
use App\Models\KeluargaKrama;
use App\Models\KramaTamiu;
use App\Models\Tamiu;
use App\Models\Tempekan;
use App\Models\KelahiranAjuan;
use App\Models\Kematian;
use App\Models\KematianAjuan;
use App\Models\Notifikasi;
use App\Models\Penduduk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class KematianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $penduduk_id = Auth::user()->user->penduduk_id;
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk_id)->first();
        $banjar_adat_id = $cacahKramaMipil->banjar_adat_id;
        $banjarAdat = BanjarAdat::where('id', $banjar_adat_id)->first();

        if ($cacahKramaMipil) {
            /**
             * Cari krama mipil (kepala keluarga) dari id cacah krama klo found return krama mipilnya (keluarga)
             * klo not found, cari di anggota krama mipil, klo masih notfound abort, klo found return krama mipilnya (keluarga)
             */
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
            if (!$kramaMipil) {
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', '1')->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->first();
                }
            }
        }
        /**
         * get semua id krama mipil yang nomor krama mipilnya dari krama mipil di atas
         */
        $kramaMipilId = KramaMipil::where('nomor_krama_mipil', $kramaMipil->nomor_krama_mipil)->pluck('id')->toArray();
        /**
         * setelah dapet semua id krama mipil, baru get semua anggota dari semua id krama mipil yg cocok
         * (tujuan di buat gini, karena di tb_kematian nggak ada id krama mipil mana asal yg meninggal, jadi untuk nyari yang meninggal ini waktu di
         * krama mipil mana, harus nge get semua id krama mipil tempat waktu yg meninggal ini)
         */
        $anggotaKramaMipilId = AnggotaKramaMipil::whereIn('krama_mipil_id', $kramaMipilId)->pluck('cacah_krama_mipil_id')->toArray();

        // $arrayIdCacah = $anggotaKramaMipil->pluck('cacah_krama_mipil_id')->toArray();
        // array_push($arrayIdCacah, $kramaMipil->cacah_krama_mipil_id);

        $kematian = Kematian::whereIn('cacah_krama_mipil_id', $anggotaKramaMipilId)
                                ->where('status' ,1)
                                ->with('cacah_krama_mipil.penduduk', 'kematian_ajuan')
                                ->orderBy('created_at', 'desc')->get();
        
        $kematian_ajuan = KematianAjuan::whereIn('cacah_krama_mipil_id', $anggotaKramaMipilId)
                                ->with('cacah_krama_mipil.penduduk', 'kematian')
                                ->orderBy('created_at', 'desc')->get();

        return view('pages.krama.kematian.kematian', compact('kematian_ajuan', 'kematian'));
    }

    public function create_ajuan()
    {
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
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
            if ($kramaMipil) {
                /**
                 * if krama mipil, return cacah anggota krama mipilnya
                 */
                $anggotaCacahArray = AnggotaKramaMipil::where('krama_mipil_id', $kramaMipil->id)->where('status', 1)->pluck('cacah_krama_mipil_id')->toArray();
                $kramas = CacahKramaMipil::with('penduduk')->whereIn('id', $anggotaCacahArray)->get();
                $kramas = Helper::generate_nama_collection_cacah_krama_mipil($kramas);
                return view('pages.krama.kematian.create_ajuan', compact('kramas'));
            }
            else {
                /**
                 * Klo bukan krama mipilnya, return cacah krama mipil dari krama mipilnya + anggota
                 * dari krama mipil yang nggak login
                 */
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->first();
                    $anggotaCacahArray = AnggotaKramaMipil::where('krama_mipil_id', $kramaMipil->id)->where('status', 1)->pluck('cacah_krama_mipil_id')->toArray();
                    $kramas = CacahKramaMipil::with('penduduk')->where('id', $kramaMipil->cacah_krama_mipil_id)->where('status', 1)->get();
                    /** Krama 2 di pake buat nyimpen collection yang isi anggota keluarga kramanya tanpa cacah user yg login */
                    $krama2 = CacahKramaMipil::with('penduduk')->whereIn('id', $anggotaCacahArray)
                                            ->whereNotIn('id', [$cacahKramaMipil->id])->get();
                    $finalKrama = $kramas->merge($krama2);
                    $kramas = $finalKrama;
                    $kramas = Helper::generate_nama_collection_cacah_krama_mipil($kramas);
                    return view('pages.krama.kematian.create_ajuan', compact('kramas'));
                }
                else {
                    return response()->json([
                        'statusCode' => 500,
                        'status' => false,
                        'data' => null,
                        'message' => 'data anggota krama mipil not found'
                    ], 200);
                }
            }
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data cacah krama mipil not found'
            ], 200);
        }
    }

    public function create_ajuan_ulang($id){
        $kematian = KematianAjuan::find($id);
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
            $kramaMipil = KramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
            if ($kramaMipil) {
                /**
                 * if krama mipil, return cacah anggota krama mipilnya
                 */
                $anggotaCacahArray = AnggotaKramaMipil::where('krama_mipil_id', $kramaMipil->id)->where('status', 1)->pluck('cacah_krama_mipil_id')->toArray();
                $kramas = CacahKramaMipil::with('penduduk')->whereIn('id', $anggotaCacahArray)->get();
                $kramas = Helper::generate_nama_collection_cacah_krama_mipil($kramas);
                return view('pages.krama.kematian.create_ajuan_ulang', compact('kramas', 'kematian'));
            }
            else {
                /**
                 * Klo bukan krama mipilnya, return cacah krama mipil dari krama mipilnya + anggota
                 * dari krama mipil yang nggak login
                 */
                $anggotaKramaMipil = AnggotaKramaMipil::where('cacah_krama_mipil_id', $cacahKramaMipil->id)->where('status', 1)->first();
                if ($anggotaKramaMipil) {
                    $kramaMipil = KramaMipil::where('id', $anggotaKramaMipil->krama_mipil_id)->first();
                    $anggotaCacahArray = AnggotaKramaMipil::where('krama_mipil_id', $kramaMipil->id)->where('status', 1)->pluck('cacah_krama_mipil_id')->toArray();
                    $kramas = CacahKramaMipil::with('penduduk')->where('id', $kramaMipil->cacah_krama_mipil_id)->where('status', 1)->get();
                    /** Krama 2 di pake buat nyimpen collection yang isi anggota keluarga kramanya tanpa cacah user yg login */
                    $krama2 = CacahKramaMipil::with('penduduk')->whereIn('id', $anggotaCacahArray)
                                            ->whereNotIn('id', [$cacahKramaMipil->id])->get();
                    $finalKrama = $kramas->merge($krama2);
                    $kramas = $finalKrama;
                    $kramas = Helper::generate_nama_collection_cacah_krama_mipil($kramas);
                    return view('pages.krama.kematian.create_ajuan_ulang', compact('kramas', 'kematian'));
                }
                else {
                    return response()->json([
                        'statusCode' => 500,
                        'status' => false,
                        'data' => null,
                        'message' => 'data anggota krama mipil not found'
                    ], 200);
                }
            }
        }
        else {
            return response()->json([
                'statusCode' => 500,
                'status' => false,
                'data' => null,
                'message' => 'data cacah krama mipil not found'
            ], 200);
        }
    }

    public function store_ajuan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_kematian' => 'required',
            'cacah_krama_mipil' => 'required',
            'penyebab_kematian' => 'required',
            'nomor_suket_kematian' => 'required|unique:tb_kematian|max:50',
            'nomor_akta_kematian' => 'unique:tb_kematian|nullable|max:21',
            'file_suket_kematian' => 'required',
        ],[
            'tanggal_kematian.required' => "Tanggal Kematian wajib diisi",
            'cacah_krama_mipil.required' => "Cacah Krama wajib dipilih",
            'penyebab_kematian.required' => "Penyebab Kematian wajib diisi",
            'nomor_suket_kematian.required' => "Nomor Surat Keterangan wajib diisi",
            'nomor_suket_kematian.unique' => "Nomor Surat Keterangan telah terdaftar",
            'nomor_akta_kematian.unique' => "Nomor Akta Kematian telah terdaftar",
            'nomor_suket_kematian.max' => "Nomor Surat Keterangan maksimal 50 karakter",
            'nomor_akta_kematian.max' => "Nomor Akta Kematian maksimal 21 karakter",
            'file_suket_kematian.required' => "File Surat Keterangan wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        /**
         * Fungsi untuk nge create pengajuan kematian baru
         * form data:
         * nomor_akta_kematian
         * nomor_suket_kematian
         * cacah_krama_mipil (id)
         * tanggal_kematian (2000-12-30)
         * penyebab_kematian
         * file_akta_kematian
         * file_suket_kematian
         *
         * Return: data kematian
         */
        $penduduk_id = Auth::user()->user->penduduk_id;
        $cacahKramaMipil = CacahKramaMipil::where('id', $request->cacah_krama_mipil)->with('penduduk')->first();
        $penduduk = Penduduk::find($cacahKramaMipil->penduduk_id);
        $banjar_adat_id = $cacahKramaMipil->banjar_adat_id;
        $banjarAdat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjarAdat->desa_adat_id);

        //Validasi Tanggal
        $tanggal_lahir = $penduduk->tanggal_lahir;
        $tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
        $tanggal_sekarang = Carbon::now()->toDateString();;
        if($tanggal_kematian>$tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_kematian' => 'Tanggal kematian tidak boleh melebihi tanggal sekarang']);
        }
        if($tanggal_kematian<$tanggal_lahir){
            return back()->withInput()->withErrors(['tanggal_kematian' => 'Tanggal kematian tidak boleh lebih kecil dari tanggal lahir']);
        }

        $kematian = new KematianAjuan();
        $kematian->nomor_akta_kematian = $request->nomor_akta_kematian;
        $kematian->nomor_suket_kematian = $request->nomor_suket_kematian;
        $kematian->cacah_krama_mipil_id = $request->cacah_krama_mipil;
        $kematian->banjar_adat_id = $banjar_adat_id;
        $kematian->tanggal_kematian = date("Y-m-d", strtotime($request->tanggal_kematian));
        $kematian->penyebab_kematian = $request->penyebab_kematian;
        $kematian->keterangan = $request->keterangan;
        $kematian->status = '0';
        $kematian->save();
        if($request->hasFile('file_akta_kematian')){
            $file = $request->file('file_akta_kematian');
            $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_akta_kematian';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $kematian->file_akta_kematian = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->hasFile('file_suket_kematian')){
            $file = $request->file('file_suket_kematian');
            $fileLocation = '/file/'.$desa_adat->id.'/kematian/'.$kematian->id.'/file_suket_kematian';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $kematian->file_suket_kematian = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        $kematian->user_id = Auth::user()->id;
        $kematian->update();
        $kematian = KematianAjuan::where('id', $kematian->id)->with('cacah_krama_mipil.penduduk')->first();

        $notifikasi = new Notifikasi();
        $notifikasi->notif_kematian_ajuan(Auth::user()->id, $banjar_adat_id, $kematian->id, $cacahKramaMipil->penduduk->nama, 0);
        error_log('ngirim notif gan');
        $userNotif = new User();

        error_log('ngirim notif gan 2');

        $kontenNotif = "Terdapat ajuan data kematian baru oleh Krama ".$cacahKramaMipil->penduduk->nama." pada tanggal ".Helper::convert_date_to_locale_id($kematian->tanggal_kematian);
        $userNotif->sendNotifAjuan(
                                $kontenNotif,
                                null,
                                "Terdapat ajuan kematian baru",
                                $banjar_adat_id,
                                $kematian->id,
                                1,
                            );
        
        return redirect()->route('Kematian Home')->with(['success' => 'Data Kematian berhasil diajukan', 'is_ajuan' => true]);

    }

    public function detail_kematian($id){
        $kematian = Kematian::find($id);
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
        return view('pages.krama.kematian.detail_kematian', compact('kematian', 'cacah_krama_mipil', 'penduduk'));
    }

    public function detail_ajuan($id){
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

        $jangka_waktu = $kematian->created_at->diff(Carbon::now())->format('%a');
        return view('pages.krama.kematian.detail_ajuan', compact('kematian', 'cacah_krama_mipil', 'penduduk', 'jangka_waktu'));
    }
}