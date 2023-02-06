<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Helper\Helper;
use App\Http\Controllers\AdminController\KecamatanController;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKramaMipil;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\CacahTamiu;
use App\Models\DesaAdat;
use App\Models\DesaDinas;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kematian;
use App\Models\KramaMipil;
use App\Models\LogPenduduk;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\PrajuruDesaAdat;
use App\Models\Provinsi;
use App\Models\Tempekan;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KramaMipilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan','banjar_adat')->where('banjar_adat_id', $banjar_adat_id);

        if (isset($request->status)) {
            if($request->status == '1'){
                $krama_mipil->where('status', '1');
            }else if($request->status == '0'){
                $krama_mipil->where('status', '0')->where('tanggal_nonaktif', '!=', NULL);
            }else if($request->status == '2'){
                $krama_mipil->where('banjar_adat_id', $banjar_adat_id)->where('status', '1');
                if(isset($request->rentang_waktu)){
                    $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                        $rentang_waktu = explode(' - ', $request->rentang_waktu);
                        $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                        $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                        return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
                    });
                }
        
                if(isset($request->rentang_waktu_registrasi)){
                    $rentang_waktu = explode(' - ', $request->rentang_waktu_registrasi);
                    $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                    $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                    $krama_mipil->whereBetween('tanggal_registrasi', [$start_date, $end_date]);
                }
        
                if (isset($request->golongan_darah)) {
                    $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                        return $query->whereIn('golongan_darah', $request->golongan_darah);
                    });
                }
        
                if (isset($request->tempekan)) {
                    $krama_mipil->whereHas('cacah_krama_mipil', function ($query) use ($request) {
                        return $query->whereIn('tempekan_id', $request->tempekan);
                    });
                }
        
                if (isset($request->pekerjaan)) {
                    $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                        return $query->whereIn('profesi_id', $request->pekerjaan);
                    });
                }
        
                if (isset($request->pendidikan)) {
                    $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                        return $query->whereIn('pendidikan_id', $request->pendidikan);
                    });
                }
                $krama_mipil->orWhere(function ($query) use ($banjar_adat_id) {
                    $query->where('status', '0')
                        ->where('tanggal_nonaktif', '!=', NULL)
                        ->where('banjar_adat_id', $banjar_adat_id);
                });
            }
        }else{
            $krama_mipil->where('status', '1');
        }

        if(isset($request->rentang_waktu)){
            $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                $rentang_waktu = explode(' - ', $request->rentang_waktu);
                $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
                $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
                return $query->whereBetween('tanggal_lahir', [$start_date, $end_date]);
            });
        }

        if(isset($request->rentang_waktu_registrasi)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu_registrasi);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $krama_mipil->whereBetween('tanggal_registrasi', [$start_date, $end_date]);
        }

        if (isset($request->golongan_darah)) {
            $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                return $query->whereIn('golongan_darah', $request->golongan_darah);
            });
        }

        if (isset($request->tempekan)) {
            $krama_mipil->whereHas('cacah_krama_mipil', function ($query) use ($request) {
                return $query->whereIn('tempekan_id', $request->tempekan);
            });
        }

        if (isset($request->pekerjaan)) {
            $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                return $query->whereIn('profesi_id', $request->pekerjaan);
            });
        }

        if (isset($request->pendidikan)) {
            $krama_mipil->whereHas('cacah_krama_mipil.penduduk', function ($query) use ($request) {
                return $query->whereIn('pendidikan_id', $request->pendidikan);
            });
        }

        $krama_mipil->orderBy('tanggal_registrasi', 'DESC');
        return DataTables::of($krama_mipil)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $nama = '';
                if($data->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$data->cacah_krama_mipil->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$data->cacah_krama_mipil->penduduk->nama;
                if($data->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$data->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $return = '';
                if($data->status == '1'){
                    if($data->cacah_krama_mipil->penduduk->tanggal_kematian != NULL || $data->cacah_krama_mipil->status == '0'){
                        if($data->cacah_krama_mipil->penduduk->tanggal_kematian != NULL){
                            $tanggal_kematian = date("d-m-Y", strtotime($data->cacah_krama_mipil->penduduk->tanggal_kematian));
                        }else{
                            $tanggal_kematian = date("d-m-Y", strtotime($data->cacah_krama_mipil->tanggal_nonaktif));
                        }
                        $return .= '<button class="btn btn-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Peringatan" onclick="tindakan('.$data->id.', \''.$tanggal_kematian.'\', \''.$nama.'\')"><i class="fas fa-exclamation-triangle"></i></button>';
                    }
                    $return .= '<a class="btn btn-primary btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-krama-mipil-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_krama('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }else if($data->status == '0'){
                    $return .= '<a class="btn btn-primary btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-krama-mipil-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                }
                
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }

    public function datatable_cacah_krama_mipil(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $arr_cacah_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('cacah_krama_mipil_id')->toArray();
        $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('id')->toArray();
        $arr_anggota_krama_mipil_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->where('status', '1')->pluck('cacah_krama_mipil_id')->toArray();
        $arr_cacah_krama_mipil_meninggal_id = Kematian::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('cacah_krama_mipil_id')->toArray();        
        $kramas = CacahKramaMipil::with('penduduk', 'tempekan')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_cacah_krama_mipil_id)->whereNotIn('id', $arr_anggota_krama_mipil_id)->whereNotIn('id', $arr_cacah_krama_mipil_meninggal_id);
        if (isset($request->tempekan_id)) {
            $kramas->where('tempekan_id', $request->tempekan_id);
        }
        return Datatables::of($kramas)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $nama = '';
                if($data->penduduk->gelar_depan != ''){
                    $nama = $nama.$data->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$data->penduduk->nama;
                if($data->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$data->penduduk->gelar_belakang;
                }
                $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_cacah_krama('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }
    
    public function index(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $pekerjaan = Pekerjaan::get();
        $pendidikan = Pendidikan::get();
        return view('pages.banjar.krama_mipil.krama_mipil', compact('tempekan', 'pekerjaan', 'pendidikan'));
    }

    public function get_penduduk($nik){
        $penduduk = Penduduk::with('ayah', 'ibu')->where('nik', $nik)->where('tanggal_kematian', NULL)->first();
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($penduduk){
            $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));
            $cacah_krama_mipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->first();
            $cacah_krama_tamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('tanggal_keluar', NULL)->first();
            $cacah_tamiu = CacahTamiu::where('penduduk_id', $penduduk->id)->where('banjar_adat_id', $banjar_adat_id)->where('tanggal_keluar', NULL)->first();
            if($cacah_krama_mipil){
                $status = 'terdaftar_krama_mipil';
                return response()->json([
                    'status' => $status
                ]);
            }else if($cacah_krama_tamiu){
                $status = 'terdaftar_krama_tamiu';
                return response()->json([
                    'status' => $status
                ]);
            }else if($cacah_tamiu){
                $status = 'terdaftar_tamiu';
                return response()->json([
                    'status' => $status
                ]);
            }else{
                $status = 'ditemukan';
                $desa = DesaDinas::find($penduduk->desa_id);
                $kecamatan = Kecamatan::where('id', $desa->kecamatan_id)->first();
                $kabupaten = Kabupaten::where('id', $kecamatan->kabupaten_id)->first();
                $provinsi = Provinsi::where('id', $kabupaten->provinsi_id)->first();
                
                //Data Master
                $provinsis = Provinsi::get();
                $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
                $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
                $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();
                $pekerjaans = Pekerjaan::get();
                $pendidikans = Pendidikan::get();
                return response()->json([
                    'status' => $status,
                    'penduduk' => $penduduk,
                    'desa' => $desa,
                    'kecamatan' => $kecamatan,
                    'kabupaten' => $kabupaten,
                    'provinsi' => $provinsi,
                    'desas' => $desas,
                    'kecamatans' => $kecamatans,
                    'kabupatens' => $kabupatens,
                    'provinsis' => $provinsis,
                    'pendidikans' => $pendidikans,
                    'pekerjaans' => $pekerjaans
                ]);
            }
        }else{
            $status = 'tidak_ditemukan';
            return response()->json([
                'status' => $status
            ]);
        }
    }

    public function create(){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        return view('pages.banjar.krama_mipil.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'tempekan', 'banjar_dinas'));
    }

    public function store(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $kramas = CacahKramaMipil::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();

        //TAMBAH PENDUDUK BARU
        if($penduduk == NULL){
            $validator = Validator::make($request->all(), [
                'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'pekerjaan' => 'required',
                'pendidikan' => 'required',
                'golongan_darah' => 'required',
                'status_perkawinan' => 'required',
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'desa' => 'required',
                'jenis_kependudukan' => 'required',
                'jenis_krama_mipil' => 'required',
                'kedudukan_krama_mipil' => 'required',
                'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas'
            ],[
                'nik.regex' => "NIK hanya boleh mengandung angka",
                'nik.unique' => "NIK yang dimasukkan telah terdaftar",
                'nama.required' => "Nama wajib diisi",
                'nama.regex' => "Nama hanya boleh mengandung huruf",
                'tempat_lahir.required' => "Tempat Lahir wajib diisi",
                'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
                'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
                'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
                'pekerjaan.required' => "Pekerjaan wajib dipilih",
                'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
                'golongan_darah.required' => "Golongan Darah wajib dipilih",
                'status_perkawinan.required' => "Status Perkawinan wajib dipilih",
                'alamat.required' => "Alamat wajib diisi",
                'provinsi.required' => "Provinsi wajib dipilih",
                'kabupaten.required' => "Kabupaten wajib dipilih",
                'kecamatan.required' => "Kecamatan wajib dipilih",
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
                'jenis_krama_mipil.required' => "Jenis Krama Mipil wajib dipilih",
                'kedudukan_krama_mipil.required' => "Kedudukan Krama Mipil wajib dipilih",
                'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
            ]);

            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $penduduk = new Penduduk();
            $penduduk->nik = $request->nik;
            $penduduk->desa_id = $request->desa;
            $penduduk->profesi_id = $request->pekerjaan;
            $penduduk->pendidikan_id = $request->pendidikan;
            $penduduk->gelar_depan = $request->gelar_depan;
            $penduduk->nama = $request->nama;
            $penduduk->gelar_belakang = $request->gelar_belakang;
            $penduduk->nama_alias = $request->nama_alias;
            $penduduk->agama = $request->agama;
            $penduduk->tempat_lahir = $request->tempat_lahir;
            $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $penduduk->jenis_kelamin = $request->jenis_kelamin;
            $penduduk->golongan_darah = $request->golongan_darah;
            $penduduk->status_perkawinan = $request->status_perkawinan;
            $penduduk->telepon = $request->telepon;
            $penduduk->alamat = $request->alamat;
            $penduduk->koordinat_alamat = $request->koordinat_alamat;
            $penduduk->ayah_kandung_id = $request->ayah_kandung;
            $penduduk->ibu_kandung_id = $request->ibu_kandung;
            if($request->foto != ''){
                $image_parts = explode(';base64', $request->foto);
                $image_type_aux = explode('image/', $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid().'.png';
                $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
                $path = $fileLocation."/".$filename;
                $penduduk->foto = '/storage'.$path;
                Storage::disk('public')->put($path, $image_base64);
            }
            $penduduk->save();
        }else{
            $validator = Validator::make($request->all(), [
                'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
                'nik' => [
                    Rule::unique('tb_penduduk')->ignore($penduduk->id),
                ],
                'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'pekerjaan' => 'required',
                'pendidikan' => 'required',
                'golongan_darah' => 'required',
                'status_perkawinan' => 'required',
                'alamat' => 'required',
                'provinsi' => 'required',
                'kabupaten' => 'required',
                'kecamatan' => 'required',
                'desa' => 'required',
                'jenis_kependudukan' => 'required',
                'jenis_krama_mipil' => 'required',
                'kedudukan_krama_mipil' => 'required',
                'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas'
            ],[
                'nik.regex' => "NIK hanya boleh mengandung angka",
                'nik.unique' => "NIK yang dimasukkan telah terdaftar",
                'nama.required' => "Nama wajib diisi",
                'nama.regex' => "Nama hanya boleh mengandung huruf",
                'tempat_lahir.required' => "Tempat Lahir wajib diisi",
                'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
                'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
                'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
                'pekerjaan.required' => "Pekerjaan wajib dipilih",
                'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
                'golongan_darah.required' => "Golongan Darah wajib dipilih",
                'status_perkawinan.required' => "Status Perkawinan wajib dipilih",
                'alamat.required' => "Alamat wajib diisi",
                'provinsi.required' => "Provinsi wajib dipilih",
                'kabupaten.required' => "Kabupaten wajib dipilih",
                'kecamatan.required' => "Kecamatan wajib dipilih",
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
                'jenis_krama_mipil.required' => "Jenis Krama Mipil wajib dipilih",
                'kedudukan_krama_mipil.required' => "Kedudukan Krama Mipil wajib dipilih",
                'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
            ]);

            if($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }

            $penduduk->nik = $request->nik;
            $penduduk->desa_id = $request->desa;
            $penduduk->profesi_id = $request->pekerjaan;
            $penduduk->pendidikan_id = $request->pendidikan;
            $penduduk->gelar_depan = $request->gelar_depan;
            $penduduk->nama = $request->nama;
            $penduduk->gelar_belakang = $request->gelar_belakang;
            $penduduk->nama_alias = $request->nama_alias;
            $penduduk->agama = $request->agama;
            $penduduk->tempat_lahir = $request->tempat_lahir;
            $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $penduduk->jenis_kelamin = $request->jenis_kelamin;
            $penduduk->golongan_darah = $request->golongan_darah;
            $penduduk->status_perkawinan = $request->status_perkawinan;
            $penduduk->telepon = $request->telepon;
            $penduduk->alamat = $request->alamat;
            $penduduk->koordinat_alamat = $request->koordinat_alamat;
            $penduduk->ayah_kandung_id = $request->ayah_kandung;
            $penduduk->ibu_kandung_id = $request->ibu_kandung;
            if($request->foto != ''){
                $image_parts = explode(';base64', $request->foto);
                $image_type_aux = explode('image/', $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = uniqid().'.png';
                $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
                $path = $fileLocation."/".$filename;
                $penduduk->foto = '/storage'.$path;
                Storage::disk('public')->put($path, $image_base64);
            }
            $penduduk->update();
        }

        //NOMOR CACAH KRAMA MIPIL & NOMOR INDUK CACAH KRAMA
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
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
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_mipil->tempekan_id = $request->tempekan_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->status = '1';
        $cacah_krama_mipil->jenis_kependudukan = $request->jenis_kependudukan;
        if($request->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $request->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();

        //GENERATE NOMOR KRAMA MIPIL
        $tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));

        $curr_month = Carbon::parse($tanggal_registrasi)->format('m');
        $curr_year = Carbon::parse($tanggal_registrasi)->year;
        $jumlah_krama_bulan_regis_sama = KramaMipil::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat_id)->whereYear('tanggal_registrasi', $curr_year)->where('status', '1')->withTrashed()->count();
        $curr_year = Carbon::parse($tanggal_registrasi)->format('y');
        $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
        $nomor_krama_mipil = $banjar_adat->kode_banjar_adat.'01'.$curr_month.$curr_year;
        if($jumlah_krama_bulan_regis_sama < 10){
            $nomor_krama_mipil = $nomor_krama_mipil.'00'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 100){
            $nomor_krama_mipil = $nomor_krama_mipil.'0'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 1000){
            $nomor_krama_mipil = $nomor_krama_mipil.$jumlah_krama_bulan_regis_sama;
        }

        //INSERT KRAMA MIPIL
        $krama_mipil = new KramaMipil();
        $krama_mipil->nomor_krama_mipil = $nomor_krama_mipil;
        $krama_mipil->banjar_adat_id = $banjar_adat_id;
        $krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
        $krama_mipil->status = '1';
        $krama_mipil->jenis_krama_mipil = $request->jenis_krama_mipil;
        $krama_mipil->kedudukan_krama_mipil = $request->kedudukan_krama_mipil;
        $krama_mipil->alasan_perubahan = 'Krama Mipil Baru';
        $krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $krama_mipil->save();

        return redirect()->route('banjar-krama-mipil-detail', $krama_mipil->id)->with('success', 'Krama Mipil berhasil ditambahkan');
    }

    public function generate_nomor_krama_mipil($tanggal_registrasi){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);

        $tanggal_registrasi = date("Y-m-d", strtotime($tanggal_registrasi));

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
        return response()->json([
            'nomor_krama_mipil' => $nomor_krama_mipil
        ]);
    }

    public function edit($id){
        //GET MASTER KRAMA
        $krama = KramaMipil::with('cacah_krama_mipil.penduduk.ayah', 'cacah_krama_mipil.penduduk.ibu')->find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($krama->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        //CONVERT TANGGAL LAHIR
        $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));

        //GET MASTER ALAMAT KRAMA
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);


        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $provinsis = Provinsi::get();

        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        //MASTER LAINNYA
        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        return view('pages.banjar.krama_mipil.edit', compact(
            'krama', 'cacah_krama_mipil', 'penduduk', 
            'provinsis', 'kabupatens', 'kecamatans', 'desas',
            'desa', 'kecamatan', 'kabupaten', 'provinsi',
            'pekerjaans', 'pendidikans', 'tempekan', 'banjar_dinas'));
    }

    public function anggota($id){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $krama_mipil->banjar_adat_id){
            return redirect()->back();
        }
        if($krama_mipil->status == '0'){
            return redirect()->back();
        }
        
        //SET NAMA KRAMA
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        $anggota_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->where('status', '1')->orderBy('status_hubungan', 'ASC')->get();
        return view('pages.banjar.krama_mipil.detail', compact('krama_mipil', 'anggota_krama_mipil'));
    }

    public function update($id, Request $request){
        //GET KRAMA
        $krama_mipil = KramaMipil::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        $validator = Validator::make($request->all(), [
            'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
            'nik' => [
                Rule::unique('tb_penduduk')->ignore($penduduk->id),
            ],
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'pekerjaan' => 'required',
            'pendidikan' => 'required',
            'golongan_darah' => 'required',
            'status_perkawinan' => 'required',
            'alamat' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
            'jenis_kependudukan' => 'required',
            'jenis_krama_mipil' => 'required',
            'kedudukan_krama_mipil' => 'required',
            'banjar_dinas_id' => 'required_if:jenis_kependudukan,==,adat_&_dinas'
        ],[
            'nik.regex' => "NIK hanya boleh mengandung angka",
            'nik.unique' => "NIK yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'pekerjaan.required' => "Pekerjaan wajib dipilih",
            'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
            'golongan_darah.required' => "Golongan Darah wajib dipilih",
            'status_perkawinan.required' => "Status Perkawinan wajib dipilih",
            'alamat.required' => "Alamat wajib diisi",
            'provinsi.required' => "Provinsi wajib dipilih",
            'kabupaten.required' => "Kabupaten wajib dipilih",
            'kecamatan.required' => "Kecamatan wajib dipilih",
            'desa.required' => "Desa/Kelurahan wajib dipilih",
            'jenis_kependudukan.required' => "Jenis Kependudukan wajib dipilih",
            'jenis_krama_mipil.required' => "Jenis Krama Mipil wajib dipilih",
            'kedudukan_krama_mipil.required' => "Kedudukan Krama Mipil wajib dipilih",
            'banjar_dinas_id.required_if' => "Banjar Dinas wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET ANGGOTA KELUARGA
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->get();

        //UPDATE DATA ANGGOTA YANG SAMA
        foreach($anggota_krama_mipil as $anggota){
            //GET CACAH ANGGOTA
            $cacah_anggota = CacahKramaMipil::find($anggota->cacah_krama_mipil_id);
            $cacah_anggota->jenis_kependudukan = $request->jenis_kependudukan;
            $cacah_anggota->tempekan_id = $request->tempekan_id;
            if($request->jenis_kependudukan == 'adat_&_dinas'){
                $cacah_anggota->banjar_dinas_id = $request->banjar_dinas_id;            
            }else{
                $cacah_anggota->banjar_dinas_id = NULL;            
            }
            $cacah_anggota->update();

            //GET PENDUDUK ANGGOTA
            $penduduk_anggota = Penduduk::find($cacah_anggota->penduduk_id);
            if($penduduk_anggota->alamat == $krama_mipil->cacah_krama_mipil->penduduk->alamat){
                $penduduk_anggota->desa_id = $request->desa;
                $penduduk_anggota->alamat = $request->alamat;
                $penduduk_anggota->koordinat_alamat = $request->koordinat_alamat;
                $penduduk_anggota->update();
            }
        }

        //UPDATE PENDUDUK KRAMA
        if($request->foto != ''){
            $image_parts = explode(';base64', $request->foto);
            $image_type_aux = explode('image/', $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $filename = uniqid().'.png';
            $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
            $path = $fileLocation."/".$filename;
            $penduduk->foto = '/storage'.$path;
            Storage::disk('public')->put($path, $image_base64);
        }
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $request->desa;
        $penduduk->profesi_id = $request->pekerjaan;
        $penduduk->pendidikan_id = $request->pendidikan;
        $penduduk->gelar_depan = $request->gelar_depan;
        $penduduk->nama = $request->nama;
        $penduduk->gelar_belakang = $request->gelar_belakang;
        $penduduk->nama_alias = $request->nama_alias;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->status_perkawinan = $request->status_perkawinan;
        $penduduk->telepon = $request->telepon;
        $penduduk->alamat = $request->alamat;
        $penduduk->koordinat_alamat = $request->koordinat_alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        $penduduk->update();

        //UPDATE CACAH KRAMA MIPIL
        $cacah_krama_mipil->tempekan_id = $request->tempekan_id;
        $cacah_krama_mipil->jenis_kependudukan = $request->jenis_kependudukan;
        if($request->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $request->banjar_dinas_id;
        }else{
            $cacah_krama_mipil->banjar_dinas_id = NULL;
        }
        $cacah_krama_mipil->update();

        //UPDATE KRAMA MIPIL
        $krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_registrasi));
        $krama_mipil->jenis_krama_mipil = $request->jenis_krama_mipil;
        $krama_mipil->kedudukan_krama_mipil = $request->kedudukan_krama_mipil;
        $krama_mipil->update();

        return redirect()->route('banjar-krama-mipil-detail', $krama_mipil->id)->with('success', 'Krama Mipil berhasil diperbaharui');
    }

    public function detail($id){
        $krama = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $krama->banjar_adat_id){
            return redirect()->back();
        }
        if($krama->status == '0'){
            return redirect()->back();
        }

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
        return view('pages.banjar.krama_mipil.detail_krama_mipil', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas', 'tempekan'));
    }

    public function ganti($id, Request $request){
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
                $anggota_krama_mipil_baru->tanggal_registrasi = $anggota_lama->tanggal_registrasi;
                $anggota_krama_mipil_baru->save();
            }else if($anggota_lama->cacah_krama_mipil_id == $request->krama_mipil_baru && $request->alasan_penggantian != 'krama_mipil_meninggal_dunia'){
                    $anggota_krama_mipil_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_baru->krama_mipil_id = $krama_mipil_baru->id;
                    $anggota_krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
                    $anggota_krama_mipil_baru->status_hubungan = 'famili_lain';
                    $anggota_krama_mipil_baru->tanggal_registrasi = $krama_mipil_lama->tanggal_registrasi;
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
            $anggota_lama->status == '1';
            $anggota_lama->update();
        }

        //NONAKTIFKAN KRAMA MIPIL LAMA
        $krama_mipil_lama->status = '0';
        $krama_mipil_lama->update();

        return redirect()->route('banjar-krama-mipil-detail', $krama_mipil_baru->id)->with('success', 'Krama Mipil berhasil diganti');
    }

    public function delete($id, Request $request){
         //GET KRAMA DAN KELUARGA
         $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
         $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->get();

         foreach($anggota_krama_mipil as $anggota){
             $anggota->status = '0';
             $anggota->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
             $anggota->alasan_keluar = $request->alasan_keluar;
             $anggota->update();

             //NONAKTIFKAN CACAH
             $cacah_anggota = CacahKramaMipil::find($anggota->cacah_krama_mipil_id);
             $cacah_anggota->status = '0';
             $cacah_anggota->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
             $cacah_anggota->alasan_keluar = $request->alasan_keluar;
             $cacah_anggota->update();
         }

         $krama_mipil->status = '0';
         $krama_mipil->alasan_keluar = $request->alasan_keluar;
         $krama_mipil->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
         $krama_mipil->update();

         //NONAKTIFKAN CACAH KRAMA MIPIL
         $cacah_krama_mipil = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
         $cacah_krama_mipil->status = '0';
         $cacah_krama_mipil->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
         $cacah_krama_mipil->alasan_keluar = $request->alasan_keluar;
         $cacah_krama_mipil->update();

         return redirect()->back()->with('success', 'Krama Mipil berhasil dinonaktifkan');
    }

    public function daftar_riwayat($id){
        //GET KRAMA CACAH
        $krama_mipil = KramaMipil::find($id);
        $cacah_krama_mipil = CacahKramaMipil::with('penduduk')->find($krama_mipil->cacah_krama_mipil_id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_mipil->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
        $nama = '';
        if($penduduk->gelar_depan != NULL){
            $nama = $nama.$penduduk->gelar_depan.' ';
        }
        $nama = $nama.$penduduk->nama;
        if($penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$penduduk->gelar_belakang;
        }
        $penduduk->nama = $nama;

        //GET RIWAYAT PERUBAHAN
        $riwayats = LogPenduduk::where('penduduk_id', $penduduk->id)->get();

        //RETURN
        return view('pages.banjar.krama_mipil.daftar_riwayat', compact('riwayats', 'penduduk','krama_mipil'));
    }

    public function detail_riwayat($id, $id_riwayat){
        //GET KRAMA
        $krama_mipil = KramaMipil::find($id);
        $cacah_krama_mipil = CacahKramaMipil::with('penduduk')->find($krama_mipil->cacah_krama_mipil_id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_mipil->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $curr_penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
        $nama = '';
        if($curr_penduduk->gelar_depan != NULL){
            $nama = $nama.$curr_penduduk->gelar_depan.' ';
        }
        $nama = $nama.$curr_penduduk->nama;
        if($curr_penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$curr_penduduk->gelar_belakang;
        }
        $curr_penduduk->nama = $nama;
        
        //GET RIWAYAT PERUBAHAN
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $krama = $cacah_krama_mipil;
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = LogPenduduk::with('pekerjaan', 'pendidikan')->find($id_riwayat);
        if($krama->penduduk_id != $penduduk->penduduk_id){
            return redirect()->back();
        }

        //GET DATA
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat->id)->get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);
        return view('pages.banjar.krama_mipil.detail_riwayat', compact('curr_penduduk', 'krama', 'krama_mipil', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat', 'banjar_dinas'));
    }

    public function kartu_keluarga($id){
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
        if($krama_mipil->banjar_adat_id != $banjar_adat_id){
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

        return view('pages.banjar.krama_mipil.kartu_keluarga', compact(
            'krama_mipil', 'anggota_krama_mipil',
            'banjar_adat', 'banjar_dinas','desa_adat', 'bendesa',
            'kecamatan', 'kabupaten', 'tanggal_sekarang'
        ));
    }

    public function riwayat_keluarga($id){
         //GET KRAMA MIPIL
         $krama_mipil = KramaMipil::find($id);
         $krama_mipil = Helper::generate_nama_krama_mipil($krama_mipil);
 
         //VALIDASI
         $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
         if($krama_mipil->banjar_adat_id != $banjar_adat_id){
             return redirect()->back();
         }

         //GET NOMOR KRAMA MIPIL
         $nomor_krama_mipil = $krama_mipil->nomor_krama_mipil;

         //GET RIWAYAT
         $riwayat_krama_mipil = KramaMipil::where('nomor_krama_mipil', $nomor_krama_mipil)->get();

         //RETURN VIEW
         return view('pages.banjar.krama_mipil.riwayat_keluarga', compact('krama_mipil', 'riwayat_krama_mipil'));
    }

    public function detail_riwayat_keluarga($id, $id_riwayat){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id_riwayat);
        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $krama_mipil->banjar_adat_id){
            return redirect()->back();
        }
        
        //SET NAMA KRAMA
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        $anggota_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->orderBy('status_hubungan', 'ASC')->get();
        return view('pages.banjar.krama_mipil.detail_riwayat_keluarga', compact('krama_mipil', 'anggota_krama_mipil', 'id'));
    }
}