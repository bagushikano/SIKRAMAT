<?php

namespace App\Http\Controllers\BanjarAdatController;
use App\Http\Controllers\Controller;
use App\Models\AnggotaKramaMipil;
use App\Models\AnggotaKramaTamiu;
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
use App\Models\KramaTamiu;
use App\Models\LogPenduduk;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\Tempekan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KramaTamiuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk', 'cacah_krama_tamiu.banjar_dinas', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id);

        if (isset($request->status)) {
            if($request->status == '1'){
                $krama_tamiu->where('status', '1');
            }else if($request->status == '0'){
                $krama_tamiu->where('status', '0')->where('tanggal_nonaktif', '!=', NULL);
            }else if($request->status == '2'){
                $krama_tamiu->where('banjar_adat_id', $banjar_adat_id)->where('status', '1');
                if(isset($request->rentang_waktu)){
                    $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
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
                    $krama_tamiu->whereBetween('tanggal_registrasi', [$start_date, $end_date]);
                }
        
                if (isset($request->golongan_darah)) {
                    $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
                        return $query->whereIn('golongan_darah', $request->golongan_darah);
                    });
                }
        
                if (isset($request->asal)) {
                    $krama_tamiu->whereHas('cacah_krama_tamiu', function ($query) use ($request) {
                        return $query->where('asal', $request->asal);
                    });
                }
        
                if (isset($request->pekerjaan)) {
                    $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
                        return $query->whereIn('profesi_id', $request->pekerjaan);
                    });
                }
        
                if (isset($request->pendidikan)) {
                    $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
                        return $query->whereIn('pendidikan_id', $request->pendidikan);
                    });
                }
                $krama_tamiu->orWhere(function ($query) use ($banjar_adat_id) {
                    $query->where('status', '0')
                        ->where('tanggal_nonaktif', '!=', NULL)
                        ->where('banjar_adat_id', $banjar_adat_id);
                });
            }
        }else{
            $krama_tamiu->where('status', '1');
        }

        if(isset($request->rentang_waktu)){
            $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
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
            $krama_tamiu->whereBetween('tanggal_registrasi', [$start_date, $end_date]);
        }

        if (isset($request->golongan_darah)) {
            $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
                return $query->whereIn('golongan_darah', $request->golongan_darah);
            });
        }

        if (isset($request->asal)) {
            $krama_tamiu->whereHas('cacah_krama_tamiu', function ($query) use ($request) {
                return $query->where('asal', $request->asal);
            });
        }

        if (isset($request->pekerjaan)) {
            $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
                return $query->whereIn('profesi_id', $request->pekerjaan);
            });
        }

        if (isset($request->pendidikan)) {
            $krama_tamiu->whereHas('cacah_krama_tamiu.penduduk', function ($query) use ($request) {
                return $query->whereIn('pendidikan_id', $request->pendidikan);
            });
        }

        $krama_tamiu->orderBy('tanggal_registrasi', 'DESC');
        return DataTables::of($krama_tamiu)
            ->addIndexColumn()
            ->addColumn('link', function ($data) {
                $nama = '';
                if($data->cacah_krama_tamiu->penduduk->gelar_depan != ''){
                    $nama = $nama.$data->cacah_krama_tamiu->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$data->cacah_krama_tamiu->penduduk->nama;
                if($data->cacah_krama_tamiu->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$data->cacah_krama_tamiu->penduduk->gelar_belakang;
                }
                $return = '';
                // if($data->cacah_krama_tamiu->penduduk->tanggal_kematian != NULL || $data->cacah_krama_tamiu->status == '0'){
                //     if($data->cacah_krama_tamiu->penduduk->tanggal_kematian != NULL){
                //         $tanggal_kematian = date("d-m-Y", strtotime($data->cacah_krama_tamiu->penduduk->tanggal_kematian));
                //     }else{
                //         $tanggal_kematian = date("d-m-Y", strtotime($data->cacah_krama_tamiu->tanggal_nonaktif));
                //     }
                //     $return .= '<button class="btn btn-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Peringatan" onclick="tindakan('.$data->id.', \''.$tanggal_kematian.'\', \''.$nama.'\')"><i class="fas fa-exclamation-triangle"></i></button>';
                // }
                if($data->status == '1'){
                    $return .= '<a class="btn btn-primary btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-krama-tamiu-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_krama('.$data->id.')"><i class="fas fa-user-alt-slash"></i></button>';
                }else if($data->status == '0'){
                    $return .= '<a class="btn btn-primary btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-krama-tamiu-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                }
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
        return view('pages.banjar.krama_tamiu.krama_tamiu', compact('tempekan', 'pekerjaan', 'pendidikan'));
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

    public function create()
    {
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
        $provinsis = Provinsi::get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
        return view('pages.banjar.krama_tamiu.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'kabupatens', 'tempekan', 'banjar_dinas', 'desas'));
    }

    public function store(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $penduduk = Penduduk::where('nik', $request->nik)->first();
        $kramas = CacahKramaTamiu::where('banjar_adat_id', $banjar_adat_id)->pluck('penduduk_id')->toArray();

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
                'desa' => 'required',
                'banjar_dinas_id' => 'required',
                'asal' => 'required',
                'provinsi_asal' => 'required_if:asal,==,luar_bali',
                'kabupaten_asal' => 'required_if:asal,==,luar_bali',
                'kecamatan_asal' => 'required_if:asal,==,luar_bali',
                'desa_asal' => 'required_if:asal,==,luar_bali',
                'kabupaten_asal_dalam_bali' => 'required_if:asal,==,dalam_bali',
                'kecamatan_asal_dalam_bali' => 'required_if:asal,==,dalam_bali',
                'desa_adat_asal' => 'required_if:asal,==,dalam_bali',
                'banjar_adat_asal' => 'required_if:asal,==,dalam_bali',
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
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
                'asal.required' => "Asal Krama Tamiu wajib dipilih",
                'provinsi_asal.required_if' => "Provinsi Asal wajib dipilih",
                'kabupaten_asal.required_if' => "Kabupaten Asal wajib dipilih",
                'kecamatan_asal.required_if' => "Kecamatan Asal wajib dipilih",
                'desa_asal.required_if' => "Desa/Kelurahan Asal wajib dipilih",
                'kabupaten_asal_dalam_bali.required_if' => "Kabupaten Asal wajib dipilih",
                'kecamatan_asal_dalam_bali.required_if' => "Kecamatan Asal wajib dipilih",
                'desa_adat_asal.required_if' => "Desa Adat Asal wajib dipilih",
                'banjar_adat_asal.required_if' => "Banjar Adat Asal wajib dipilih",
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
                'desa' => 'required',
                'banjar_dinas_id' => 'required',
                'asal' => 'required',
                'provinsi_asal' => 'required_if:asal,==,luar_bali',
                'kabupaten_asal' => 'required_if:asal,==,luar_bali',
                'kecamatan_asal' => 'required_if:asal,==,luar_bali',
                'desa_asal' => 'required_if:asal,==,luar_bali',
                'kabupaten_asal_dalam_bali' => 'required_if:asal,==,dalam_bali',
                'kecamatan_asal_dalam_bali' => 'required_if:asal,==,dalam_bali',
                'desa_adat_asal' => 'required_if:asal,==,dalam_bali',
                'banjar_adat_asal' => 'required_if:asal,==,dalam_bali',
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
                'desa.required' => "Desa/Kelurahan wajib dipilih",
                'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
                'asal.required' => "Asal Krama Tamiu wajib dipilih",
                'provinsi_asal.required_if' => "Provinsi Asal wajib dipilih",
                'kabupaten_asal.required_if' => "Kabupaten Asal wajib dipilih",
                'kecamatan_asal.required_if' => "Kecamatan Asal wajib dipilih",
                'desa_asal.required_if' => "Desa/Kelurahan Asal wajib dipilih",
                'kabupaten_asal_dalam_bali.required_if' => "Kabupaten Asal wajib dipilih",
                'kecamatan_asal_dalam_bali.required_if' => "Kecamatan Asal wajib dipilih",
                'desa_adat_asal.required_if' => "Desa Adat Asal wajib dipilih",
                'banjar_adat_asal.required_if' => "Banjar Adat Asal wajib dipilih",
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

        //NOMOR CACAH KRAMA MIPIL
        $banjar_adat_id = session()->get('banjar_adat_id');
        $jumlah_penduduk_tanggal_sama = Penduduk::where('tanggal_lahir', $penduduk->tanggal_lahir)->whereIn('id', $kramas)->withTrashed()->count();
        $jumlah_penduduk_tanggal_sama = $jumlah_penduduk_tanggal_sama + 1;
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $nomor_cacah_krama_tamiu = $banjar_adat->kode_banjar_adat;
        $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.'02'.Carbon::parse($penduduk->tanggal_lahir)->format('dmy');
        if($jumlah_penduduk_tanggal_sama<10){
            $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.'00'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<100){
            $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.'0'.$jumlah_penduduk_tanggal_sama;
        }else if($jumlah_penduduk_tanggal_sama<1000){
            $nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu.$jumlah_penduduk_tanggal_sama;
        }

        //INSERT CACAH KRAMA TAMIU
        $cacah_krama_tamiu = new CacahKramaTamiu();
        $cacah_krama_tamiu->nomor_cacah_krama_tamiu = $nomor_cacah_krama_tamiu;
        $cacah_krama_tamiu->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $cacah_krama_tamiu->penduduk_id = $penduduk->id;
        $cacah_krama_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        $cacah_krama_tamiu->asal = $request->asal;
        $cacah_krama_tamiu->alamat_asal = $request->alamat_asal;
        if($request->asal == 'dalam_bali'){
            $cacah_krama_tamiu->banjar_adat_asal_id = $request->banjar_adat_asal; 
        }else if($request->asal == 'luar_bali'){
            $cacah_krama_tamiu->desa_asal_id = $request->desa_asal;
        }
        $cacah_krama_tamiu->save();

        //GENERATE NOMOR KRAMA TAMIU
        $tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));

        $curr_month = Carbon::parse($tanggal_masuk)->format('m');
        $curr_year = Carbon::parse($tanggal_masuk)->year;
        $jumlah_krama_bulan_regis_sama = KramaTamiu::whereMonth('tanggal_registrasi', $curr_month)->where('banjar_adat_id', $banjar_adat->id)->whereYear('tanggal_registrasi', $curr_year)->where('status', '1')->withTrashed()->count();
        $curr_year = Carbon::parse($tanggal_masuk)->format('y');
        $jumlah_krama_bulan_regis_sama = $jumlah_krama_bulan_regis_sama + 1;
        $nomor_krama_tamiu = $banjar_adat->kode_banjar_adat.'02'.$curr_month.$curr_year;
        if($jumlah_krama_bulan_regis_sama < 10){
            $nomor_krama_tamiu = $nomor_krama_tamiu.'00'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 100){
            $nomor_krama_tamiu = $nomor_krama_tamiu.'0'.$jumlah_krama_bulan_regis_sama;
        }else if($jumlah_krama_bulan_regis_sama < 1000){
            $nomor_krama_tamiu = $nomor_krama_tamiu.$jumlah_krama_bulan_regis_sama;
        }

        //INSERT KRAMA TAMIU
        $krama_tamiu = new KramaTamiu();
        $krama_tamiu->nomor_krama_tamiu = $nomor_krama_tamiu;
        $krama_tamiu->banjar_adat_id = $banjar_adat_id;
        $krama_tamiu->cacah_krama_tamiu_id = $cacah_krama_tamiu->id;
        $krama_tamiu->status = '1';
        $krama_tamiu->alasan_perubahan = 'Krama Tamiu Baru';
        $krama_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_masuk));
        $krama_tamiu->save();

        return redirect()->route('banjar-krama-tamiu-detail', $krama_tamiu->id)->with('success', 'Krama Tamiu berhasil ditambahkan');
    }

    public function anggota($id){
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk')->find($id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $krama_tamiu->banjar_adat_id){
            return redirect()->back();
        }
        if($krama_tamiu->status == '0'){
            return redirect()->back();
        }

        //SET NAMA KRAMA
        $nama = '';
        if($krama_tamiu->cacah_krama_tamiu->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_tamiu->cacah_krama_tamiu->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$krama_tamiu->cacah_krama_tamiu->penduduk->nama;
        if($krama_tamiu->cacah_krama_tamiu->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_tamiu->cacah_krama_tamiu->penduduk->gelar_belakang;
        }
        $krama_tamiu->cacah_krama_tamiu->penduduk->nama = $nama;

        $anggota_krama_tamiu = AnggotaKramaTamiu::where('krama_tamiu_id', $krama_tamiu->id)->where('status', '1')->orderBy('status_hubungan', 'ASC')->get();
        return view('pages.banjar.krama_tamiu.detail', compact('krama_tamiu', 'anggota_krama_tamiu'));
    }

    public function edit($id){
        //GET MASTER KRAMA
        $krama = KramaTamiu::with('cacah_krama_tamiu.penduduk.ayah', 'cacah_krama_tamiu.penduduk.ibu')->find($id);
        $cacah_krama_tamiu = CacahKramaTamiu::find($krama->cacah_krama_tamiu_id);
        $penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $cacah_krama_tamiu->banjar_adat_id){
            return redirect()->back();
        }

        //CONVERT TANGGAL LAHIR
        $penduduk->tanggal_lahir = date("d-m-Y", strtotime($penduduk->tanggal_lahir));

        //MASTER LAINNYA
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $pekerjaans = Pekerjaan::get();
        $pendidikans = Pendidikan::get();
        $provinsis = Provinsi::get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();

        if($krama->cacah_krama_tamiu->asal == 'luar_bali'){
            $desa_asal = DesaDinas::find($krama->cacah_krama_tamiu->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

            $desas = DesaDinas::where('kecamatan_id', $kecamatan_asal->id)->get();
            $desa_sekitars = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
            $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten_asal->id)->get();
            $kabupatens = Kabupaten::where('provinsi_id', $provinsi_asal->id)->get();
            return view('pages.banjar.krama_tamiu.edit', compact('desa_sekitars', 'krama', 'penduduk', 'pekerjaans', 'pendidikans', 'provinsis', 'banjar_dinas', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal', 'desas', 'kecamatans', 'kabupatens'));
        }else if($krama->cacah_krama_tamiu->asal == 'dalam_bali'){
            $banjar_adat_asal = BanjarAdat::find($krama->cacah_krama_tamiu->banjar_adat_asal_id);
            $desa_adat_asal = DesaAdat::find($banjar_adat_asal->desa_adat_id);
            $kecamatan_asal = Kecamatan::find($desa_adat_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);

            $desa_sekitars = DesaDinas::where('kecamatan_id', $desa_adat_asal->kecamatan_id)->get();
            $banjar_adat = BanjarAdat::where('desa_adat_id', $desa_adat_asal->id)->get();
            $desa_adat = DesaAdat::where('kecamatan_id', $kecamatan_asal->id)->get();
            $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten_asal->id)->get();
            $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
            return view('pages.banjar.krama_tamiu.edit', compact('krama', 'penduduk', 'pekerjaans', 'pendidikans', 'provinsis', 'desa_sekitars', 'banjar_dinas', 'banjar_adat_asal', 'desa_adat_asal', 'kecamatan_asal', 'kabupaten_asal', 'banjar_adat', 'desa_adat', 'kecamatans', 'kabupatens'));
        }
    }

    public function update($id, Request $request){
        //GET KRAMA
        $krama_tamiu = KramaTamiu::find($id);
        $cacah_krama_tamiu= CacahKramaTamiu::find($krama_tamiu->cacah_krama_tamiu_id);
        $penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);

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
            'desa' => 'required',
            'banjar_dinas_id' => 'required',
            'asal' => 'required',
            'provinsi_asal' => 'required_if:asal,==,luar_bali',
            'kabupaten_asal' => 'required_if:asal,==,luar_bali',
            'kecamatan_asal' => 'required_if:asal,==,luar_bali',
            'desa_asal' => 'required_if:asal,==,luar_bali',
            'kabupaten_asal_dalam_bali' => 'required_if:asal,==,dalam_bali',
            'kecamatan_asal_dalam_bali' => 'required_if:asal,==,dalam_bali',
            'desa_adat_asal' => 'required_if:asal,==,dalam_bali',
            'banjar_adat_asal' => 'required_if:asal,==,dalam_bali',
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
            'desa.required' => "Desa/Kelurahan wajib dipilih",
            'banjar_dinas_id.required' => "Banjar Dinas wajib dipilih",
            'asal.required' => "Asal Krama Tamiu wajib dipilih",
            'provinsi_asal.required_if' => "Provinsi Asal wajib dipilih",
            'kabupaten_asal.required_if' => "Kabupaten Asal wajib dipilih",
            'kecamatan_asal.required_if' => "Kecamatan Asal wajib dipilih",
            'desa_asal.required_if' => "Desa/Kelurahan Asal wajib dipilih",
            'kabupaten_asal_dalam_bali.required_if' => "Kabupaten Asal wajib dipilih",
            'kecamatan_asal_dalam_bali.required_if' => "Kecamatan Asal wajib dipilih",
            'desa_adat_asal.required_if' => "Desa Adat Asal wajib dipilih",
            'banjar_adat_asal.required_if' => "Banjar Adat Asal wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //UPDATE PENDUDUK
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

        //UPDATE CACAH
        $cacah_krama_tamiu->banjar_dinas_id = $request->banjar_dinas_id;
        $cacah_krama_tamiu->tanggal_masuk = date("Y-m-d", strtotime($request->tanggal_masuk));
        $cacah_krama_tamiu->asal = $request->asal;
        $cacah_krama_tamiu->alamat_asal = $request->alamat_asal;
        if($request->asal == 'dalam_bali'){
            $cacah_krama_tamiu->banjar_adat_asal_id = $request->banjar_adat_asal; 
            $cacah_krama_tamiu->desa_asal_id = NULL;
        }else if($request->asal == 'luar_bali'){
            $cacah_krama_tamiu->banjar_adat_asal_id = NULL;
            $cacah_krama_tamiu->desa_asal_id = $request->desa_asal;
        }
        $cacah_krama_tamiu->update();

        //UPDATE KRAMA
        $krama_tamiu->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_masuk));
        $krama_tamiu->update();

        //UPDATE ANGGOTA
        $anggota_krama_tamiu = AnggotaKramaTamiu::where('krama_tamiu_id', $krama_tamiu->id)->get();
        foreach($anggota_krama_tamiu as $anggota){
            //GET CACAH ANGGOTA
            $cacah_anggota = CacahKramaTamiu::find($anggota->cacah_krama_tamiu_id);
            $cacah_anggota->asal = $request->asal;
            $cacah_anggota->alamat_asal = $request->alamat_asal;
            if($request->asal == 'dalam_bali'){
                $cacah_anggota->banjar_adat_asal_id = $request->banjar_adat_asal; 
                $cacah_anggota->desa_asal_id = NULL;
            }else if($request->asal == 'luar_bali'){
                $cacah_anggota->banjar_adat_asal_id = NULL;
                $cacah_anggota->desa_asal_id = $request->desa_asal;
            }
            $cacah_anggota->update();

            //GET PENDUDUK ANGGOTA
            $penduduk_anggota = Penduduk::find($cacah_anggota->penduduk_id);
            $penduduk_anggota->desa_id = $request->desa;
            $penduduk_anggota->alamat = $request->alamat;
            $penduduk_anggota->koordinat_alamat = $request->koordinat_alamat;
            $penduduk_anggota->update();
            
        }

        return redirect()->route('banjar-krama-tamiu-detail', $krama_tamiu->id)->with('success', 'Krama Tamiu berhasil diperbaharui');
    }

    public function detail($id){
        $krama = KramaTamiu::with('cacah_krama_tamiu.penduduk')->find($id);

        //VALIDASI
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($banjar_adat_id != $krama->banjar_adat_id){
            return redirect()->back();
        }
        if($krama->status == '0'){
            return redirect()->back();
        }

        $cacah_krama_tamiu = CacahKramaTamiu::with('penduduk')->find($krama->cacah_krama_tamiu_id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($cacah_krama_tamiu->penduduk_id);
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);

        //Asal luar bali
        if($krama->cacah_krama_tamiu->asal == 'luar_bali'){
            $desa_asal = DesaDinas::find($krama->cacah_krama_tamiu->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
            return view('pages.banjar.krama_tamiu.detail_krama_tamiu', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
        }else if($krama->cacah_krama_tamiu->asal == 'dalam_bali'){
            $banjar_adat_asal = BanjarAdat::find($krama->cacah_krama_tamiu->banjar_adat_asal_id);
            $desa_adat_asal = DesaAdat::find($banjar_adat_asal->desa_adat_id);
            $kecamatan_asal = Kecamatan::find($desa_adat_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            return view('pages.banjar.krama_tamiu.detail_krama_tamiu', compact('krama', 'penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat_asal', 'desa_adat_asal', 'kecamatan_asal', 'kabupaten_asal'));
        }
    }

    public function ganti($id, Request $request){
        $validator = Validator::make($request->all(), [
            'krama_tamiu_baru' => 'required',
            'alasan_penggantian' => 'required',
        ],[
            'krama_tamiu_baru.required' => "Krama Tamiu baru wajib dipilih",
            'alasan_penggantian.required' => "Alasan Pergantian wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET KELUARGA LAMA
        $krama_tamiu_lama = KramaTamiu::with('cacah_krama_tamiu.penduduk')->find($id);
        $anggota_krama_tamiu_lama = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->where('krama_tamiu_id', $krama_tamiu_lama->id)->get();

        //INSERT KRAMA TAMIU BARU
        $krama_tamiu_baru = new KramaTamiu();
        $krama_tamiu_baru->nomor_krama_tamiu = $krama_tamiu_lama->nomor_krama_tamiu;
        $krama_tamiu_baru->banjar_adat_id = $krama_tamiu_lama->banjar_adat_id;
        $krama_tamiu_baru->cacah_krama_tamiu_id = $request->krama_tamiu_baru;
        $krama_tamiu_baru->status = '1';
        $krama_tamiu_baru->alasan_perubahan = ucwords(str_replace('_', ' ', $request->alasan_penggantian));
        $krama_tamiu_baru->tanggal_registrasi = $krama_tamiu_lama->tanggal_registrasi;
        $krama_tamiu_baru->save();

        //INSERT ANGGOTA BARU DAN DELETE ANGGOTA LAMA SEBAGAI HISTORY
        foreach ($anggota_krama_tamiu_lama as $anggota_lama){
            if($anggota_lama->cacah_krama_tamiu_id != $request->krama_tamiu_baru){
                $anggota_krama_tamiu_baru = new AnggotaKramaTamiu();
                $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
                $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $anggota_lama->cacah_krama_tamiu_id;
                $anggota_krama_tamiu_baru->status_hubungan = $anggota_lama->status_hubungan;
                $anggota_krama_tamiu_baru->tanggal_registrasi = $anggota_lama->tanggal_registrasi;
                $anggota_krama_tamiu_baru->save();
            }else if($anggota_lama->cacah_krama_tamiu_id == $request->krama_tamiu_baru && $request->alasan_penggantian != 'krama_tamiu_meninggal_dunia'){
                    $anggota_krama_tamiu_baru = new AnggotaKramaTamiu();
                    $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
                    $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $krama_tamiu_lama->cacah_krama_tamiu_id;
                    $anggota_krama_tamiu_baru->status_hubungan = 'famili_lain';
                    $anggota_krama_tamiu_baru->tanggal_registrasi = $krama_tamiu_lama->tanggal_registrasi;
                    $anggota_krama_tamiu_baru->save();
                // if($anggota_lama->status_hubungan == 'anak'){
                //     $anggota_krama_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
                //     $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $krama_tamiu_lama->cacah_krama_tamiu_id;
                //     if($krama_tamiu_lama->cacah_krama_tamiu->penduduk->jenis_kelamin == 'perempuan'){
                //         $anggota_krama_tamiu_baru->status_hubungan = 'ibu';
                //     }else{
                //         $krama_tamiu_lama->status_hubungan = 'ayah';
                //     }
                //     $anggota_krama_tamiu_baru->save();
                // }else if($anggota_lama->status_hubungan == 'istri'){
                //     $anggota_krama_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
                //     $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $krama_tamiu_lama->cacah_krama_tamiu_id;
                //     $anggota_krama_tamiu_baru->status_hubungan = 'suami';
                //     $anggota_krama_tamiu_baru->save();
                // }else if($anggota_lama->status_hubungan == 'suami'){
                //     $anggota_krama_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
                //     $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $krama_tamiu_lama->cacah_krama_tamiu_id;
                //     $anggota_krama_tamiu_baru->status_hubungan = 'istri';
                //     $anggota_krama_tamiu_baru->save();
                // }else if($anggota_lama->status_hubungan == 'ayah' || $anggota_lama->status_hubungan == 'ibu'){
                //     $anggota_krama_tamiu_baru = new AnggotaKramaMipil();
                //     $anggota_krama_tamiu_baru->krama_tamiu_id = $krama_tamiu_baru->id;
                //     $anggota_krama_tamiu_baru->cacah_krama_tamiu_id = $krama_tamiu_lama->cacah_krama_tamiu_id;
                //     $anggota_krama_tamiu_baru->status_hubungan = 'anak';
                //     $anggota_krama_tamiu_baru->save();
                // }
                
            }
            $anggota_lama->status == '1';
            $anggota_lama->update();
        }

        //NONAKTIFKAN KRAMA TAMIU LAMA
        $krama_tamiu_lama->status = '0';
        $krama_tamiu_lama->update();

        return redirect()->route('banjar-krama-tamiu-detail', $krama_tamiu_baru->id)->with('success', 'Krama Tamiu berhasil diganti');
    }

    public function daftar_riwayat($id){
        //GET KRAMA CACAH
        $krama_tamiu = KramaTamiu::find($id);
        $cacah_krama_tamiu = CacahKramaTamiu::with('penduduk')->find($krama_tamiu->cacah_krama_tamiu_id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);
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
        return view('pages.banjar.krama_tamiu.daftar_riwayat', compact('riwayats', 'penduduk','krama_tamiu'));
    }

    public function detail_riwayat($id, $id_riwayat){
        //GET KRAMA
        $krama_tamiu = KramaTamiu::find($id);
        $cacah_krama_tamiu = CacahKramaTamiu::with('penduduk')->find($krama_tamiu->cacah_krama_tamiu_id);

        //VALIDASI
        $banjar_adat_id = $banjar_adat_id = session()->get('banjar_adat_id');
        if($cacah_krama_tamiu->banjar_adat_id != $banjar_adat_id){
            return redirect()->back();
        }

        //GET PENDUDUK
        $curr_penduduk = Penduduk::find($cacah_krama_tamiu->penduduk_id);
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
        $krama = $cacah_krama_tamiu;
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = LogPenduduk::with('pekerjaan', 'pendidikan')->find($id_riwayat);
        if($krama->penduduk_id != $penduduk->penduduk_id){
            return redirect()->back();
        }

        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $penduduk = Penduduk::with('pekerjaan', 'pendidikan')->find($krama->penduduk_id);
        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);
        $penduduk->koordinat_alamat = json_decode($penduduk->koordinat_alamat);

        //Asal luar bali
        if($krama->asal == 'luar_bali'){
            $desa_asal = DesaDinas::find($krama->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
            return view('pages.banjar.krama_tamiu.detail_riwayat', compact('krama', 'krama_tamiu', 'penduduk', 'curr_penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
        }else if($krama->asal == 'dalam_bali'){
            $banjar_adat_asal = BanjarAdat::find($krama->banjar_adat_asal_id);
            $desa_adat_asal = DesaAdat::find($banjar_adat_asal->desa_adat_id);
            $kecamatan_asal = Kecamatan::find($desa_adat_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            return view('pages.banjar.krama_tamiu.detail_riwayat', compact('krama', 'krama_tamiu', 'penduduk', 'curr_penduduk', 'desa', 'kecamatan', 'kabupaten', 'provinsi', 'banjar_adat_asal', 'desa_adat_asal', 'kecamatan_asal', 'kabupaten_asal'));
        }
    }

    public function delete($id, Request $request){
        //GET KRAMA DAN KELUARGA
        $krama_tamiu = KramaTamiu::with('cacah_krama_tamiu.penduduk')->find($id);
        $anggota_krama_tamiu = AnggotaKramaTamiu::with('cacah_krama_tamiu.penduduk')->where('krama_tamiu_id', $krama_tamiu->id)->get();

        foreach($anggota_krama_tamiu as $anggota){
            $anggota->status = '0';
            $anggota->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
            $anggota->alasan_keluar = $request->alasan_keluar;
            $anggota->update();

            //NONAKTIFKAN CACAH
            $cacah_anggota = CacahKramaTamiu::find($anggota->cacah_krama_tamiu_id);
            $cacah_anggota->status = '0';
            $cacah_anggota->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
            $cacah_anggota->alasan_keluar = $request->alasan_keluar;
            $cacah_anggota->update();
        }

        $krama_tamiu->status = '0';
        $krama_tamiu->alasan_keluar = $request->alasan_keluar;
        $krama_tamiu->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_keluar));
        $krama_tamiu->update();

        //NONAKTIFKAN CACAH KRAMA MIPIL
        $cacah_krama_tamiu = CacahKramaTamiu::find($krama_tamiu->cacah_krama_tamiu_id);
        $cacah_krama_tamiu->status = '0';
        $cacah_krama_tamiu->tanggal_keluar = date("Y-m-d", strtotime($request->tanggal_keluar));
        $cacah_krama_tamiu->alasan_keluar = $request->alasan_keluar;
        $cacah_krama_tamiu->update();

        return redirect()->back()->with('success', 'Krama Tamiu berhasil dinonaktifkan');
    }
}