<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PerceraianController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $perceraian = Perceraian::with('purusa.penduduk', 'pradana.penduduk')->where(function ($query) use ($banjar_adat_id) {
            $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
        });

        if(isset($request->rentang_waktu)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $perceraian->whereBetween('tanggal_perceraian', [$start_date, $end_date])->get();
        }

        if (isset($request->status)) {
            $perceraian->where('status_perceraian', $request->status);
        }

        $perceraian = $perceraian->orderBy('tanggal_perceraian', 'DESC')->get()->filter(function ($item) {
            $banjar_adat_id = session()->get('banjar_adat_id');
            if(($item->banjar_adat_purusa_id == $banjar_adat_id) && ($item->banjar_adat_pradana_id == $banjar_adat_id)){
                return $item;
            }else if(($item->banjar_adat_purusa_id == $banjar_adat_id) && ($item->banjar_adat_pradana_id != $banjar_adat_id)){
                return $item;
            }else if(($item->banjar_adat_purusa_id != $banjar_adat_id) && ($item->banjar_adat_pradana_id == $banjar_adat_id)){
                if($item->status_perceraian == '1' || $item->status_perceraian == '3'){
                    return $item;
                }
            }
        });

        return DataTables::of($perceraian)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                $return = '';
                if($data->status_perceraian == '0'){
                    $return .= '<span class="badge badge-warning text-wrap px-3 py-1"> Draft </span>';
                }else if($data->status_perceraian == '1'){
                    $return .= '<span class="badge badge-warning text-wrap px-3 py-1"> Menunggu Konfirmasi </span>';
                }else if($data->status_perceraian == '2'){
                    $return .= '<span class="badge badge-danger text-wrap px-3 py-1"> Tidak Terkonfirmasi </span>';
                }else if($data->status_perceraian == '3'){
                    $return .= '<span class="badge badge-success text-wrap px-3 py-1"> Sah </span>';
                }
                return $return;
            })
            ->addColumn('link', function ($data) {
                $return = '';
                if($data->status_perceraian == '0'){
                    $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                    $return .= '<a class="dropdown-item text-warning" href="'.route('banjar-perceraian-edit', $data->id).'"><i class="fas fa-edit mr-2"></i>Edit</a>';
                    $return .= '<button class="dropdown-item text-danger" type="button" onclick="delete_perceraian('.$data->id.')"><i class="fas fa-trash mr-2"></i> Hapus</button>';
                    $return .= '</div></div>';
                }else if($data->status_perceraian == '1'){
                    $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                    $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-perceraian-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                    $return .= '</div></div>';
                }else if($data->status_perceraian == '2'){
                    $return .= '<button class="btn btn-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Peringatan" onclick="tindakan('.$data->id.', \''.$data->nomor_perceraian.'\', \''.$data->alasan_penolakan.'\')"><i class="fas fa-exclamation-triangle"></i></button>';
                }else if($data->status_perceraian == '3'){
                    $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                    $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-perceraian-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                    $return .= '</div></div>';
                }
                return $return;
            })
            ->rawColumns(['status', 'link'])
            ->make(true);
    }

    public function datatable_krama_mipil(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->where('jenis_krama_mipil', 'krama_penuh')->where('kedudukan_krama_mipil', '!=', NULL)->orderBy('tanggal_registrasi', 'DESC')->get()
        ->filter(function ($item){
            $pasangan = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->where(function ($query) {
                $query->where('status_hubungan', 'istri')
                    ->orWhere('status_hubungan', 'suami');
            })->count();
            if($pasangan>0){
                return $item;
            }
        })
        ->map(function ($item){
            $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
            return $item;
        });

        return Datatables::of($kramas)
        ->addIndexColumn()
        ->addColumn('anggota', function ($data) {
            $return = '<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>';
            return $return;
        })
        ->addColumn('link', function ($data) {
            $nama = '';
            if($data->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$data->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$data->cacah_krama_mipil->penduduk->nama;
            if($data->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$data->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_mipil('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function datatable_krama_mipil_baru_krama_mipil(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        if (isset($request->banjar_adat_krama_mipil)) {
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $request->banjar_adat_krama_mipil)->where('status', '1')->where('jenis_krama_mipil', 'krama_penuh')->where('kedudukan_krama_mipil', '!=', NULL)->where('id', '!=', $request->krama_mipil_pasangan)->orderBy('tanggal_registrasi', 'DESC')->get()
            ->map(function ($item){
                $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
                return $item;
            });
        }else{
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->where('jenis_krama_mipil', 'krama_penuh')->where('kedudukan_krama_mipil', '!=', NULL)->where('id', '!=', $request->krama_mipil_pasangan)->orderBy('tanggal_registrasi', 'DESC')->get()
            ->filter(function ($item) use ($request){

                if (isset($request->krama_mipil_saat_ini)) {
                    if($item->id != $request->krama_mipil_saat_ini){
                        return $item;
                    }
                }

            })
            ->map(function ($item){
                $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
                return $item;
            });
        }


        return Datatables::of($kramas)
        ->addIndexColumn()
        ->addColumn('anggota', function ($data) {
            $return = '<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>';
            return $return;
        })
        ->addColumn('link', function ($data) {
            $nama = '';
            if($data->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$data->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$data->cacah_krama_mipil->penduduk->nama;
            if($data->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$data->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_mipil_baru_krama_mipil('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function datatable_krama_mipil_baru_pasangan(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        if (isset($request->banjar_adat_pasangan)) {
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $request->banjar_adat_pasangan)->where('status', '1')->where('jenis_krama_mipil', 'krama_penuh')->where('kedudukan_krama_mipil', '!=', NULL)->where('id', '!=', $request->krama_mipil_krama_mipil)->orderBy('tanggal_registrasi', 'DESC')->get()
            ->map(function ($item){
                $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
                return $item;
            });
        }else{
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->where('jenis_krama_mipil', 'krama_penuh')->where('kedudukan_krama_mipil', '!=', NULL)->where('id', '!=', $request->krama_mipil_krama_mipil)->orderBy('tanggal_registrasi', 'DESC')->get()
            ->filter(function ($item) use ($request){

                if (isset($request->krama_mipil_saat_ini)) {
                    if($item->id != $request->krama_mipil_saat_ini){
                        return $item;
                    }
                }

            })
            ->map(function ($item){
                $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
                return $item;
            });
        }


        return Datatables::of($kramas)
        ->addIndexColumn()
        ->addColumn('anggota', function ($data) {
            $return = '<button type="button" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></button>';
            return $return;
        })
        ->addColumn('link', function ($data) {
            $nama = '';
            if($data->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$data->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$data->cacah_krama_mipil->penduduk->nama;
            if($data->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$data->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_mipil_baru_pasangan('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function pilih_krama_mipil($id){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $krama_mipil->kedudukan_krama_mipil = ucwords($krama_mipil->kedudukan_krama_mipil);

        $pasangan = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where(function ($query) {
            $query->where('status_hubungan', 'istri')
                ->orWhere('status_hubungan', 'suami');
        })->get()->map(function($item){
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
            return $item;
        });

        $anggota_krama_mipil =  AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where('status_hubungan', '!=', 'istri')->where('status_hubungan', '!=', 'suami')
        ->get()->map(function($item){
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

            return $item;
        });

        return response()->json([
            'krama_mipil' => $krama_mipil,
            'pasangan' => $pasangan,
            'anggota_krama_mipil' => $anggota_krama_mipil
        ]);
    }

    public function index(){
        return view ('pages.banjar.perceraian.perceraian');
    }

    public function create(){
        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
        return view ('pages.banjar.perceraian.create', compact('provinsis', 'kabupatens'));
    }

    public function store($status, Request $request){
        //Validator
        $validator = Validator::make($request->all(), [
            'tanggal_perceraian' => 'required',
            'nama_pemuput' => 'required|regex:/^[a-zA-Z\s]*$/|max:100',
            'nomor_bukti_perceraian' => 'required|unique:tb_perceraian,nomor_perceraian|nullable|max:50',
            'file_bukti_perceraian' => 'required',
            'nomor_akta_perceraian' => 'unique:tb_perceraian|nullable|max:21',
            'file_akta_perceraian' => 'required_with:nomor_akta_perceraian',

            'krama_mipil' => 'required',
            'status_krama_mipil' => 'required',
            'pasangan' => 'required',
            'status_pasangan' => 'required'
        ],[
            'tanggal_perceraian.regex' => "Tanggal Perceraian wajib diisi",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'nama_pemuput.regex' => "Nama Pemuput hanya boleh mengandung huruf",
            'nomor_bukti_perceraian.required' => "Nomor Bukti Perceraian wajib diisi",
            'nomor_bukti_perceraian.unique' => "Nomor Bukti Perceraian telah terdaftar",
            'nomor_bukti_perceraian.max' => "Nomor Bukti Perceraian maksimal terdiri dari 50 karakter",
            'file_bukti_perceraian.required' => "File Bukti Perceraian wajib diisi",
            'nomor_akta_perceraian.unique' => "Nomor Akta Perceraian telah terdaftar",
            'nomor_akta_perceraian.max' => "Nomor Akta Perceraian maksimal terdiri dari 21 karakter",
            'file_akta_perceraian.required_with' => "File Akta Perceraian wajib diisi",
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'status_krama_mipil.required' => "Status Krama Mipil wajib dipilih",
            'pasangan.required' => "Pasangan wajib dipilih",
            'status_pasangan.required' => "Status Pasangan wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal
        $tanggal_cerai = date("Y-m-d", strtotime($request->tanggal_perceraian));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_cerai > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_perceraian' => 'Tanggal perceraian tidak boleh melebihi tanggal sekarang']);
        }

        //Get Master Data
        $krama_mipil = KramaMipil::find($request->krama_mipil);
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        //Convert Nomor Perceraian
        $convert_nomor_perceraian = str_replace("/","-",$request->nomor_bukti_perceraian);

        //Generate Array Anggota
        $arr_status_anggota = json_encode($request->status_anggota_);
        if(empty($arr_status_anggota)){
            $arr_status_anggota = NULL;
        }

        //Mencari tau siapa purusa (Krama atau Pasangannya)
        if($krama_mipil->kedudukan_krama_mipil == 'purusa'){
            $is_purusa = 'krama_mipil';
            $purusa = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
            $pradana = CacahKramaMipil::find($request->pasangan);
        }else{
            $is_purusa = 'pasangan';
            $purusa = CacahKramaMipil::find($request->pasangan);
            $pradana = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        }

        //Insert Data Perceraian
        $perceraian = new Perceraian();
        $perceraian->nomor_perceraian = $request->nomor_bukti_perceraian;
        $perceraian->nomor_akta_perceraian = $request->nomor_akta_perceraian;
        $perceraian->krama_mipil_id = $request->krama_mipil;
        $perceraian->tanggal_perceraian = date("Y-m-d", strtotime($request->tanggal_perceraian));
        $perceraian->nama_pemuput = $request->nama_pemuput;
        $perceraian->keterangan = $request->keterangan;
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

        //Jika Purusanya adalah Krama Mipil (Kepala Keluarga)
        if($is_purusa == 'krama_mipil'){
            $perceraian->status_purusa = $request->status_krama_mipil;
            $perceraian->status_pradana = $request->status_pasangan;

            //Status Purusa
            if($request->status_krama_mipil == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->krama_mipil_baru_purusa_id = $request->krama_mipil_baru_krama_mipil;
                $perceraian->status_hubungan_baru_purusa = $request->status_hubungan_baru_krama_mipil;
            }

            //Status Pradana
            if($request->status_pasangan == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->banjar_adat_pradana_id = $banjar_adat->id;
                $perceraian->desa_adat_pradana_id = $desa_adat->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_pasangan;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_pasangan;
            }else if($request->status_pasangan == 'keluar_banjar'){
                //Get Asal Pasangan/Pradana
                $banjar_adat_pasangan = BanjarAdat::find($request->banjar_adat_pasangan);
                $desa_adat_pasangan = DesaAdat::find($banjar_adat_pasangan->desa_adat_id);
                $perceraian->banjar_adat_pradana_id = $banjar_adat_pasangan->id;
                $perceraian->desa_adat_pradana_id = $desa_adat_pasangan->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_pasangan;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_pasangan;
            }else{
                $perceraian->desa_baru_pradana_id = $request->desa_pasangan_keluar;
            }
        }
        //Jika Purusanya adalah Pasangannya (Anggota Keluarga)
        else{
            $perceraian->status_purusa = $request->status_pasangan;
            $perceraian->status_pradana = $request->status_krama_mipil;

            //Status Purusa
            if($request->status_pasangan == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->krama_mipil_baru_purusa_id = $request->krama_mipil_baru_pasangan;
                $perceraian->status_hubungan_baru_purusa = $request->status_hubungan_baru_pasangan;
            }

            //Status Pradana
            if($request->status_krama_mipil == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->banjar_adat_pradana_id = $banjar_adat->id;
                $perceraian->desa_adat_pradana_id = $desa_adat->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_krama_mipil;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_krama_mipil;
            }else if($request->status_krama_mipil == 'keluar_banjar'){
                //Get Asal Pasangan/Pradana
                $banjar_adat_krama_mipil = BanjarAdat::find($request->banjar_adat_krama_mipil);
                $desa_adat_krama_mipil = DesaAdat::find($banjar_adat_krama_mipil->desa_adat_id);
                $perceraian->banjar_adat_pradana_id = $banjar_adat_krama_mipil->id;
                $perceraian->desa_adat_pradana_id = $desa_adat_krama_mipil->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_krama_mipil;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_krama_mipil;
            }else{
                $perceraian->desa_baru_pradana_id = $request->desa_pasangan_keluar;
            }
        }

        $perceraian->save();

        if($status == 0){
            return redirect()->route('banjar-perceraian-home')->with('success', 'Draft Perceraian Berhasil Ditambahkan');
        }else{
            //Update Data Perceraian Terutama Status!
            if($perceraian->status_pradana == 'keluar_banjar'){
                $perceraian->status_perceraian = '1';
            }else{
                $perceraian->status_perceraian = '3';
            }
            $perceraian->update();

            if($perceraian->status_pradana == 'keluar_banjar'){
                $perceraian->status_perceraian = '1';
                $perceraian->update();

                $notifikasi = new Notifikasi();
                $notifikasi->notif_create_perceraian_beda_banjar_adat($perceraian->id);
                return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil Ditambahkan');
            }else{
                if($is_purusa == 'krama_mipil'){
                    $this->krama_mipil_purusa($perceraian);
                    return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil Ditambahkan');
                }else{
                    $this->krama_mipil_pradana($perceraian);
                    return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil Ditambahkan');
                }
            }
        }
    }

    public function edit($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $perceraian = Perceraian::find($id);

        //Validasi Banjar dan Status
        if($perceraian->banjar_adat_purusa_id != $banjar_adat_id){
            return redirect()->back();
        }

        //Get Data Krama Mipil
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($perceraian->krama_mipil_id);
        $krama_mipil = Helper::generate_nama_krama_mipil($krama_mipil);

        //Mencari tau siapa purusa (Krama atau Pasangannya)
        if($krama_mipil->kedudukan_krama_mipil == 'purusa'){
            $is_purusa = 'krama_mipil';
        }else{
            $is_purusa = 'pasangan';
        }
        $purusa = CacahKramaMipil::find($perceraian->purusa_id);
        $pradana = CacahKramaMipil::find($perceraian->pradana_id);

        //Get Master Data
        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', '51')->get();

        //Get Anggota Keluarga
        $daftar_status_anggota = json_decode($perceraian->status_anggota);
        $anggota_krama_mipil =  AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where('status_hubungan', '!=', 'istri')->where('status_hubungan', '!=', 'suami')
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
                    $item->status_baru = $value;
                }
            }
            return $item;
        });

        //Get Data Pasangan
        $pasangan = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->where(function ($query) {
            $query->where('status_hubungan', 'istri')
                ->orWhere('status_hubungan', 'suami');
        })->get()->map(function($item){
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
            return $item;
        });

        //Get Detail Data Purusa
        if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_baru'){
            $krama_mipil_baru_purusa = KramaMipil::with('cacah_krama_mipil.penduduk')->find($perceraian->krama_mipil_baru_purusa_id);
            $krama_mipil_baru_purusa = Helper::generate_nama_krama_mipil($krama_mipil_baru_purusa);
        }

        //Get Detail Data Pradana
        if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
            $krama_mipil_baru_pradana = KramaMipil::with('cacah_krama_mipil.penduduk')->find($perceraian->krama_mipil_baru_pradana_id);
            $krama_mipil_baru_pradana = Helper::generate_nama_krama_mipil($krama_mipil_baru_pradana);
        }else if($perceraian->status_pradana == 'keluar_banjar'){
            $krama_mipil_baru_pradana = KramaMipil::with('cacah_krama_mipil.penduduk')->find($perceraian->krama_mipil_baru_pradana_id);
            $krama_mipil_baru_pradana = Helper::generate_nama_krama_mipil($krama_mipil_baru_pradana);

            $banjar_adat_pradana = BanjarAdat::find($perceraian->banjar_adat_pradana_id);
            $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);
            $kecamatan_pradana = Kecamatan::find($desa_adat_pradana->kecamatan_id);
            $kabupaten_pradana = Kabupaten::find($kecamatan_pradana->kabupaten_id);

            $banjar_adat_pradanas = BanjarAdat::where('desa_adat_id', $desa_adat_pradana->id)->get();
            $desa_adat_pradanas = DesaAdat::where('kecamatan_id', $kecamatan_pradana->id)->get();
            $kecamatan_pradanas = Kecamatan::where('kabupaten_id', $kabupaten_pradana->id)->get();
        }else{
            $desa_pradana = DesaDinas::find($perceraian->desa_baru_pradana_id);
            $kecamatan_pradana = Kecamatan::find($desa_pradana->kecamatan_id);
            $kabupaten_pradana = Kabupaten::find($kecamatan_pradana->kabupaten_id);
            $provinsi_pradana = Provinsi::find($kabupaten_pradana->provinsi_id);

            $desa_pradanas = DesaDinas::where('kecamatan_id', $kecamatan_pradana->id)->get();
            $kecamatan_pradanas = Kecamatan::where('kabupaten_id', $kabupaten_pradana->id)->get();
            $kabupaten_pradanas = Kabupaten::where('provinsi_id', $provinsi_pradana->id)->get();
        }

        //Return Data ke View Sesuai Kebutuhan
        if($perceraian->status_purusa == 'tetap_di_banjar_dan_kk_baru'){
            if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
                return view ('pages.banjar.perceraian.edit', compact(
                    'perceraian', 'krama_mipil', 'pasangan', 'anggota_krama_mipil', 'provinsis', 'kabupatens',
                    'krama_mipil_baru_purusa', 'krama_mipil_baru_pradana'));
            }else if($perceraian->status_pradana == 'keluar_banjar'){
                return view ('pages.banjar.perceraian.edit', compact(
                    'perceraian', 'krama_mipil', 'pasangan', 'anggota_krama_mipil', 'provinsis', 'kabupatens',
                    'krama_mipil_baru_purusa', 'krama_mipil_baru_pradana',
                    'banjar_adat_pradana', 'desa_adat_pradana', 'kecamatan_pradana', 'kabupaten_pradana',
                    'banjar_adat_pradanas', 'desa_adat_pradanas', 'kecamatan_pradanas'));
            }else{
                return view ('pages.banjar.perceraian.edit', compact(
                    'perceraian', 'krama_mipil', 'pasangan', 'anggota_krama_mipil', 'provinsis', 'kabupatens',
                    'krama_mipil_baru_purusa',
                    'desa_pradana', 'kecamatan_pradana', 'kabupaten_pradana', 'provinsi_pradana',
                    'desa_pradanas', 'kecamatan_pradanas', 'kabupaten_pradanas'));
            }
        }else{
            if($perceraian->status_pradana == 'tetap_di_banjar_dan_kk_baru'){
                return view ('pages.banjar.perceraian.edit', compact(
                    'perceraian', 'krama_mipil', 'pasangan', 'anggota_krama_mipil', 'provinsis', 'kabupatens',
                    'krama_mipil_baru_pradana'));
            }else if($perceraian->status_pradana == 'keluar_banjar'){
                return view ('pages.banjar.perceraian.edit', compact(
                    'perceraian', 'krama_mipil', 'pasangan', 'anggota_krama_mipil', 'provinsis', 'kabupatens',
                    'krama_mipil_baru_pradana',
                    'banjar_adat_pradana', 'desa_adat_pradana', 'kecamatan_pradana', 'kabupaten_pradana',
                    'banjar_adat_pradanas', 'desa_adat_pradanas', 'kecamatan_pradanas'));
            }else{
                return view ('pages.banjar.perceraian.edit', compact(
                    'perceraian', 'krama_mipil', 'pasangan', 'anggota_krama_mipil', 'provinsis', 'kabupatens',
                    'desa_pradana', 'kecamatan_pradana', 'kabupaten_pradana', 'provinsi_pradana',
                    'desa_pradanas', 'kecamatan_pradanas', 'kabupaten_pradanas'));
            }
        }
    }

    public function update($id, $status, Request $request){
        $perceraian = Perceraian::find($id);
        //Validator
        $validator = Validator::make($request->all(), [
            'tanggal_perceraian' => 'required',
            'nama_pemuput' => 'required|regex:/^[a-zA-Z\s]*$/|max:100',
            'nomor_bukti_perceraian' => 'required|unique:tb_perceraian,nomor_perceraian|nullable|max:50',
            'nomor_bukti_perceraian' => [
                Rule::unique('tb_perceraian', 'nomor_perceraian')->ignore($perceraian->id),
            ],
            'nomor_akta_perceraian' => 'unique:tb_perceraian|nullable|max:18',
            'nomor_akta_perceraian' => [
                Rule::unique('tb_perceraian')->ignore($perceraian->id),
            ],
            'krama_mipil' => 'required',
            'status_krama_mipil' => 'required',
            'pasangan' => 'required',
            'status_pasangan' => 'required'
        ],[
            'tanggal_perceraian.regex' => "Tanggal Perceraian wajib diisi",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'nama_pemuput.regex' => "Nama Pemuput hanya boleh mengandung huruf",
            'nomor_bukti_perceraian.required' => "Nomor Bukti Perceraian wajib diisi",
            'nomor_bukti_perceraian.unique' => "Nomor Bukti Perceraian telah terdaftar",
            'nomor_bukti_perceraian.max' => "Nomor Bukti Perceraian maksimal terdiri dari 50 karakter",
            'file_bukti_perceraian.required' => "File Bukti Perceraian wajib diisi",
            'nomor_akta_perceraian.unique' => "Nomor Akta Perceraian telah terdaftar",
            'nomor_akta_perceraian.max' => "Nomor Akta Perceraian maksimal terdiri dari 18 karakter",
            'file_akta_perceraian.required_with' => "File Akta Perceraian wajib diisi",
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'status_krama_mipil.required' => "Status Krama Mipil wajib dipilih",
            'pasangan.required' => "Pasangan wajib dipilih",
            'status_pasangan.required' => "Status Pasangan wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal
        $tanggal_cerai = date("Y-m-d", strtotime($request->tanggal_perceraian));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_cerai > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_perceraian' => 'Tanggal perceraian tidak boleh melebihi tanggal sekarang']);
        }

        //Get Master Data
        $krama_mipil = KramaMipil::find($request->krama_mipil);
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        //Convert Nomor Perceraian
        $convert_nomor_perceraian = str_replace("/","-",$request->nomor_bukti_perceraian);

        //Generate Array Anggota
        $arr_status_anggota = json_encode($request->status_anggota_);
        if(empty($arr_status_anggota)){
            $arr_status_anggota = NULL;
        }

        //Mencari tau siapa purusa (Krama atau Pasangannya)
        if($krama_mipil->kedudukan_krama_mipil == 'purusa'){
            $is_purusa = 'krama_mipil';
            $purusa = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
            $pradana = CacahKramaMipil::find($request->pasangan);
        }else{
            $is_purusa = 'pasangan';
            $purusa = CacahKramaMipil::find($request->pasangan);
            $pradana = CacahKramaMipil::find($krama_mipil->cacah_krama_mipil_id);
        }

        //Update Data Perceraian
        $perceraian->nomor_perceraian = $request->nomor_bukti_perceraian;
        $perceraian->nomor_akta_perceraian = $request->nomor_akta_perceraian;
        $perceraian->krama_mipil_id = $request->krama_mipil;
        $perceraian->tanggal_perceraian = date("Y-m-d", strtotime($request->tanggal_perceraian));
        $perceraian->nama_pemuput = $request->nama_pemuput;
        $perceraian->keterangan = $request->keterangan;
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

        //Jika Purusanya adalah Krama Mipil (Kepala Keluarga)
        if($is_purusa == 'krama_mipil'){
            $perceraian->status_purusa = $request->status_krama_mipil;
            $perceraian->status_pradana = $request->status_pasangan;

            //Status Purusa
            if($request->status_krama_mipil == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->krama_mipil_baru_purusa_id = $request->krama_mipil_baru_krama_mipil;
                $perceraian->status_hubungan_baru_purusa = $request->status_hubungan_baru_krama_mipil;
            }

            //Status Pradana
            if($request->status_pasangan == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->banjar_adat_pradana_id = $banjar_adat->id;
                $perceraian->desa_adat_pradana_id = $desa_adat->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_pasangan;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_pasangan;
            }else if($request->status_pasangan == 'keluar_banjar'){
                //Get Asal Pasangan/Pradana
                $banjar_adat_pasangan = BanjarAdat::find($request->banjar_adat_pasangan);
                $desa_adat_pasangan = DesaAdat::find($banjar_adat_pasangan->desa_adat_id);
                $perceraian->banjar_adat_pradana_id = $banjar_adat_pasangan->id;
                $perceraian->desa_adat_pradana_id = $desa_adat_pasangan->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_pasangan;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_pasangan;
            }else{
                $perceraian->desa_baru_pradana_id = $request->desa_pasangan_keluar;
            }
        }
        //Jika Purusanya adalah Pasangannya (Anggota Keluarga)
        else{
            $perceraian->status_purusa = $request->status_pasangan;
            $perceraian->status_pradana = $request->status_krama_mipil;

            //Status Purusa
            if($request->status_pasangan == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->krama_mipil_baru_purusa_id = $request->krama_mipil_baru_pasangan;
                $perceraian->status_hubungan_baru_purusa = $request->status_hubungan_baru_pasangan;
            }

            //Status Pradana
            if($request->status_krama_mipil == 'tetap_di_banjar_dan_kk_baru'){
                $perceraian->banjar_adat_pradana_id = $banjar_adat->id;
                $perceraian->desa_adat_pradana_id = $desa_adat->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_krama_mipil;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_krama_mipil;
            }else if($request->status_krama_mipil == 'keluar_banjar'){
                //Get Asal Pasangan/Pradana
                $banjar_adat_krama_mipil = BanjarAdat::find($request->banjar_adat_krama_mipil);
                $desa_adat_krama_mipil = DesaAdat::find($banjar_adat_krama_mipil->desa_adat_id);
                $perceraian->banjar_adat_pradana_id = $banjar_adat_krama_mipil->id;
                $perceraian->desa_adat_pradana_id = $desa_adat_krama_mipil->id;
                $perceraian->krama_mipil_baru_pradana_id = $request->krama_mipil_baru_krama_mipil;
                $perceraian->status_hubungan_baru_pradana = $request->status_hubungan_baru_krama_mipil;
            }else{
                $perceraian->desa_baru_pradana_id = $request->desa_pasangan_keluar;
            }
        }

        $perceraian->update();

        if($status == 0){
            return redirect()->route('banjar-perceraian-home')->with('success', 'Draft Perceraian Berhasil Diperbaharui');
        }else{
            //Update Data Perceraian Terutama Status!
            if($perceraian->status_pradana == 'keluar_banjar'){
                $perceraian->status_perceraian = '1';
            }else{
                $perceraian->status_perceraian = '3';
            }
            $perceraian->update();

            if($perceraian->status_pradana == 'keluar_banjar'){
                $perceraian->status_perceraian = '1';
                $perceraian->update();

                if($perceraian->alasan_penolakan == ''){
                    $notifikasi = new Notifikasi();
                    $notifikasi->notif_create_perceraian_beda_banjar_adat($perceraian->id);
                }else{
                    $notifikasi = new Notifikasi();
                    $notifikasi->notif_edit_perceraian_beda_banjar_adat($perceraian->id);
                }


                return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil diperbaharui');
            }else{
                if($is_purusa == 'krama_mipil'){
                    $this->krama_mipil_purusa($perceraian);
                    return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil Diperbaharui');
                }else{
                    $this->krama_mipil_pradana($perceraian);
                    return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil Diperbaharui');
                }
            }
        }
    }

    public function destroy($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $perceraian = Perceraian::find($id);

        //Validasi Banjar dan Status
        if($perceraian->banjar_adat_purusa_id != $banjar_adat_id){
            return redirect()->back();
        }

        if($perceraian->status_perceraian == '0'){
            $perceraian->delete();
            return redirect()->back()->with('success', 'Draft Perceraian berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Perceraian yang telah sah tidak dapat dihapus');
        }


    }

    public function detail($id){
        $perceraian = Perceraian::find($id);
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
                    $item->status_baru = $value;
                }
            }
            return $item;
        });

        return view('pages.banjar.perceraian.detail', compact('perceraian', 'anggota_krama_mipil'));
    }

    public function tolak_perceraian($id, Request $request){
        $validator = Validator::make($request->all(), [
            'alasan_penolakan' => 'required',
        ],[
            'alasan_penolakan.required' => "Alasan wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $perceraian = Perceraian::find($id);
        $perceraian->alasan_penolakan = $request->alasan_penolakan;
        $perceraian->status_perceraian = '2';
        $perceraian->update();

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();
        $notifikasi->notif_tolak_perceraian_beda_banjar_adat($perceraian->id);

        return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian berhasil diperbaharui');
    }

    public function konfirmasi_perceraian($id){
        //Get Data
        $perceraian = Perceraian::find($id);
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

        //Logic Perkawinan
        if($is_purusa == 'krama_mipil'){
            $this->krama_mipil_purusa($perceraian);
            return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil Dikonfirmasi');
        }else{
            $this->krama_mipil_pradana($perceraian);
            return redirect()->route('banjar-perceraian-home')->with('success', 'Perceraian Berhasil Dikonfirmasi');
        }
    }

    //Fungsi Helper Untuk Purusa Pradana
    private function krama_mipil_purusa($perceraian){
        //Get Master Data
        $krama_mipil = KramaMipil::find($perceraian->krama_mipil_id);
        $banjar_adat_id = session()->get('banjar_adat_id');
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
        $banjar_adat_id = session()->get('banjar_adat_id');
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
