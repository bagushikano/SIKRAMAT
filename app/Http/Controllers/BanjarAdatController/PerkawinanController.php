<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PerkawinanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $perkawinan = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->where(function ($query) use ($banjar_adat_id) {
            $query->where('banjar_adat_purusa_id', $banjar_adat_id)
                ->orWhere('banjar_adat_pradana_id', $banjar_adat_id);
        });

        if(isset($request->rentang_waktu)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $perkawinan->whereBetween('tanggal_perkawinan', [$start_date, $end_date])->get();
        }

        if (isset($request->status)) {
            $perkawinan->where('status_perkawinan', $request->status);
        }

        if (isset($request->jenis_perkawinan)) {
            $perkawinan->whereIn('jenis_perkawinan', $request->jenis_perkawinan);
        }

        $perkawinan = $perkawinan->orderBy('tanggal_perkawinan', 'DESC')->get()->filter(function ($item) {
            $banjar_adat_id = session()->get('banjar_adat_id');
            if($item->jenis_perkawinan == 'satu_banjar_adat'){
                $item->jenis = 'Satu Banjar Adat';
                return $item;
            }else if($item->jenis_perkawinan == 'beda_banjar_adat'){
                if($item->banjar_adat_purusa_id == $banjar_adat_id){
                    $item->jenis = 'Masuk Banjar Adat';
                    return $item;
                }else if($item->banjar_adat_pradana_id == $banjar_adat_id){
                    if($item->status_perkawinan == '0' || $item->status_perkawinan == '1' || $item->status_perkawinan == '3'){
                        $item->jenis = 'Keluar Banjar Adat';
                        return $item;
                    }
                }
            }else if($item->jenis_perkawinan == 'campuran_masuk'){
                $item->jenis = 'Campuran Masuk';
                return $item;
            }else if($item->jenis_perkawinan == 'campuran_keluar'){
                $item->jenis = 'Campuran Keluar';
                return $item;
            }
        });

        return DataTables::of($perkawinan)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                $return = '';
                if($data->status_perkawinan == '0'){
                    $return .= '<span class="badge badge-warning text-wrap px-3 py-1"> Draft </span>';
                }else if($data->status_perkawinan == '1'){
                    $return .= '<span class="badge badge-info text-wrap px-3 py-1"> Terkonfirmasi </span>';
                }else if($data->status_perkawinan == '2'){
                    $return .= '<span class="badge badge-danger text-wrap px-3 py-1"> Tidak Terkonfirmasi </span>';
                }else if($data->status_perkawinan == '3'){
                    $return .= '<span class="badge badge-success text-wrap px-3 py-1"> Sah </span>';
                }
                return $return;
            })
            ->addColumn('link', function ($data) {
                $return = '';
                if($data->jenis_perkawinan == 'satu_banjar_adat' || $data->jenis_perkawinan == 'campuran_masuk' || $data->jenis_perkawinan == 'campuran_keluar'){
                    if($data->status_perkawinan == '0' || $data->status_perkawinan == '1'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-warning" href="'.route('banjar-perkawinan-edit', $data->id).'"><i class="fas fa-edit mr-2"></i>Edit</a>';
                        $return .= '<button class="dropdown-item text-danger" onclick="delete_perkawinan('.$data->id.')"><i class="fas fa-trash mr-2"></i> Hapus</button>';
                        $return .= '</div></div>';
                    }else if($data->status_perkawinan == '3'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-perkawinan-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }
                }else if($data->jenis == 'Masuk Banjar Adat'){
                    if($data->status_perkawinan == '0'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-warning" href="'.route('banjar-perkawinan-edit', $data->id).'"><i class="fas fa-edit mr-2"></i>Edit</a>';
                        $return .= '<button class="dropdown-item text-danger" type="button" onclick="delete_perkawinan('.$data->id.')"><i class="fas fa-trash mr-2"></i> Hapus</button>';
                        $return .= '</div></div>';
                    }else if($data->status_perkawinan == '1'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-perkawinan-masuk-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }else if($data->status_perkawinan == '2'){
                        $return .= '<button class="btn btn-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Peringatan" onclick="tindakan('.$data->id.', \''.$data->nomor_perkawinan.'\', \''.$data->alasan_penolakan.'\')"><i class="fas fa-exclamation-triangle"></i></button>';
                    }else if($data->status_perkawinan == '3'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-perkawinan-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }
                }else if($data->jenis == 'Keluar Banjar Adat'){
                    if($data->status_perkawinan == '0'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-perkawinan-keluar-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }else if($data->status_perkawinan == '1' || $data->status_perkawinan == '3'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-perkawinan-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';                    }
                }
                return $return;
            })
            ->rawColumns(['status', 'link'])
            ->make(true);
    }

    public function datatable_purusa(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $krama_pradana = Perkawinan::pluck('pradana_id')->toArray();
        $kramas = CacahKramaMipil::with('penduduk', 'tempekan')->where('status', '1')->whereNotIn('id', $krama_pradana);
        if(isset($request->jk_pradana)){
            $kramas->whereHas('penduduk', function ($query) use ($request) {
                return $query->where('jenis_kelamin', '!=', $request->jk_pradana);
            });
        }
        if (isset($request->banjar_adat_id)) {
            $kramas->where('banjar_adat_id', $request->banjar_adat_id);
        }else{
            $kramas->where('banjar_adat_id', $banjar_adat_id);
        }

        if(isset($request->pradana)){
            $pradana = CacahKramaMipil::with('penduduk')->find($request->pradana);
            $jenis_kelamin_pradana = $pradana->penduduk->jenis_kelamin;
            $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('id')->toArray();
            $arr_krama_istri_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->where('status', '1')->where('status_hubungan', 'istri')->pluck('cacah_krama_mipil_id')->toArray();
            $kramas = $kramas->get()->filter(function ($item) use ($pradana, $jenis_kelamin_pradana, $arr_krama_istri_id){
                if($item->id != $pradana->id){
                    if($item->penduduk->jenis_kelamin != $jenis_kelamin_pradana){
                        if(!in_array($item->id, $arr_krama_istri_id)){
                            return $item;
                        }
                    }
                }
            });
        }else{
            $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('id')->toArray();
            $arr_krama_istri_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->where('status', '1')->where('status_hubungan', 'istri')->pluck('cacah_krama_mipil_id')->toArray();
            $kramas = $kramas->get()->filter(function ($item) use ($arr_krama_istri_id){
                if(!in_array($item->id, $arr_krama_istri_id)){
                    return $item;
                }
            });
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
                $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_purusa('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }

    public function datatable_pradana(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $krama_kk = KramaMipil::pluck('cacah_krama_mipil_id')->toArray();
        $kramas = CacahKramaMipil::with('penduduk', 'tempekan')->where('status', '1')->whereNotIn('id', $krama_kk);
        if (isset($request->banjar_adat_id)) {
            $kramas->where('banjar_adat_id', $request->banjar_adat_id);
            $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $request->banjar_adat_id)->where('status', '1')->pluck('id')->toArray();
            $arr_krama_istri_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->where('status', '1')->where('status_hubungan', 'istri')->pluck('cacah_krama_mipil_id')->toArray();
            $kramas->whereNotIn('id', $arr_krama_istri_id);
        }else{
            $kramas->where('banjar_adat_id', $banjar_adat_id);
        }

        if (isset($request->current_pradana_id)) {
            $krama_pradana = Perkawinan::where('pradana_id', '!=', $request->current_pradana_id)->pluck('pradana_id')->toArray();
            $kramas->whereNotIn('id', $krama_pradana);
        }else{
            $krama_pradana = Perkawinan::pluck('pradana_id')->toArray();
            $kramas->whereNotIn('id', $krama_pradana);
        }

        if(isset($request->purusa)){
            $purusa_as_kk = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
            if($purusa_as_kk){
                $arr_anggota_keluarga_id = AnggotaKramaMipil::where('krama_mipil_id', $purusa_as_kk->id)->where('status', '1')->pluck('cacah_krama_mipil_id')->toArray();
            }else{
                $purusa_as_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                $kk_purusa = KramaMipil::find($purusa_as_anggota->krama_mipil_id);
                $arr_anggota_keluarga_id = AnggotaKramaMipil::where('krama_mipil_id', $kk_purusa->id)->where('status', '1')->pluck('cacah_krama_mipil_id')->toArray();
            }


            $purusa = CacahKramaMipil::with('penduduk')->find($request->purusa);
            $jenis_kelamin_purusa = $purusa->penduduk->jenis_kelamin;
            $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('id')->toArray();
            $arr_krama_istri_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->where('status', '1')->where('status_hubungan', 'istri')->pluck('cacah_krama_mipil_id')->toArray();
            $kramas = $kramas->whereNotIn('id', $arr_anggota_keluarga_id)->get()->filter(function ($item) use ($purusa, $jenis_kelamin_purusa, $arr_krama_istri_id){
                if($item->id != $purusa->id){
                    if($item->penduduk->jenis_kelamin != $jenis_kelamin_purusa){
                        if(!in_array($item->id, $arr_krama_istri_id)){
                            return $item;
                        }
                    }
                }
            });
        }else{
            $arr_krama_mipil_id = KramaMipil::where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->pluck('id')->toArray();
            $arr_krama_istri_id = AnggotaKramaMipil::whereIn('krama_mipil_id', $arr_krama_mipil_id)->where('status', '1')->where('status_hubungan', 'istri')->pluck('cacah_krama_mipil_id')->toArray();
            $kramas = $kramas->get()->filter(function ($item) use ($arr_krama_istri_id){
                if(!in_array($item->id, $arr_krama_istri_id)){
                    return $item;
                }
            });
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
                $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_pradana('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
                return $return;
            })
            ->rawColumns(['link'])
            ->make(true);
    }

    public function get_calon_kepala_keluarga($purusa_id, $pradana_id){
        //COLLECTION DECLARE
        $calon_kepala_keluarga = new Collection();

        //GET PURUSA
        $purusa = CacahKramaMipil::with('penduduk')->find($purusa_id);
        $nama = '';
        if($purusa->penduduk->gelar_depan != NULL){
            $nama = $nama.$purusa->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$purusa->penduduk->nama;
        if($purusa->penduduk->gelar_belakang != NULL){
            $nama = $nama.', '.$purusa->penduduk->gelar_belakang;
        }
        $purusa->penduduk->nama = $nama;
        $calon_kepala_keluarga->push($purusa);

        //GET PRADANA
        $pradana = CacahKramaMipil::with('penduduk')->find($pradana_id);
        if($pradana){
            $nama = '';
            if($pradana->penduduk->gelar_depan != NULL){
                $nama = $nama.$pradana->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$pradana->penduduk->nama;
            if($pradana->penduduk->gelar_belakang != NULL){
                $nama = $nama.', '.$pradana->penduduk->gelar_belakang;
            }
            $pradana->penduduk->nama = $nama;
            $calon_kepala_keluarga->push($pradana);
        }

        return response()->json([
            $calon_kepala_keluarga
        ]);
    }

    public function index(){
        return view('pages.banjar.perkawinan.perkawinan');
    }

    public function create($jenis_perkawinan){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        if($jenis_perkawinan == 'satu_banjar_adat'){
            return view('pages.banjar.perkawinan.create_satu_banjar_adat');
        }else if($jenis_perkawinan == 'beda_banjar_adat'){
            $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
            return view('pages.banjar.perkawinan.create_beda_banjar_adat', compact('kabupatens'));
        }else if($jenis_perkawinan == 'campuran_masuk'){
            $pekerjaans = Pekerjaan::get();
            $pendidikans = Pendidikan::get();
            $provinsis = Provinsi::get();
            $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
            return view('pages.banjar.perkawinan.create_campuran_masuk', compact('pekerjaans', 'pendidikans', 'provinsis', 'desas'));
        }else if($jenis_perkawinan == 'campuran_keluar'){
            $provinsis = Provinsi::get();
            return view('pages.banjar.perkawinan.create_campuran_keluar', compact('provinsis'));
        }else{
            return redirect()->back();
        }
    }

    public function store_satu_banjar_adat($status, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'purusa' => 'required',
            'pradana' => 'required',
            'file_bukti_serah_terima_perkawinan' => 'required',
            'tanggal_perkawinan' => 'required',
            'status_kekeluargaan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',
        ],[
            'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
            'purusa.required' => "Purusa wajib dipilih",
            'pradana.required' => "Pradana wajib dipilih",
            'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
            'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }
        //GET PURUSA DAN PRADANA
        $purusa = CacahKramaMipil::with('penduduk')->find($request->purusa);
        $pradana = CacahKramaMipil::with('penduduk')->find($request->pradana);

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        $banjar_adat_pradana = BanjarAdat::find($banjar_adat_id);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        //STATUS PERKAWINAN DRAFT/SAH
        if($status == '0'){
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
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil ditambahkan');
        }else if($status == '1'){
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
            $perkawinan->status_perkawinan = '3';

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

            //Kekeluargaan
            if($request->status_kekeluargaan == 'tetap'){
                //GET KK LAMA PURUSA
                $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                if(!$krama_mipil_purusa_lama){
                    $is_kk = 0;
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                    $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }else{
                    $is_kk = 1;
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }

                //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PRADANA KE PURUSA)
                //GET KK LAMA PRADANA
                $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
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
                        if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
                            $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                            $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                            $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                            $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                            $anggota_krama_mipil_pradana_baru->status = '1';
                            $anggota_krama_mipil_pradana_baru->save();
                        }else{
                            $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->pradana;
                if($is_kk){
                    if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                    }
                }else{
                    $anggota_krama_mipil_purusa_baru->status_hubungan = 'menantu';
                }
                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                $anggota_krama_mipil_purusa_baru->status = '1';
                $anggota_krama_mipil_purusa_baru->save();

                //7. LEBUR KK PURUSA LAMA
                $krama_mipil_purusa_lama->status = '0';
                $krama_mipil_purusa_lama->update();
            }else if($request->status_kekeluargaan == 'baru'){
                //GET CALON KK
                if($request->calon_kepala_keluarga == $request->purusa){
                    $calon_kk = 'purusa';
                }else if($request->calon_kepala_keluarga == $request->pradana){
                    $calon_kk = 'pradana';
                }

                //IF CALON KK IS PURUSA/PRADANA
                if($calon_kk == 'purusa'){
                    //GET KK LAMA PURUSA
                    $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                    if(!$krama_mipil_purusa_lama){
                        $is_kk = 0;
                        $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                        $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                        $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                    }else{
                        $is_kk = 1;
                        $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                    }

                    //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PRADANA KE PURUSA)
                    //GET KK LAMA PRADANA
                    $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
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
                            if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
                                $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                                $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                                $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                                $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                                $anggota_krama_mipil_pradana_baru->status = '1';
                                $anggota_krama_mipil_pradana_baru->save();
                            }else{
                                $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                        $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->pradana;
                        if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                        }else{
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                        }
                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                        $banjar_adat_id = session()->get('banjar_adat_id');
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
                        $krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->purusa;
                        $krama_mipil_purusa_baru->status = '1';
                        $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                        $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                        $krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $krama_mipil_purusa_baru->save();

                        //9. MASUKKAN PRADANA KE KK PURUSA
                        $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                        $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->pradana;
                        if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                        }else{
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                        }                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $anggota_krama_mipil_purusa_baru->status = '1';
                        $anggota_krama_mipil_purusa_baru->save();
                    }
                }else if($calon_kk == 'pradana'){
                    //GET KK LAMA PRADANA
                    $krama_mipil_pradana_lama = KramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
                    if(!$krama_mipil_pradana_lama){
                        $is_kk = 0;
                        $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
                        $krama_mipil_pradana_lama = KramaMipil::find($pradana_sebagai_anggota->krama_mipil_id);
                        $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();
                    }else{
                        $is_kk = 1;
                        $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();
                    }

                    //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PURUSA KE PRADANA)
                    //GET KK LAMA PURUSA
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                        if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
                            $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                            $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                            $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                            $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                            $anggota_krama_mipil_pradana_baru->status = '1';
                            $anggota_krama_mipil_pradana_baru->save();
                        }else{
                            $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                    $banjar_adat_id = session()->get('banjar_adat_id');
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
                    $krama_mipil_pradana_baru->cacah_krama_mipil_id = $request->pradana;
                    $krama_mipil_pradana_baru->status = '1';
                    $krama_mipil_pradana_baru->kedudukan_krama_mipil = 'pradana';
                    $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $krama_mipil_pradana_baru->save();

                    //9. MASUKKAN PURUSA KE KK PRADANA
                    $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                    $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $request->purusa;
                    if($purusa->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $anggota_krama_mipil_pradana_baru->status = '1';
                    $anggota_krama_mipil_pradana_baru->save();
                }
            }

            //Alamat dan Status Kawin
            $purusa = CacahKramaMipil::find($request->purusa);
            $pradana = CacahKramaMipil::find($request->pradana);
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
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil ditambahkan');
        }
    }

    public function store_beda_banjar_adat($status, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'banjar_adat_pradana' => 'required',
            'purusa' => 'required',
            'pradana' => 'required',
            'file_bukti_serah_terima_perkawinan' => 'required',
            'tanggal_perkawinan' => 'required',
            'status_kekeluargaan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',
        ],[
            'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
            'banjar_adat_pradana.required' => "Banjar Adat Pradana wajib dipilih",
            'purusa.required' => "Purusa wajib dipilih",
            'pradana.required' => "Pradana wajib dipilih",
            'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
            'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

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
        $perkawinan->status_perkawinan = $status;
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

        if($status == '0'){
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil ditambahkan');
        }else{
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil ditambahkan');
        }
    }

    public function store_campuran_masuk($status, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'purusa' => 'required',
            'file_bukti_serah_terima_perkawinan' => 'required',
            'tanggal_perkawinan' => 'required',
            'status_kekeluargaan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',

            'nik' => 'required|unique:tb_penduduk|regex:/^[0-9]*$/',
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'pekerjaan' => 'required',
            'pendidikan' => 'required',
            'golongan_darah' => 'required',
            'alamat' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
        ],[
            'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
            'purusa.required' => "Purusa wajib dipilih",
            'lampiran.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
            'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",

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
            'alamat.required' => "Alamat Asal wajib diisi",
            'provinsi.required' => "Provinsi Asal wajib dipilih",
            'kabupaten.required' => "Kabupaten Asal wajib dipilih",
            'kecamatan.required' => "Kecamatan Asal wajib dipilih",
            'desa.required' => "Desa/Kelurahan Asal wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET DATA PURUSA
        $purusa = CacahKramaMipil::find($request->purusa);
        $penduduk_purusa = Penduduk::find($purusa->penduduk_id);

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        //INSERT PENDUDUK
        $penduduk = new Penduduk();
        $penduduk->nik = $request->nik;
        $penduduk->gelar_depan = $request->gelar_depan;
        $penduduk->nama = $request->nama;
        $penduduk->gelar_belakang = $request->gelar_belakang;
        $penduduk->nama_alias = $request->nama_alias;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->agama = 'hindu';
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->profesi_id = $request->pekerjaan;
        $penduduk->pendidikan_id = $request->pendidikan;
        $penduduk->telepon = $request->telepon;
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
        $penduduk->koordinat_alamat = $penduduk_purusa->koordinat_alamat;
        $penduduk->alamat = $penduduk_purusa->alamat;
        $penduduk->desa_id = $penduduk_purusa->desa_id;
        $penduduk->save();

        //NOMOR CACAH KRAMA
        $banjar_adat_id = session()->get('banjar_adat_id');
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

        if($status == '0'){
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
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->nama_ayah_pradana = $request->nama_ayah;
            $perkawinan->nik_ibu_pradana = $request->nik_ibu;
            $perkawinan->nama_ibu_pradana = $request->nama_ibu;
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->agama_asal_pradana = $request->agama;
            $perkawinan->alamat_asal_pradana = $request->alamat;
            $perkawinan->desa_asal_pradana_id = $request->desa;
            if($request->file('file_sudhi_wadhani')!=""){
                $file = $request->file('file_sudhi_wadhani');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_sudhi_wadhani';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $perkawinan->file_sudhi_wadhani = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $perkawinan->save();

            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil ditambahkan');
        }else if($status == '3'){
            //AKTIFKAN CACAH
            $cacah_krama_mipil->status = '1';
            $cacah_krama_mipil->update();

            $perkawinan = new Perkawinan();
            $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
            $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
            $perkawinan->jenis_perkawinan = 'campuran_masuk';
            $perkawinan->purusa_id = $request->purusa;
            $perkawinan->pradana_id = $cacah_krama_mipil->id;
            $perkawinan->banjar_adat_purusa_id = $banjar_adat_id;
            $perkawinan->desa_adat_purusa_id = $desa_adat_purusa->id;
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
            $perkawinan->status_perkawinan = '3';
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
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->nama_ayah_pradana = $request->nama_ayah;
            $perkawinan->nik_ibu_pradana = $request->nik_ibu;
            $perkawinan->nama_ibu_pradana = $request->nama_ibu;
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->agama_asal_pradana = $request->agama;
            $perkawinan->alamat_asal_pradana = $request->alamat;
            $perkawinan->desa_asal_pradana_id = $request->desa;
            if($request->file('file_sudhi_wadhani')!=""){
                $file = $request->file('file_sudhi_wadhani');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_sudhi_wadhani';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $perkawinan->file_sudhi_wadhani = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $perkawinan->save();

            //Kekeluargaan
            if($request->status_kekeluargaan == 'tetap'){
                //GET KK LAMA PURUSA
                $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                if(!$krama_mipil_purusa_lama){
                    $is_kk = 0;
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                $anggota_krama_mipil_purusa_baru->status = '1';
                $anggota_krama_mipil_purusa_baru->save();

                //7. LEBUR KK PURUSA LAMA
                $krama_mipil_purusa_lama->status = '0';
                $krama_mipil_purusa_lama->update();

            }else if($request->status_kekeluargaan == 'baru'){
                //GET CALON KK
                if($request->calon_kepala_keluarga == $request->purusa){
                    $calon_kk = 'purusa';
                }else if($request->calon_kepala_keluarga == 'pradana'){
                    $calon_kk = 'pradana';
                }

                //IF CALON KK IS PURUSA/PRADANA
                if($calon_kk == 'purusa'){
                    //GET KK LAMA PURUSA
                    $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                    if(!$krama_mipil_purusa_lama){
                        $is_kk = 0;
                        $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                        $banjar_adat_id = session()->get('banjar_adat_id');
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
                        $krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->purusa;
                        $krama_mipil_purusa_baru->status = '1';
                        $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                        $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                        $krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $krama_mipil_purusa_baru->save();

                        //9. MASUKKAN PRADANA KE KK PURUSA
                        $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                        $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $pradana->id;
                        if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                        }else{
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                        }                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $anggota_krama_mipil_purusa_baru->status = '1';
                        $anggota_krama_mipil_purusa_baru->save();
                    }
                }else if($calon_kk == 'pradana'){
                    //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PURUSA KE PRADANA)
                    //GET KK LAMA PURUSA
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                    $banjar_adat_id = session()->get('banjar_adat_id');
                    $banjar_adat = BanjarAdat::find($banjar_adat_id);
                    $tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                    $krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $krama_mipil_pradana_baru->save();

                    //9. MASUKKAN PURUSA KE KK PRADANA
                    $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                    $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $request->purusa;
                    if($purusa->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $anggota_krama_mipil_pradana_baru->status = '1';
                    $anggota_krama_mipil_pradana_baru->save();
                }
            }
            //Alamat dan Status Kawin
            $purusa = CacahKramaMipil::find($request->purusa);
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
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil disimpan');
        }
    }

    public function store_campuran_keluar($status, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'pradana' => 'required',
            'file_bukti_serah_terima_perkawinan' => 'required',
            'tanggal_perkawinan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'file_akta_perkawinan' => 'required_with:nomor_akta_perkawinan',

            'nik_pasangan' => 'required|unique:tb_penduduk,nik|regex:/^[0-9]*$/',
            'nama_pasangan' => 'required|regex:/^[a-zA-Z\s]*$/',
            'alamat_pasangan' => 'required',
        ],[
            'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.unique' => "Nomor Bukti Serah Terima Perkawinan telah terdaftar",
            'pradana.required' => "Purusa wajib dipilih",
            'file_bukti_serah_terima_perkawinan.required' => "Bukti Serah Terima Perkawinan wajib diunggah",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
            'file_akta_perkawinan.required_with' => "File Akta Perkawinan wajib diisi",

            'nik_pasangan.regex' => "NIK hanya boleh mengandung angka",
            'nik_pasangan.unique' => "NIK yang dimasukkan telah terdaftar",
            'nama_pasangan.required' => "Nama wajib diisi",
            'nama_pasangan.regex' => "Nama hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET CACAH/MEMPELAI PRADANA
        $pradana = CacahKramaMipil::find($request->pradana);
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_pradana = BanjarAdat::find($banjar_adat_id);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        $perkawinan = new Perkawinan();
        $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
        $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
        $perkawinan->jenis_perkawinan = 'campuran_keluar';
        $perkawinan->pradana_id = $request->pradana;
        $perkawinan->banjar_adat_pradana_id = $banjar_adat_pradana->id;
        $perkawinan->desa_adat_pradana_id = $desa_adat_pradana->id;
        $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
        $perkawinan->keterangan = $request->keterangan;

        //DATA PASANGAN
        $perkawinan->nik_pasangan = $request->nik_pasangan;
        $perkawinan->nama_pasangan = $request->nama_pasangan;
        $perkawinan->alamat_asal_pasangan = $request->alamat_pasangan;
        $perkawinan->agama_pasangan = $request->agama;
        $perkawinan->desa_asal_pasangan_id = $request->desa_asal;

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

        if($status == '0'){
            $perkawinan->status_perkawinan = '0';
            $perkawinan->save();
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil ditambahkan');
        }else{
            //NON AKTIFKAN CACAH
            $pradana->status = '0';
            $pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
            $pradana->alasan_keluar = 'Perkawinan (Campuran Keluar)';
            $pradana->update();

            //KELUARKAN CACAH DARI KELUARGA IF EXIST
            //GET KK LAMA PRADANA
            $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
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
                    if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
                        $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                        $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                        $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                        $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                        $anggota_krama_mipil_pradana_baru->status = '1';
                        $anggota_krama_mipil_pradana_baru->save();
                    }else{
                        $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil ditambahkan');
        }
    }

    public function edit($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $perkawinan = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->find($id);
        if($perkawinan->jenis_perkawinan != 'campuran_keluar'){
            //PURUSA
            $nama = '';
            if($perkawinan->purusa->penduduk->gelar_depan != ''){
                $nama = $nama.$perkawinan->purusa->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->purusa->penduduk->nama;
            if($perkawinan->purusa->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->purusa->penduduk->gelar_belakang;
            }
            $perkawinan->purusa->penduduk->nama = $nama;
        }

        //PRADANA
        $nama = '';
        $perkawinan->pradana->penduduk->nama_penggal = $perkawinan->pradana->penduduk->nama;
        if($perkawinan->pradana->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->pradana->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->pradana->penduduk->nama;
        if($perkawinan->pradana->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->pradana->penduduk->gelar_belakang;
        }
        $perkawinan->pradana->penduduk->nama = $nama;

        //RETURN PER JENIS PERKAWINAN
        if($perkawinan->jenis_perkawinan == 'satu_banjar_adat'){
            return view('pages.banjar.perkawinan.edit_satu_banjar_adat', compact('perkawinan'));
        }else if($perkawinan->jenis_perkawinan == 'beda_banjar_adat'){
            //GET CURRENT VAL
            $desa_adat_pradana = DesaAdat::find($perkawinan->desa_adat_pradana_id);
            $kecamatan_pradana = Kecamatan::find($desa_adat_pradana->kecamatan_id);
            $kabupaten_pradana = Kabupaten::find($kecamatan_pradana->kabupaten_id);

            $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
            $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten_pradana->id)->get();
            $desa_adats = DesaAdat::where('kecamatan_id', $kecamatan_pradana->id)->get();
            $banjar_adats = BanjarAdat::where('desa_adat_id', $desa_adat_pradana->id)->where('id', '!=', $banjar_adat_id)->get();
            return view('pages.banjar.perkawinan.edit_beda_banjar_adat', compact('perkawinan', 'banjar_adats', 'desa_adats', 'kecamatans', 'kabupatens', 'kabupaten_pradana', 'kecamatan_pradana'));
        }else if($perkawinan->jenis_perkawinan == 'campuran_masuk'){
            //GET MASTER PRADANA
            $desa_asal = DesaDinas::find($perkawinan->desa_asal_pradana_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

            $provinsis = Provinsi::get();
            $kabupatens = Kabupaten::where('provinsi_id', $provinsi_asal->id)->get();
            $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten_asal->id)->get();
            $desas = DesaDinas::where('kecamatan_id', $kecamatan_asal->id)->get();

            $pekerjaans = Pekerjaan::get();
            $pendidikans = Pendidikan::get();
            $desa_tinggals = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
            return view('pages.banjar.perkawinan.edit_campuran_masuk', compact('perkawinan', 'pekerjaans', 'pendidikans', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal', 'provinsis', 'kabupatens', 'kecamatans', 'desas', 'desa_tinggals'));
        }else if($perkawinan->jenis_perkawinan == 'campuran_keluar'){
            $provinsis = Provinsi::get();

            //GET CURR ASAL
            $desa_asal = DesaDinas::find($perkawinan->desa_asal_pasangan_id);
            if($desa_asal){
                $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
                $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
                $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

                $desas = DesaDinas::where('kecamatan_id', $kecamatan_asal->id)->get();
                $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten_asal->id)->get();
                $kabupatens = Kabupaten::where('provinsi_id', $provinsi_asal->id)->get();

                return view('pages.banjar.perkawinan.edit_campuran_keluar', compact('provinsis', 'kabupatens', 'kecamatans', 'desas', 'perkawinan', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
            }else{
                return view('pages.banjar.perkawinan.edit_campuran_keluar', compact('provinsis', 'perkawinan'));
            }
        }else{
            return redirect()->back();
        }
    }

    public function update_satu_banjar_adat($id, $status, Request $request)
    {
        $perkawinan = Perkawinan::find($id);
        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'nomor_bukti_serah_terima_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_perkawinan')->ignore($perkawinan->id),
            ],
            'purusa' => 'required',
            'pradana' => 'required',
            'tanggal_perkawinan' => 'required',
            'status_kekeluargaan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'nomor_akta_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_akta_perkawinan')->ignore($perkawinan->id),
            ],
        ],[
            'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
            'purusa.required' => "Purusa wajib dipilih",
            'pradana.required' => "Pradana wajib dipilih",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }
        //GET PURUSA DAN PRADANA
        $purusa = CacahKramaMipil::with('penduduk')->find($request->purusa);
        $pradana = CacahKramaMipil::with('penduduk')->find($request->pradana);

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        $banjar_adat_pradana = BanjarAdat::find($banjar_adat_id);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        //STATUS PERKAWINAN DRAFT/SAH
        if($status == '0'){
            $perkawinan = Perkawinan::find($id);
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
            }else{
                $perkawinan->calon_krama_id = NULL;
            }
            $perkawinan->nama_pemuput = $request->nama_pemuput;
            $perkawinan->status_perkawinan = '0';

            if($request->file('file_bukti_serah_terima_perkawinan')!=""){
                $file = $request->file('file_bukti_serah_terima_perkawinan');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
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
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($perkawinan->file_akta_perkawinan != NULL){
                    $old_path = str_replace("/storage","",$perkawinan->file_akta_perkawinan);
                    Storage::disk('public')->delete($old_path);
                }
                $perkawinan->file_akta_perkawinan = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $perkawinan->update();
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil diperbaharui');
        }else if($status == '1'){
            $perkawinan = Perkawinan::find($id);
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
            }else{
                $perkawinan->calon_krama_id = NULL;
            }            $perkawinan->nama_pemuput = $request->nama_pemuput;
            $perkawinan->status_perkawinan = '3';

            if($request->file('lampiran')!=""){
                $file = $request->file('lampiran');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/lampiran';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($perkawinan->lampiran != NULL){
                    $old_path = str_replace("/storage","",$perkawinan->lampiran);
                    Storage::disk('public')->delete($old_path);
                }
                $perkawinan->lampiran = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $perkawinan->update();

            //Kekeluargaan
            if($request->status_kekeluargaan == 'tetap'){
                //GET KK LAMA PURUSA
                $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                if(!$krama_mipil_purusa_lama){
                    $is_kk = 0;
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                    $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }else{
                    $is_kk = 1;
                    $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                }

                //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PRADANA KE PURUSA)
                //GET KK LAMA PRADANA
                $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
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
                        if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
                            $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                            $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                            $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                            $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                            $anggota_krama_mipil_pradana_baru->status = '1';
                            $anggota_krama_mipil_pradana_baru->save();
                        }else{
                            $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->pradana;
                if($is_kk){
                    if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                    }
                }else{
                    $anggota_krama_mipil_purusa_baru->status_hubungan = 'menantu';
                }
                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                $anggota_krama_mipil_purusa_baru->status = '1';
                $anggota_krama_mipil_purusa_baru->save();

                //7. LEBUR KK PURUSA LAMA
                $krama_mipil_purusa_lama->status = '0';
                $krama_mipil_purusa_lama->update();
            }else if($request->status_kekeluargaan == 'baru'){
                //GET CALON KK
                if($request->calon_kepala_keluarga == $request->purusa){
                    $calon_kk = 'purusa';
                }else if($request->calon_kepala_keluarga == $request->pradana){
                    $calon_kk = 'pradana';
                }

                //IF CALON KK IS PURUSA/PRADANA
                if($calon_kk == 'purusa'){
                    //GET KK LAMA PURUSA
                    $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                    if(!$krama_mipil_purusa_lama){
                        $is_kk = 0;
                        $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                        $krama_mipil_purusa_lama = KramaMipil::find($purusa_sebagai_anggota->krama_mipil_id);
                        $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                    }else{
                        $is_kk = 1;
                        $anggota_krama_mipil_purusa_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_purusa_lama->id)->get();
                    }

                    //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PRADANA KE PURUSA)
                    //GET KK LAMA PRADANA
                    $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
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
                            if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
                                $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                                $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                                $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                                $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                                $anggota_krama_mipil_pradana_baru->status = '1';
                                $anggota_krama_mipil_pradana_baru->save();
                            }else{
                                $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                        $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->pradana;
                        if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                        }else{
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                        }
                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                        $banjar_adat_id = session()->get('banjar_adat_id');
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

                        //8. PEMBENTUKAN KK PURUSA BARU
                        $krama_mipil_purusa_baru = new KramaMipil();
                        $krama_mipil_purusa_baru->nomor_krama_mipil = $nomor_krama_mipil;
                        $krama_mipil_purusa_baru->banjar_adat_id = $krama_mipil_purusa_lama->banjar_adat_id;
                        $krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->purusa;
                        $krama_mipil_purusa_baru->status = '1';
                        $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                        $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                        $krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $krama_mipil_purusa_baru->save();

                        //9. MASUKKAN PRADANA KE KK PURUSA
                        $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                        $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->pradana;
                        if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                        }else{
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                        }                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $anggota_krama_mipil_purusa_baru->status = '1';
                        $anggota_krama_mipil_purusa_baru->save();
                    }
                }else if($calon_kk == 'pradana'){
                    //GET KK LAMA PRADANA
                    $krama_mipil_pradana_lama = KramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
                    if(!$krama_mipil_pradana_lama){
                        $is_kk = 0;
                        $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
                        $krama_mipil_pradana_lama = KramaMipil::find($pradana_sebagai_anggota->krama_mipil_id);
                        $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();
                    }else{
                        $is_kk = 1;
                        $anggota_krama_mipil_pradana_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_pradana_lama->id)->get();
                    }

                    //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PURUSA KE PRADANA)
                    //GET KK LAMA PURUSA
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                    $krama_mipil_pradana_baru->status = '1';
                    $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_pradana_baru->tanggal_registrasi = $krama_mipil_pradana_lama->tanggal_registrasi;
                    $krama_mipil_pradana_baru->save();

                    //COPY DATA ANGGOTA LAMA PRADANA
                    foreach($anggota_krama_mipil_pradana_lama as $anggota_lama_pradana){
                        if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
                            $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                            $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                            $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $anggota_lama_pradana->cacah_krama_mipil_id;
                            $anggota_krama_mipil_pradana_baru->status_hubungan = $anggota_lama_pradana->status_hubungan;
                            $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_pradana->tanggal_registrasi));
                            $anggota_krama_mipil_pradana_baru->status = '1';
                            $anggota_krama_mipil_pradana_baru->save();
                        }else{
                            $anggota_lama_pradana->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                    $banjar_adat_id = session()->get('banjar_adat_id');
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
                    $krama_mipil_pradana_baru->cacah_krama_mipil_id = $request->pradana;
                    $krama_mipil_pradana_baru->status = '1';
                    $krama_mipil_pradana_baru->kedudukan_krama_mipil = 'pradana';
                    $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $krama_mipil_pradana_baru->save();

                    //9. MASUKKAN PURUSA KE KK PRADANA
                    $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                    $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $request->purusa;
                    if($purusa->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $anggota_krama_mipil_pradana_baru->status = '1';
                    $anggota_krama_mipil_pradana_baru->save();
                }
            }

            //Alamat dan Status Kawin
            $purusa = CacahKramaMipil::find($request->purusa);
            $pradana = CacahKramaMipil::find($request->pradana);
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
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil diperbaharui');
        }
    }

    public function update_beda_banjar_adat($id, $status, Request $request)
    {
        $perkawinan = Perkawinan::find($id);

        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'nomor_bukti_serah_terima_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_perkawinan')->ignore($perkawinan->id),
            ],
            'banjar_adat_pradana' => 'required',
            'purusa' => 'required',
            'pradana' => 'required',
            'tanggal_perkawinan' => 'required',
            'status_kekeluargaan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'nomor_akta_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_akta_perkawinan')->ignore($perkawinan->id),
            ],
        ],[
            'nomor_bukti_serah_terima_perkawinan' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'banjar_adat_pradana.required' => "Banjar Adat Pradana wajib dipilih",
            'purusa.required' => "Purusa wajib dipilih",
            'pradana.required' => "Pradana wajib dipilih",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        $banjar_adat_pradana = BanjarAdat::find($request->banjar_adat_pradana);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        if($perkawinan->alasan_penolakan != NULL){
            /**
             * create notif baru
             */

            $notifikasi = new Notifikasi();
            $notifikasi->notif_edit_perkawinan_beda_banjar_adat($perkawinan->id);
        }

        //STATUS PERKAWINAN DRAFT/SAH
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
        $perkawinan->status_perkawinan = $status;
        $perkawinan->keterangan = $request->keterangan;
        $perkawinan->alasan_penolakan = NULL;
        if($request->status_kekeluargaan == 'baru'){
            $perkawinan->calon_krama_id = $request->calon_kepala_keluarga;
        }else{
            $perkawinan->calon_krama_id = NULL;
        }
        $perkawinan->nama_pemuput = $request->nama_pemuput;

        if($request->file('file_bukti_serah_terima_perkawinan')!=""){
            $file = $request->file('file_bukti_serah_terima_perkawinan');
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
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
            $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($perkawinan->file_akta_perkawinan != NULL){
                $old_path = str_replace("/storage","",$perkawinan->file_akta_perkawinan);
                Storage::disk('public')->delete($old_path);
            }
            $perkawinan->file_akta_perkawinan = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        $perkawinan->save();
        if($status == '0'){
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil diperbaharui');
        }else{
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil diperbaharui');
        }
    }

    public function update_campuran_masuk($id, $status, Request $request)
    {
        //GET MASTER PERKAWINAN
        $perkawinan = Perkawinan::find($id);
        $cacah_pradana = CacahKramaMipil::find($perkawinan->pradana_id);
        $penduduk = Penduduk::find($cacah_pradana->penduduk_id);

        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'nomor_bukti_serah_terima_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_perkawinan')->ignore($perkawinan->id),
            ],            'purusa' => 'required',
            'tanggal_perkawinan' => 'required',
            'status_kekeluargaan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'nomor_akta_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_akta_perkawinan')->ignore($perkawinan->id),
            ],

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
            'alamat' => 'required',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'kecamatan' => 'required',
            'desa' => 'required',
        ],[
            'nomor_bukti_serah_terima_perkawinan.required' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.unique' => "No. Bukti Serah Terima Perkawinan telah terdaftar",
            'purusa.required' => "Purusa wajib dipilih",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'status_kekeluargaan.required' => "Status Kekeluargaan wajib dipilih",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",

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
            'alamat.required' => "Alamat Asal wajib diisi",
            'provinsi.required' => "Provinsi Asal wajib dipilih",
            'kabupaten.required' => "Kabupaten Asal wajib dipilih",
            'kecamatan.required' => "Kecamatan Asal wajib dipilih",
            'desa.required' => "Desa/Kelurahan Asal wajib dipilih",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET DATA PURUSA PRADANA
        $purusa = CacahKramaMipil::find($request->purusa);
        $pradana = $cacah_pradana;
        $penduduk_purusa = Penduduk::find($purusa->penduduk_id);

        //GET BANJAR DAN DESA ADAT PURUSA PRADANA
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_purusa = BanjarAdat::find($banjar_adat_id);
        $desa_adat_purusa = DesaAdat::find($banjar_adat_purusa->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        //UPDATE PENDUDUK
        $penduduk->nik = $request->nik;
        $penduduk->gelar_depan = $request->gelar_depan;
        $penduduk->nama = $request->nama;
        $penduduk->gelar_belakang = $request->gelar_belakang;
        $penduduk->nama_alias = $request->nama_alias;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->agama = 'hindu';
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->profesi_id = $request->pekerjaan;
        $penduduk->pendidikan_id = $request->pendidikan;
        $penduduk->telepon = $request->telepon;
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
        $penduduk->koordinat_alamat = $penduduk_purusa->koordinat_alamat;
        $penduduk->alamat = $penduduk_purusa->alamat;
        $penduduk->desa_id = $penduduk_purusa->desa_id;
        $penduduk->update();

        //NOMOR CACAH KRAMA
        $banjar_adat_id = session()->get('banjar_adat_id');
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

        //UPDATE CACAH KRAMA MIPIL
        $cacah_krama_mipil = $cacah_pradana;
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
        $cacah_krama_mipil->update();

        if($status == '0'){
            //UPDATE DRAFT PERKAWINAN
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

            if($request->file('file_bukti_serah_terima_perkawinan')!=""){
                $file = $request->file('file_bukti_serah_terima_perkawinan');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
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
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($perkawinan->file_akta_perkawinan != NULL){
                    $old_path = str_replace("/storage","",$perkawinan->file_akta_perkawinan);
                    Storage::disk('public')->delete($old_path);
                }
                $perkawinan->file_akta_perkawinan = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }

            //DATA + PRADANA
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->nama_ayah_pradana = $request->nama_ayah;
            $perkawinan->nik_ibu_pradana = $request->nik_ibu;
            $perkawinan->nama_ibu_pradana = $request->nama_ibu;
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->agama_asal_pradana = $request->agama;
            $perkawinan->alamat_asal_pradana = $request->alamat;
            $perkawinan->desa_asal_pradana_id = $request->desa;
            if($request->file('file_sudhi_wadhani')!=""){
                $file = $request->file('file_sudhi_wadhani');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_sudhi_wadhani';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $perkawinan->file_sudhi_wadhani = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $perkawinan->update();

            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil diperbaharui');
        }else if($status == '3'){
            //AKTIFKAN CACAH
            $cacah_krama_mipil->status = '1';
            $cacah_krama_mipil->update();

            $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
            $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
            $perkawinan->jenis_perkawinan = 'campuran_masuk';
            $perkawinan->purusa_id = $request->purusa;
            $perkawinan->pradana_id = $cacah_krama_mipil->id;
            $perkawinan->banjar_adat_purusa_id = $banjar_adat_id;
            $perkawinan->desa_adat_purusa_id = $desa_adat_purusa->id;
            $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
            $perkawinan->keterangan = $request->keterangan;
            $perkawinan->status_kekeluargaan = $request->status_kekeluargaan;
            if($request->status_kekeluargaan == 'baru'){
                if($request->calon_kepala_keluarga == 'pradana'){
                    $perkawinan->calon_krama_id = $cacah_krama_mipil->id;
                }else{
                    $perkawinan->calon_krama_id = $request->calon_kepala_keluarga;
                }
            }else{
                $perkawinan->calon_krama_id = NULL;
            }
            $perkawinan->nama_pemuput = $request->nama_pemuput;
            $perkawinan->status_perkawinan = '3';
            if($request->file('file_bukti_serah_terima_perkawinan')!=""){
                $file = $request->file('file_bukti_serah_terima_perkawinan');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_bukti_serah_terima_perkawinan';
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
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_akta_perkawinan';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($perkawinan->file_akta_perkawinan != NULL){
                    $old_path = str_replace("/storage","",$perkawinan->file_akta_perkawinan);
                    Storage::disk('public')->delete($old_path);
                }
                $perkawinan->file_akta_perkawinan = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }

            //DATA + PRADANA
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->nama_ayah_pradana = $request->nama_ayah;
            $perkawinan->nik_ibu_pradana = $request->nik_ibu;
            $perkawinan->nama_ibu_pradana = $request->nama_ibu;
            $perkawinan->nik_ayah_pradana = $request->nik_ayah;
            $perkawinan->agama_asal_pradana = $request->agama;
            $perkawinan->alamat_asal_pradana = $request->alamat;
            $perkawinan->desa_asal_pradana_id = $request->desa;
            if($request->file('file_sudhi_wadhani')!=""){
                $file = $request->file('file_sudhi_wadhani');
                $fileLocation = '/file/'.$desa_adat_purusa->id.'/perkawinan/'.$convert_nomor_perkawinan.'/file_sudhi_wadhani';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $perkawinan->file_sudhi_wadhani = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $perkawinan->update();

            //Kekeluargaan
            if($request->status_kekeluargaan == 'tetap'){
                //GET KK LAMA PURUSA
                $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                if(!$krama_mipil_purusa_lama){
                    $is_kk = 0;
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                $anggota_krama_mipil_purusa_baru->status = '1';
                $anggota_krama_mipil_purusa_baru->save();

                //7. LEBUR KK PURUSA LAMA
                $krama_mipil_purusa_lama->status = '0';
                $krama_mipil_purusa_lama->update();


            }else if($request->status_kekeluargaan == 'baru'){
                //GET CALON KK
                if($request->calon_kepala_keluarga == $request->purusa){
                    $calon_kk = 'purusa';
                }else if($request->calon_kepala_keluarga == $request->pradana){
                    $calon_kk = 'pradana';
                }

                //IF CALON KK IS PURUSA/PRADANA
                if($calon_kk == 'purusa'){
                    //GET KK LAMA PURUSA
                    $krama_mipil_purusa_lama = KramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
                    if(!$krama_mipil_purusa_lama){
                        $is_kk = 0;
                        $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                        $banjar_adat_id = session()->get('banjar_adat_id');
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
                        $krama_mipil_purusa_baru->cacah_krama_mipil_id = $request->purusa;
                        $krama_mipil_purusa_baru->status = '1';
                        $krama_mipil_purusa_baru->kedudukan_krama_mipil = 'purusa';
                        $krama_mipil_purusa_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                        $krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $krama_mipil_purusa_baru->save();

                        //9. MASUKKAN PRADANA KE KK PURUSA
                        $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                        $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                        $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $pradana->id;
                        if($pradana->penduduk->jenis_kelamin == 'perempuan'){
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'istri';
                        }else{
                            $anggota_krama_mipil_purusa_baru->status_hubungan = 'suami';
                        }                        $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                        $anggota_krama_mipil_purusa_baru->status = '1';
                        $anggota_krama_mipil_purusa_baru->save();
                    }
                }else if($calon_kk == 'pradana'){
                    //TRANSAKSI PEMINDAHAN ANGGOTA KELUARGA (PURUSA KE PRADANA)
                    //GET KK LAMA PURUSA
                    $purusa_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->purusa)->where('status', '1')->first();
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
                            if($anggota_lama_purusa->cacah_krama_mipil_id != $request->purusa){
                                $anggota_krama_mipil_purusa_baru = new AnggotaKramaMipil();
                                $anggota_krama_mipil_purusa_baru->krama_mipil_id = $krama_mipil_purusa_baru->id;
                                $anggota_krama_mipil_purusa_baru->cacah_krama_mipil_id = $anggota_lama_purusa->cacah_krama_mipil_id;
                                $anggota_krama_mipil_purusa_baru->status_hubungan = $anggota_lama_purusa->status_hubungan;
                                $anggota_krama_mipil_purusa_baru->tanggal_registrasi = date("Y-m-d", strtotime($anggota_lama_purusa->tanggal_registrasi));
                                $anggota_krama_mipil_purusa_baru->status = '1';
                                $anggota_krama_mipil_purusa_baru->save();
                            }else{
                                $anggota_lama_purusa->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_perkawinan));
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
                    $banjar_adat_id = session()->get('banjar_adat_id');
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
                    $krama_mipil_pradana_baru->cacah_krama_mipil_id = $request->pradana;
                    $krama_mipil_pradana_baru->status = '1';
                    $krama_mipil_pradana_baru->kedudukan_krama_mipil = 'pradana';
                    $krama_mipil_pradana_baru->alasan_perubahan = 'Krama Mipil Baru (Perkawinan)';
                    $krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $krama_mipil_pradana_baru->save();

                    //9. MASUKKAN PURUSA KE KK PRADANA
                    $anggota_krama_mipil_pradana_baru = new AnggotaKramaMipil();
                    $anggota_krama_mipil_pradana_baru->krama_mipil_id = $krama_mipil_pradana_baru->id;
                    $anggota_krama_mipil_pradana_baru->cacah_krama_mipil_id = $request->purusa;
                    if($purusa->penduduk->jenis_kelamin == 'perempuan'){
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'istri';
                    }else{
                        $anggota_krama_mipil_pradana_baru->status_hubungan = 'suami';
                    }
                    $anggota_krama_mipil_pradana_baru->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_perkawinan));
                    $anggota_krama_mipil_pradana_baru->status = '1';
                    $anggota_krama_mipil_pradana_baru->save();
                }
            }
            //Alamat dan Status Kawin
            $purusa = CacahKramaMipil::find($request->purusa);
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
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil diperbaharui');
        }
    }

    public function update_campuran_keluar($id, $status, Request $request)
    {
        $perkawinan = Perkawinan::find($id);

        $validator = Validator::make($request->all(), [
            'nomor_bukti_serah_terima_perkawinan' => 'required|unique:tb_perkawinan,nomor_perkawinan|max:50',
            'nomor_bukti_serah_terima_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_perkawinan')->ignore($perkawinan->id),
            ],
            'pradana' => 'required',
            'tanggal_perkawinan' => 'required',
            'nomor_akta_perkawinan' => 'unique:tb_perkawinan|nullable|max:21',
            'nomor_akta_perkawinan' => [
                Rule::unique('tb_perkawinan', 'nomor_akta_perkawinan')->ignore($perkawinan->id),
            ],

            'nik_pasangan' => 'required|unique:tb_penduduk,nik|regex:/^[0-9]*$/',
            'nama_pasangan' => 'required|regex:/^[a-zA-Z\s]*$/',
            'alamat_pasangan' => 'required',
        ],[
            'nomor_bukti_serah_terima_perkawinan' => "No. Bukti Serah Terima Perkawinan wajib diisi",
            'pradana.required' => "Purusa wajib dipilih",
            'tanggal_perkawinan.required' => "Tanggal Perkawinan wajib diisi",
            'nomor_bukti_serah_terima_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 50 karakter",
            'nomor_akta_perkawinan.unique' => "Nomor Akta Perkawinan telah terdaftar",
            'nomor_akta_perkawinan.max' => "Nomor Akta Perkawinan maksimal terdiri dari 21 karakter",

            'nik_pasangan.regex' => "NIK hanya boleh mengandung angka",
            'nik_pasangan.unique' => "NIK yang dimasukkan telah terdaftar",
            'nama_pasangan.required' => "Nama wajib diisi",
            'nama_pasangan.regex' => "Nama hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //GET CACAH/MEMPELAI PRADANA
        $pradana = CacahKramaMipil::find($request->pradana);
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat_pradana = BanjarAdat::find($banjar_adat_id);
        $desa_adat_pradana = DesaAdat::find($banjar_adat_pradana->desa_adat_id);

        //CONVERT NOMOR PERKAWINAN
        $convert_nomor_perkawinan = str_replace("/","-",$request->nomor_bukti_serah_terima_perkawinan);

        $perkawinan->nomor_perkawinan = $request->nomor_bukti_serah_terima_perkawinan;
        $perkawinan->nomor_akta_perkawinan = $request->nomor_akta_perkawinan;
        $perkawinan->jenis_perkawinan = 'campuran_keluar';
        $perkawinan->pradana_id = $request->pradana;
        $perkawinan->banjar_adat_pradana_id = $banjar_adat_pradana->id;
        $perkawinan->desa_adat_pradana_id = $desa_adat_pradana->id;
        $perkawinan->tanggal_perkawinan = date("Y-m-d", strtotime($request->tanggal_perkawinan));
        $perkawinan->keterangan = $request->keterangan;

        //DATA PASANGAN
        $perkawinan->nik_pasangan = $request->nik_pasangan;
        $perkawinan->nama_pasangan = $request->nama_pasangan;
        $perkawinan->alamat_asal_pasangan = $request->alamat_pasangan;
        $perkawinan->agama_pasangan = $request->agama;
        $perkawinan->desa_asal_pasangan_id = $request->desa_asal;

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

        if($status == '0'){
            $perkawinan->status_perkawinan = '0';
            $perkawinan->update();
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Draft Perkawinan berhasil diperbaharui');
        }else{
            //NON AKTIFKAN CACAH
            $pradana->status = '0';
            $pradana->tanggal_nonaktif = date("Y-m-d", strtotime($perkawinan->tanggal_perkawinan));
            $pradana->alasan_keluar = 'Perkawinan (Campuran Keluar)';
            $pradana->update();

            //KELUARKAN CACAH DARI KELUARGA IF EXIST
            //GET KK LAMA PRADANA
            $pradana_sebagai_anggota = AnggotaKramaMipil::where('cacah_krama_mipil_id', $request->pradana)->where('status', '1')->first();
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
                    if($anggota_lama_pradana->cacah_krama_mipil_id != $request->pradana){
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
            $perkawinan->update();
            return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil diperbaharui');
        }
    }

    public function detail($id){
        $perkawinan = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->find($id);
        //SET NAMA LENGKAP PURUSA & AYAH IBU
        if($perkawinan->jenis_perkawinan != 'campuran_keluar'){
            //PURUSA
            $nama = '';
            if($perkawinan->purusa->penduduk->gelar_depan != ''){
                $nama = $nama.$perkawinan->purusa->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->purusa->penduduk->nama;
            if($perkawinan->purusa->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->purusa->penduduk->gelar_belakang;
            }
            $perkawinan->purusa->penduduk->nama = $nama;

            //AYAH PURUSA
            if($perkawinan->purusa->penduduk->ayah){
                $nama = '';
                if($perkawinan->purusa->penduduk->ayah->gelar_depan != ''){
                    $nama = $nama.$perkawinan->purusa->ayah->gelar_depan;
                }
                $nama = $nama.' '.$perkawinan->purusa->penduduk->ayah->nama;
                if($perkawinan->purusa->penduduk->ayah->gelar_belakang != ''){
                    $nama = $nama.', '.$perkawinan->purusa->penduduk->ayah->gelar_belakang;
                }
                $perkawinan->purusa->penduduk->ayah->nama = $nama;
            }

            //IBU PURUSA
            if($perkawinan->purusa->penduduk->ibu){
                $nama = '';
                if($perkawinan->purusa->penduduk->ibu->gelar_depan != ''){
                    $nama = $nama.$perkawinan->purusa->penduduk->ibu->gelar_depan;
                }
                $nama = $nama.' '.$perkawinan->purusa->penduduk->ibu->nama;
                if($perkawinan->purusa->penduduk->ibu->gelar_belakang != ''){
                    $nama = $nama.', '.$perkawinan->purusa->penduduk->ibu->gelar_belakang;
                }
                $perkawinan->purusa->penduduk->ibu->nama = $nama;
            }
        }

        //SET NAMA LENGKAP PRADANA & AYAH IBU
        //PRADANA
        $nama = '';
        if($perkawinan->pradana->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->pradana->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->pradana->penduduk->nama;
        if($perkawinan->pradana->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->pradana->penduduk->gelar_belakang;
        }
        $perkawinan->pradana->penduduk->nama = $nama;

        //AYAH PRADANA
        if($perkawinan->pradana->penduduk->ayah){
            $nama = '';
            if($perkawinan->pradana->penduduk->ayah->gelar_depan != ''){
                $nama = $nama.$perkawinan->pradana->penduduk->ayah->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->pradana->penduduk->ayah->nama;
            if($perkawinan->pradana->penduduk->ayah->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->pradana->penduduk->ayah->gelar_belakang;
            }
            $perkawinan->pradana->penduduk->ayah->nama = $nama;
        }

        //IBU PRADANA
        if($perkawinan->pradana->penduduk->ibu){
            $nama = '';
            if($perkawinan->pradana->penduduk->ibu->gelar_depan != ''){
                $nama = $nama.$perkawinan->pradana->penduduk->ibu->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->pradana->penduduk->ibu->nama;
            if($perkawinan->pradana->penduduk->ibu->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->pradana->penduduk->ibu->gelar_belakang;
            }
            $perkawinan->pradana->penduduk->ibu->nama = $nama;
        }

        if($perkawinan->jenis_perkawinan == 'campuran_masuk'){
            $desa_asal = DesaDinas::find($perkawinan->desa_asal_pradana_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
            return view('pages.banjar.perkawinan.detail_campuran_masuk', compact('perkawinan', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
        }else if($perkawinan->jenis_perkawinan == 'campuran_keluar'){
            $desa_asal = DesaDinas::find($perkawinan->desa_asal_pasangan_id);
            if($desa_asal){
                $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
                $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
                $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);
                return view('pages.banjar.perkawinan.detail_campuran_keluar', compact('perkawinan', 'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
            }else{
                return view('pages.banjar.perkawinan.detail_campuran_keluar', compact('perkawinan'));
            }
        }
        else{
            return view('pages.banjar.perkawinan.detail', compact('perkawinan'));
        }
    }

    public function destroy($id){
        $perkawinan = Perkawinan::find($id);
        if($perkawinan->status_perkawinan == '0' || $perkawinan->status_perkawinan == '2'){
            if($perkawinan->jenis_perkawinan == 'campuran_masuk'){
                $cacah_pradana = CacahKramaMipil::find($perkawinan->pradana_id);
                $penduduk_pradana = Penduduk::find($cacah_pradana->penduduk_id);

                //DELETE SEMUA
                $cacah_pradana->delete();
                $penduduk_pradana->delete();
            }
            $perkawinan->delete();
            return redirect()->back()->with('success', 'Draft Perkawinan berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Perkawinan yang telah sah tidak dapat dihapus');
        }
    }

    //MASUK BANJAR ADAT
    public function edit_perkawinan_masuk($id){
        $perkawinan = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->find($id);
        //SET NAMA LENGKAP PURUSA & AYAH IBU
        //PURUSA
        $nama = '';
        if($perkawinan->purusa->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->purusa->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->purusa->penduduk->nama;
        if($perkawinan->purusa->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->purusa->penduduk->gelar_belakang;
        }
        $perkawinan->purusa->penduduk->nama = $nama;

        //AYAH PURUSA
        if($perkawinan->purusa->penduduk->ayah){
            $nama = '';
            if($perkawinan->purusa->penduduk->ayah->gelar_depan != ''){
                $nama = $nama.$perkawinan->purusa->ayah->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->purusa->penduduk->ayah->nama;
            if($perkawinan->purusa->penduduk->ayah->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->purusa->penduduk->ayah->gelar_belakang;
            }
            $perkawinan->purusa->penduduk->ayah->nama = $nama;
        }

        //IBU PURUSA
        if($perkawinan->purusa->penduduk->ibu){
            $nama = '';
            if($perkawinan->purusa->penduduk->ibu->gelar_depan != ''){
                $nama = $nama.$perkawinan->purusa->penduduk->ibu->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->purusa->penduduk->ibu->nama;
            if($perkawinan->purusa->penduduk->ibu->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->purusa->penduduk->ibu->gelar_belakang;
            }
            $perkawinan->purusa->penduduk->ibu->nama = $nama;
        }

        //SET NAMA LENGKAP PRADANA & AYAH IBU
        //PRADANA
        $nama = '';
        if($perkawinan->pradana->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->pradana->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->pradana->penduduk->nama;
        if($perkawinan->pradana->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->pradana->penduduk->gelar_belakang;
        }
        $perkawinan->pradana->penduduk->nama = $nama;

        //AYAH PRADANA
        if($perkawinan->pradana->penduduk->ayah){
            $nama = '';
            if($perkawinan->pradana->penduduk->ayah->gelar_depan != ''){
                $nama = $nama.$perkawinan->pradana->ayah->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->pradana->penduduk->ayah->nama;
            if($perkawinan->pradana->penduduk->ayah->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->pradana->penduduk->ayah->gelar_belakang;
            }
            $perkawinan->pradana->penduduk->ayah->nama = $nama;
        }

        //IBU PRADANA
        if($perkawinan->pradana->penduduk->ibu){
            $nama = '';
            if($perkawinan->pradana->penduduk->ibu->gelar_depan != ''){
                $nama = $nama.$perkawinan->pradana->penduduk->ibu->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->pradana->penduduk->ibu->nama;
            if($perkawinan->pradana->penduduk->ibu->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->pradana->penduduk->ibu->gelar_belakang;
            }
            $perkawinan->pradana->penduduk->ibu->nama = $nama;
        }
        return view('pages.banjar.perkawinan.detail_masuk_banjar', compact('perkawinan'));
    }

    public function konfirmasi_perkawinan_masuk($id){
        $perkawinan = Perkawinan::find($id);

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
                    $banjar_adat_id = session()->get('banjar_adat_id');
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
                $banjar_adat_id = session()->get('banjar_adat_id');
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
        return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan berhasil disahkan');
    }
    //KELUAR BANJAR ADAT

    //KELUAR BANJAR ADAT HANDLER
    public function edit_perkawinan_keluar($id){
        $perkawinan = Perkawinan::with('purusa.penduduk', 'pradana.penduduk')->find($id);
        //SET NAMA LENGKAP PURUSA & AYAH IBU
        //PURUSA
        $nama = '';
        if($perkawinan->purusa->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->purusa->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->purusa->penduduk->nama;
        if($perkawinan->purusa->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->purusa->penduduk->gelar_belakang;
        }
        $perkawinan->purusa->penduduk->nama = $nama;

        //AYAH PURUSA
        if($perkawinan->purusa->penduduk->ayah){
            $nama = '';
            if($perkawinan->purusa->penduduk->ayah->gelar_depan != ''){
                $nama = $nama.$perkawinan->purusa->ayah->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->purusa->penduduk->ayah->nama;
            if($perkawinan->purusa->penduduk->ayah->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->purusa->penduduk->ayah->gelar_belakang;
            }
            $perkawinan->purusa->penduduk->ayah->nama = $nama;
        }

        //IBU PURUSA
        if($perkawinan->purusa->penduduk->ibu){
            $nama = '';
            if($perkawinan->purusa->penduduk->ibu->gelar_depan != ''){
                $nama = $nama.$perkawinan->purusa->penduduk->ibu->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->purusa->penduduk->ibu->nama;
            if($perkawinan->purusa->penduduk->ibu->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->purusa->penduduk->ibu->gelar_belakang;
            }
            $perkawinan->purusa->penduduk->ibu->nama = $nama;
        }

        //SET NAMA LENGKAP PRADANA & AYAH IBU
        //PRADANA
        $nama = '';
        if($perkawinan->pradana->penduduk->gelar_depan != ''){
            $nama = $nama.$perkawinan->pradana->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$perkawinan->pradana->penduduk->nama;
        if($perkawinan->pradana->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$perkawinan->pradana->penduduk->gelar_belakang;
        }
        $perkawinan->pradana->penduduk->nama = $nama;

        //AYAH PRADANA
        if($perkawinan->pradana->penduduk->ayah){
            $nama = '';
            if($perkawinan->pradana->penduduk->ayah->gelar_depan != ''){
                $nama = $nama.$perkawinan->pradana->ayah->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->pradana->penduduk->ayah->nama;
            if($perkawinan->pradana->penduduk->ayah->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->pradana->penduduk->ayah->gelar_belakang;
            }
            $perkawinan->pradana->penduduk->ayah->nama = $nama;
        }

        //IBU PRADANA
        if($perkawinan->pradana->penduduk->ibu){
            $nama = '';
            if($perkawinan->pradana->penduduk->ibu->gelar_depan != ''){
                $nama = $nama.$perkawinan->pradana->penduduk->ibu->gelar_depan;
            }
            $nama = $nama.' '.$perkawinan->pradana->penduduk->ibu->nama;
            if($perkawinan->pradana->penduduk->ibu->gelar_belakang != ''){
                $nama = $nama.', '.$perkawinan->pradana->penduduk->ibu->gelar_belakang;
            }
            $perkawinan->pradana->penduduk->ibu->nama = $nama;
        }
        return view('pages.banjar.perkawinan.detail_keluar_banjar', compact('perkawinan'));
    }

    public function tolak_perkawinan_keluar($id, Request $request){
        $validator = Validator::make($request->all(), [
            'alasan_penolakan' => 'required',
        ],[
            'alasan_penolakan.required' => "Alasan wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $perkawinan = Perkawinan::find($id);
        $perkawinan->alasan_penolakan = $request->alasan_penolakan;
        $perkawinan->status_perkawinan = '2';
        $perkawinan->update();

        /**
         * create notif baru
         */

        $notifikasi = new Notifikasi();
        $notifikasi->notif_tolak_perkawinan_beda_banjar_adat($perkawinan->id);

        return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan keluar berhasil diperbaharui');
    }

    public function konfirmasi_perkawinan_keluar($id){
        //UPDATE DATA PERKAWINAN
        $perkawinan = Perkawinan::find($id);
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
        return redirect()->route('banjar-perkawinan-home')->with('success', 'Perkawinan keluar berhasil dikonfirmasi');

    }
    //KELUAR BANJAR ADAT HANDLER
}
