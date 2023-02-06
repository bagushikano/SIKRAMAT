<?php

namespace App\Http\Controllers\BanjarAdatController;

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
use App\Models\Maperas;
use App\Models\Notifikasi;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penduduk;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class MaperasController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $maperas = Maperas::with('cacah_krama_mipil_lama.penduduk', 'cacah_krama_mipil_baru.penduduk')->where(function ($query) use ($banjar_adat_id) {
            $query->where('banjar_adat_baru_id', $banjar_adat_id)
                ->orWhere('banjar_adat_lama_id', $banjar_adat_id);
        });
    
        if(isset($request->rentang_waktu)){
            $rentang_waktu = explode(' - ', $request->rentang_waktu);
            $start_date = date("Y-m-d", strtotime($rentang_waktu[0]));
            $end_date = date("Y-m-d", strtotime($rentang_waktu[1]));
            $maperas->whereBetween('tanggal_maperas', [$start_date, $end_date])->get();
        }

        if (isset($request->status)) {
            $maperas->where('status_maperas', $request->status);
        }

        if (isset($request->jenis_maperas)) {
            $maperas->whereIn('jenis_maperas', $request->jenis_maperas);
        }

        $maperas = $maperas->orderBy('tanggal_maperas', 'DESC')->get()->filter(function ($item) {
            $banjar_adat_id = session()->get('banjar_adat_id');
            if($item->jenis_maperas == 'satu_banjar_adat'){
                $item->jenis = 'Satu Banjar Adat';
                return $item;
            }
            else if($item->jenis_maperas == 'beda_banjar_adat'){
                if($item->banjar_adat_baru_id == $banjar_adat_id){
                    $item->jenis = 'Masuk Banjar Adat';
                    return $item;
                }else if($item->banjar_adat_lama_id == $banjar_adat_id){
                    if($item->status_maperas == '0' || $item->status_maperas == '1' || $item->status_maperas == '3'){
                        $item->jenis = 'Keluar Banjar Adat';
                        return $item;
                    }
                }
            }
            else if($item->jenis_maperas == 'campuran_masuk'){
                $item->jenis = 'Campuran Masuk';
                return $item;
            }
            else if($item->jenis_maperas == 'campuran_keluar'){
                $item->jenis = 'Campuran Keluar';
                return $item;
            }
        });

        return DataTables::of($maperas)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                $return = '';
                if($data->status_maperas == '0'){
                    $return .= '<span class="badge badge-warning text-wrap px-3 py-1"> Draft </span>';
                }else if($data->status_maperas == '1'){
                    $return .= '<span class="badge badge-info text-wrap px-3 py-1"> Terkonfirmasi </span>';
                }else if($data->status_maperas == '2'){
                    $return .= '<span class="badge badge-danger text-wrap px-3 py-1"> Tidak Terkonfirmasi </span>';
                }else if($data->status_maperas == '3'){
                    $return .= '<span class="badge badge-success text-wrap px-3 py-1"> Sah </span>';
                }
                return $return;
            })
            ->addColumn('link', function ($data) {
                $return = '';
                if($data->jenis_maperas == 'satu_banjar_adat' || $data->jenis_maperas == 'campuran_masuk' || $data->jenis_maperas == 'campuran_keluar'){
                    if($data->status_maperas == '0' || $data->status_maperas == '1'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-warning" href="'.route('banjar-maperas-edit', $data->id).'"><i class="fas fa-edit mr-2"></i>Edit</a>';
                        $return .= '<button class="dropdown-item text-danger" onclick="delete_maperas('.$data->id.')"><i class="fas fa-trash mr-2"></i> Hapus</button>';
                        $return .= '</div></div>';
                    }else if($data->status_maperas == '3'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-maperas-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }
                }else if($data->jenis == 'Masuk Banjar Adat'){
                    if($data->status_maperas == '0'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-warning" href="'.route('banjar-maperas-edit', $data->id).'"><i class="fas fa-edit mr-2"></i>Edit</a>';
                        $return .= '<button class="dropdown-item text-danger" type="button" onclick="delete_maperas('.$data->id.')"><i class="fas fa-trash mr-2"></i> Hapus</button>';
                        $return .= '</div></div>';
                    }else if($data->status_maperas == '1'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-maperas-masuk-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }else if($data->status_maperas == '2'){
                        $return .= '<button class="btn btn-warning btn-sm mr-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Peringatan" onclick="tindakan('.$data->id.', \''.$data->nomor_maperas.'\', \''.$data->alasan_penolakan.'\')"><i class="fas fa-exclamation-triangle"></i></button>';
                    }else if($data->status_maperas == '3'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-maperas-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }
                }else if($data->jenis == 'Keluar Banjar Adat'){
                    if($data->status_maperas == '0'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-maperas-keluar-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';
                    }else if($data->status_maperas == '1' || $data->status_maperas == '3'){
                        $return .= '<div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle" id="pilih_tindakan" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tindakan</button><div class="dropdown-menu animated--fade-in-up" aria-labelledby="dropdownFadeInUp">';
                        $return .= '<a class="dropdown-item text-primary" href="'.route('banjar-maperas-detail', $data->id).'"><i class="fas fa-eye mr-2"></i>Detail</a>';
                        $return .= '</div></div>';                    } 
                }
                return $return;
            })
            ->rawColumns(['status', 'link'])
            ->make(true);
    }

    public function datatable_krama_mipil_lama(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        if(isset($request->banjar_adat_id)){
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $request->banjar_adat_id)->where('status', '1')->where('id', '!=', $request->krama_mipil_baru_id)->orderBy('tanggal_registrasi', 'DESC')->get()->map(function ($item){
                $item->anggota_keluarga = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $item->id)->get();
                return $item;
            });
        }else{
            $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->where('id', '!=', $request->krama_mipil_baru_id)->orderBy('tanggal_registrasi', 'DESC')->get()->map(function ($item){
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
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_mipil_lama('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function datatable_krama_mipil_baru(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->where('id', '!=', $request->krama_mipil_lama_id)->orderBy('tanggal_registrasi', 'DESC')->get()->map(function ($item){
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
            $return = '<button type="button" class="btn btn-success btn-sm"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Pilih" onclick="pilih_krama_mipil_baru('.$data->id.', \''.$nama.'\')"><i class="fas fa-user-check mr-1"></i>Pilih</button>';
            return $return;
        })
        ->rawColumns(['anggota', 'link'])
        ->make(true);
    }

    public function get_daftar_anak($id){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
        ->where('krama_mipil_id', $krama_mipil->id)->where('status', '1')
        ->where('status_hubungan', '!=', 'istri')->where('status_hubungan', '!=', 'suami')
        ->where('status_hubungan', '!=', 'menantu')->where('status_hubungan', '!=', 'mertua')
        ->where('status_hubungan', '!=', 'ayah')->where('status_hubungan', '!=', 'ibu')->get();

        //SET NAMA LENGKAP ANGGOTA
        foreach($anggota_krama_mipil as $anggota){
            $nama = '';
            if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
            if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $anggota->cacah_krama_mipil->penduduk->nama = $nama;
        }

        return response()->json([
            'anggota_krama_mipil' => $anggota_krama_mipil
        ]);
    }

    public function get_orangtua_lama_anak($id){
        $cacah_krama_mipil = CacahKramaMipil::with('penduduk.ayah', 'penduduk.ibu')->find($id);
        if($cacah_krama_mipil->penduduk->ayah_kandung_id){
            //SET NAMA LENGKAP AYAH
            $nama = '';
            if($cacah_krama_mipil->penduduk->ayah->gelar_depan != ''){
                $nama = $nama.$cacah_krama_mipil->penduduk->ayah->gelar_depan.' ';
            }
            $nama = $nama.$cacah_krama_mipil->penduduk->ayah->nama;
            if($cacah_krama_mipil->penduduk->ayah->gelar_belakang != ''){
                $nama = $nama.', '.$cacah_krama_mipil->penduduk->ayah->gelar_belakang;
            }
            $nama_ayah_lama = $nama;
        }else{
            $nama_ayah_lama = '-';
        }

        if($cacah_krama_mipil->penduduk->ibu_kandung_id){
            //SET NAMA LENGKAP IBU
            $nama = '';
            if($cacah_krama_mipil->penduduk->ibu->gelar_depan != ''){
                $nama = $nama.$cacah_krama_mipil->penduduk->ibu->gelar_depan.' ';
            }
            $nama = $nama.$cacah_krama_mipil->penduduk->ibu->nama;
            if($cacah_krama_mipil->penduduk->ibu->gelar_belakang != ''){
                $nama = $nama.', '.$cacah_krama_mipil->penduduk->ibu->gelar_belakang;
            }
            $cacah_krama_mipil->penduduk->ibu->nama = $nama;
            $nama_ibu_lama = $nama;
        }else{
            $nama_ibu_lama = '-';
        }

        return response()->json([
            'nama_ayah_lama' => $nama_ayah_lama,
            'nama_ibu_lama' => $nama_ibu_lama 
        ]);
    }

    public function get_orangtua_baru_anak($id){
        //GET KRAMA MIPIL DAN ANGGOTA
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
        ->where('krama_mipil_id', $krama_mipil->id)->where('status', '1')->get();

        //SET NAMA LENGKAP KRAMA MIPIL
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        //DECLARE COLLECTION CALON AYAH DAN IBU BARU
        $ayah = new Collection();
        $ibu = new Collection();

        if($krama_mipil->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
            $ayah->push($krama_mipil->cacah_krama_mipil);
        }else{
            $ibu->push($krama_mipil->cacah_krama_mipil);
        }

        //SET NAMA LENGKAP ANGGOTA + ASSIGN AS AYAH IBU
        foreach($anggota_krama_mipil as $anggota){
            //SET NAMA LENGKAP
            $nama = '';
            if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
            if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $anggota->cacah_krama_mipil->penduduk->nama = $nama;

            //ASSIGN AS AYAH IBU
            if($anggota->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                $ayah->push($anggota->cacah_krama_mipil);
            }else{
                $ibu->push($anggota->cacah_krama_mipil);
            }
        }

        return response()->json([
            'ayah' => $ayah,
            'ibu' => $ibu
        ]);
    }

    public function index(){
        return view('pages.banjar.maperas.maperas');
    }

    public function create($jenis_maperas){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        if($jenis_maperas == 'satu_banjar_adat'){
            return view('pages.banjar.maperas.create_satu_banjar_adat');
        }
        else if($jenis_maperas == 'beda_banjar_adat'){
            $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
            return view('pages.banjar.maperas.create_beda_banjar_adat', compact('kabupatens'));
        }
        else if($jenis_maperas == 'campuran_masuk'){
            $pekerjaans = Pekerjaan::get();
            $pendidikans = Pendidikan::get();
            $provinsis = Provinsi::get();
            $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
            return view('pages.banjar.maperas.create_campuran_masuk', compact('pekerjaans', 'pendidikans', 'provinsis', 'desas'));        
        }
        else if($jenis_maperas == 'campuran_keluar'){
            $provinsis = Provinsi::get();
            return view('pages.banjar.maperas.create_campuran_keluar', compact('provinsis'));        
        }
        else{
            return redirect()->back();
        }
    }

    public function store_satu_banjar_adat($status, Request $request){
        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'krama_mipil_lama' => 'required',
            'krama_mipil_baru' => 'required',
            'anak' => 'required',
            'ayah_baru' => 'required',
            'ibu_baru' => 'required',
            'nama_pemuput' => 'required',
            'file_bukti_maperas' => 'required',
            'tanggal_maperas' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',
        ],[
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'ayah_baru.required' => "Ayah Baru wajib dipilih",
            'ibu_baru.required' => "Ibu Baru wajib dipilih",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'file_bukti_maperas.required' => "Bukti Maperas wajib diunggah",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
            'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($request->krama_mipil_lama);
        $krama_mipil_baru = KramaMipil::find($request->krama_mipil_baru);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($request->anak);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);
        $cacah_krama_mipil_baru = CacahKramaMipil::find($request->anak);

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
        $ayah_baru = CacahKramaMipil::find($request->ayah_baru);
        $ibu_baru = CacahKramaMipil::find($request->ibu_baru);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);

        //STATUS MAPERAS DRAFT/SAH
        if($status == '0'){
            $maperas = new Maperas();
            $maperas->jenis_maperas = 'satu_banjar_adat';
            $maperas->nomor_maperas = $request->nomor_bukti_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
            $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
            $maperas->cacah_krama_mipil_lama_id = $request->anak;
            $maperas->cacah_krama_mipil_baru_id = $request->anak;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $request->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $request->ayah_baru;
            $maperas->ibu_baru_id = $request->ibu_baru;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
            $maperas->nama_pemuput = $request->nama_pemuput;
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
            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil ditambahkan');
        }else{
            //SIMPAN MAPERAS
            $maperas = new Maperas();
            $maperas->jenis_maperas = 'satu_banjar_adat';
            $maperas->nomor_maperas = $request->nomor_bukti_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
            $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
            $maperas->cacah_krama_mipil_lama_id = $request->anak;
            $maperas->cacah_krama_mipil_baru_id = $request->anak;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $request->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $request->ayah_baru;
            $maperas->ibu_baru_id = $request->ibu_baru;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
            $maperas->nama_pemuput = $request->nama_pemuput;
            $maperas->status_maperas = '3';
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
                        $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_maperas));
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
                $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_maperas));
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

            //UBAH MAPERAS
            $maperas->krama_mipil_baru_id = $krama_mipil_baru_copy->id;
            $maperas->update();

            //RETURN BACK
            return redirect()->route('banjar-maperas-home')->with('success', 'Data Maperas berhasil ditambahkan');
        }
    }

    public function store_beda_banjar_adat($status, Request $request){
        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'krama_mipil_lama' => 'required',
            'krama_mipil_baru' => 'required',
            'anak' => 'required',
            'ayah_baru' => 'required',
            'ibu_baru' => 'required',
            'nama_pemuput' => 'required',
            'file_bukti_maperas' => 'required',
            'tanggal_maperas' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',
        ],[
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'ayah_baru.required' => "Ayah Baru wajib dipilih",
            'ibu_baru.required' => "Ibu Baru wajib dipilih",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'file_bukti_maperas.required' => "File Bukti Maperas wajib diunggah",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
            'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($request->krama_mipil_lama);
        $krama_mipil_baru = KramaMipil::find($request->krama_mipil_baru);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($request->anak);
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
        $ayah_baru = CacahKramaMipil::find($request->ayah_baru);
        $ibu_baru = CacahKramaMipil::find($request->ibu_baru);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);

        //STATUS MAPERAS DRAFT/SAH
        if($status == '0'){
            $maperas = new Maperas();
            $maperas->jenis_maperas = 'beda_banjar_adat';
            $maperas->nomor_maperas = $request->nomor_bukti_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
            $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
            $maperas->cacah_krama_mipil_lama_id = $request->anak;
            $maperas->cacah_krama_mipil_baru_id = $request->anak;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $request->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $request->ayah_baru;
            $maperas->ibu_baru_id = $request->ibu_baru;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
            $maperas->nama_pemuput = $request->nama_pemuput;
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
            
            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil ditambahkan');
        }
    }

    public function store_campuran_masuk($status, Request $request){
        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'krama_mipil_baru' => 'required',
            'ayah_baru' => 'required',
            'ibu_baru' => 'required',
            'nama_pemuput' => 'required',
            'file_bukti_maperas' => 'required',
            'tanggal_maperas' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',

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
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'ayah_baru.required' => "Ayah Baru wajib dipilih",
            'ibu_baru.required' => "Ibu Baru wajib dipilih",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'file_bukti_maperas.required' => "Bukti Maperas wajib diunggah",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
            'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
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
            'alamat.required' => "Alamat Asal wajib diisi",
            'provinsi.required' => "Provinsi Asal wajib dipilih",
            'kabupaten.required' => "Kabupaten Asal wajib dipilih",
            'kecamatan.required' => "Kecamatan Asal wajib dipilih",
            'desa.required' => "Desa/Kelurahan Asal wajib dipilih",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

        //GET KRAMA MIPIL
        $krama_mipil_baru = KramaMipil::find($request->krama_mipil_baru);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ORTU BARU
        $ayah_baru = CacahKramaMipil::find($request->ayah_baru);
        $ibu_baru = CacahKramaMipil::find($request->ibu_baru);
        $penduduk_ayah_baru = Penduduk::find($ayah_baru->penduduk_id);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);

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
        $penduduk->koordinat_alamat = $penduduk_ayah_baru->koordinat_alamat;
        $penduduk->alamat = $penduduk_ayah_baru->alamat;
        $penduduk->desa_id = $penduduk_ayah_baru->desa_id;
        $penduduk->ayah_kandung_id = $ayah_baru->penduduk->id;
        $penduduk->ibu_kandung_id = $ibu_baru->penduduk->id;
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

        //INSERT CACAH KRAMA MIPIL
        $cacah_krama_mipil = new CacahKramaMipil();
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_mipil->tempekan_id = $ayah_baru->tempekan_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_maperas));
        $cacah_krama_mipil->jenis_kependudukan = $ayah_baru->jenis_kependudukan;
        $cacah_krama_mipil->status = '0';
        if($ayah_baru->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $ayah_baru->banjar_dinas_id;
        }
        $cacah_krama_mipil->save();

        $maperas = new Maperas();
        $maperas->jenis_maperas = 'campuran_masuk';
        $maperas->nomor_maperas = $request->nomor_bukti_maperas;
        $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
        $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
        $maperas->cacah_krama_mipil_baru_id = $cacah_krama_mipil->id;
        $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
        $maperas->desa_adat_baru_id = $desa_adat_baru->id;
        $maperas->ayah_baru_id = $request->ayah_baru;
        $maperas->ibu_baru_id = $request->ibu_baru;
        $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $maperas->nama_pemuput = $request->nama_pemuput;
        $maperas->keterangan = $request->keterangan;
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
        $maperas->nik_ayah_lama = $request->nik_ayah;
        $maperas->nik_ibu_lama = $request->nik_ibu;
        $maperas->nama_ayah_lama = $request->nama_ayah;
        $maperas->nama_ibu_lama = $request->nama_ibu;
        $maperas->alamat_asal = $request->alamat;
        $maperas->desa_asal_id = $request->desa;
        $maperas->agama_lama = $request->agama;
        if($request->file('file_sudhi_wadhani')!=""){
            $file = $request->file('file_sudhi_wadhani');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_sudhi_wadhani';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            $maperas->file_sudhi_wadhani = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $maperas->save();

        if($status == '0'){
            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil ditambahkan');
        }else{
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
            $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_maperas));
            $anggota_krama_mipil_lama_copy->status = '1';
            $anggota_krama_mipil_lama_copy->save();

            //UBAH MAPERAS
            $maperas->krama_mipil_baru_id = $krama_mipil_baru_copy->id;
            $maperas->update();

            //MASUKKAN ANAK SBG ANGGOTA
            return redirect()->route('banjar-maperas-home')->with('success', 'Maperas berhasil ditambahkan');
        }
    }

    public function store_campuran_keluar($status, Request $request){
        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'krama_mipil_lama' => 'required',
            'anak' => 'required',
            'file_bukti_maperas' => 'required',
            'tanggal_maperas' => 'required',
            'nik_ayah' => 'required',
            'nik_ibu' => 'required',
            'nama_ayah' => 'required',
            'nama_ibu' => 'required',
            'agama' => 'required',
            'alamat' => 'required',
            'desa_asal' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'file_akta_pengangkatan_anak' => 'required_with:nomor_akta_pengangkatan_anak',
        ],[
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nik_ayah.required' => "NIK Ayah Baru wajib diisi",
            'nik_ibu.required' => "NIK Ibu Baru wajib diisi",
            'nama_ayah.required' => "Nama Ayah Baru wajib diisi",
            'nama_ibu.required' => "Nama Ibu Baru wajib diisi",
            'alamat.required' => "Alamat Asal wajib diisi",
            'agama.required' => "Agama wajib dipilih",
            'desa_asal.required' => "Desa/Kelurahan Asal wajib dipilih",
            'file_bukti_maperas.required' => "Bukti Maperas wajib diunggah",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
            'file_akta_pengangkatan_anak.required_with' => "File Akta Pengangkatan Anak wajib diisi",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($request->krama_mipil_lama);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($request->anak);
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
        $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);

        $maperas = new Maperas();
        $maperas->jenis_maperas = 'campuran_keluar';
        $maperas->nomor_maperas = $request->nomor_bukti_maperas;
        $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
        $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
        $maperas->cacah_krama_mipil_lama_id = $request->anak;
        $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
        $maperas->desa_adat_lama_id = $desa_adat_lama->id;
        $maperas->keterangan = $request->keterangan;
        if($ayah_lama){
            $maperas->ayah_lama_id = $ayah_lama->id;
        }
        if($ibu_lama){
            $maperas->ibu_lama_id = $ibu_lama->id;
        }
        $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
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
        $maperas->nik_ayah_baru = $request->nik_ayah;
        $maperas->nik_ibu_baru = $request->nik_ibu;
        $maperas->nama_ayah_baru = $request->nama_ayah;
        $maperas->nama_ibu_baru = $request->nama_ibu;
        $maperas->agama_baru = $request->agama;
        $maperas->alamat_asal = $request->alamat;
        $maperas->desa_asal_id = $request->desa_asal;
        $maperas->save();

        if($status == '0'){
            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil ditambahkan');
        }else{
            //UPDATE MAPERAS
            $maperas->status_maperas = '3';
            $maperas->update();

            //NONAKTIFKAN CACAH
            $cacah_krama_mipil_lama->status = '0';
            $cacah_krama_mipil_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_maperas));
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
                    $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_maperas));
                    $anggota_lama->alasan_keluar = 'Maperas (Campuran Keluar)';
                }
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }

            //LEBUR KK LAMA
            $krama_mipil_lama->status = '0';
            $krama_mipil_lama->update();
            return redirect()->route('banjar-maperas-home')->with('success', 'Maperas berhasil ditambahkan');
        }
    }

    public function edit($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $maperas = Maperas::find($id);

        if($maperas->jenis_maperas == 'satu_banjar_adat'){
            //VALIDASI
            if(($maperas->banjar_adat_lama_id != $banjar_adat_id) && ($maperas->banjar_adat_baru_id != $banjar_adat_id)){
                return redirect()->back();
            }
            if($maperas->status_maperas == '3'){
                return redirect()->back();
            }

            //KRAMA MIPIL LAMA
            $krama_mipil_lama = KramaMipil::with('cacah_krama_mipil.penduduk')->find($maperas->krama_mipil_lama_id);
            //SET NAMA KRAMA MIPIL LAMA
            $nama = '';
            if($krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil_lama->cacah_krama_mipil->penduduk->nama;
            if($krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $krama_mipil_lama->cacah_krama_mipil->penduduk->nama = $nama;

            //DAFTAR ANAK KRAMA MIPIL LAMA
            $anggota_krama_mipil_lama = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
            ->where('krama_mipil_id', $krama_mipil_lama->id)->where('status', '1')
            ->where('status_hubungan', '!=', 'istri')->where('status_hubungan', '!=', 'suami')
            ->where('status_hubungan', '!=', 'menantu')->where('status_hubungan', '!=', 'mertua')
            ->where('status_hubungan', '!=', 'ayah')->where('status_hubungan', '!=', 'ibu')->get();
    
            //SET NAMA LENGKAP ANGGOTA
            foreach($anggota_krama_mipil_lama as $anggota){
                $nama = '';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;
            }

            //DATA ORANG TUA LAMA
            $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
            $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
            if($ayah_lama){
                //SET NAMA LENGKAP AYAH LAMA
                $nama = '';
                if($ayah_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ayah_lama->penduduk->nama;
                if($ayah_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
                }
                $ayah_lama->penduduk->nama = $nama;
            }
            if($ibu_lama){
                //SET NAMA LENGKAP IBU LAMA
                $nama = '';
                if($ibu_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ibu_lama->penduduk->nama;
                if($ibu_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
                }
                $ibu_lama->penduduk->nama = $nama;
            }

            //KRAMA MIPIL BARU
            $krama_mipil_baru = KramaMipil::with('cacah_krama_mipil.penduduk')->find($maperas->krama_mipil_baru_id);
            //SET NAMA KRAMA MIPIL BARU
            $nama = '';
            if($krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil_baru->cacah_krama_mipil->penduduk->nama;
            if($krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $krama_mipil_baru->cacah_krama_mipil->penduduk->nama = $nama;

            //DECLARE COLLECTION CALON AYAH DAN IBU BARU
            $ayah_baru = new Collection();
            $ibu_baru = new Collection();

            if($krama_mipil_baru->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                $ayah_baru->push($krama_mipil_baru->cacah_krama_mipil);
            }else{
                $ibu_baru->push($krama_mipil_baru->cacah_krama_mipil);
            }

            //GET ANGGOTA KRAMA MIPIL BARU AS AYAH IBU
            $anggota_krama_mipil_baru = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
            ->where('krama_mipil_id', $krama_mipil_baru->id)->where('status', '1')->get();
            //SET NAMA LENGKAP ANGGOTA + ASSIGN AS AYAH IBU
            foreach($anggota_krama_mipil_baru as $anggota){
                //SET NAMA LENGKAP
                $nama = '';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;

                //ASSIGN AS AYAH IBU
                if($anggota->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                    $ayah_baru->push($anggota->cacah_krama_mipil);
                }else{
                    $ibu_baru->push($anggota->cacah_krama_mipil);
                }
            }
            return view('pages.banjar.maperas.edit_satu_banjar_adat', compact('maperas', 'krama_mipil_lama', 'anggota_krama_mipil_lama', 'ayah_lama', 'ibu_lama', 'krama_mipil_baru', 'ayah_baru', 'ibu_baru'));
        }
        else if($maperas->jenis_maperas == 'beda_banjar_adat'){
            //VALIDASI
            if(($maperas->banjar_adat_lama_id == $banjar_adat_id) && ($maperas->banjar_adat_baru_id != $banjar_adat_id)){
                return redirect()->back();
            }
            if($maperas->status_maperas == '3' || $maperas->status_maperas == '1'){
                return redirect()->back();
            }

            //KRAMA MIPIL LAMA
            $krama_mipil_lama = KramaMipil::with('cacah_krama_mipil.penduduk')->find($maperas->krama_mipil_lama_id);
            //SET NAMA KRAMA MIPIL LAMA
            $nama = '';
            if($krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil_lama->cacah_krama_mipil->penduduk->nama;
            if($krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $krama_mipil_lama->cacah_krama_mipil->penduduk->nama = $nama;

            //DAFTAR ANAK KRAMA MIPIL LAMA
            $anggota_krama_mipil_lama = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
            ->where('krama_mipil_id', $krama_mipil_lama->id)->where('status', '1')
            ->where('status_hubungan', '!=', 'istri')->where('status_hubungan', '!=', 'suami')
            ->where('status_hubungan', '!=', 'menantu')->where('status_hubungan', '!=', 'mertua')
            ->where('status_hubungan', '!=', 'ayah')->where('status_hubungan', '!=', 'ibu')->get();
    
            //SET NAMA LENGKAP ANGGOTA
            foreach($anggota_krama_mipil_lama as $anggota){
                $nama = '';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;
            }

            //DATA ORANG TUA LAMA
            $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
            $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
            if($ayah_lama){
                //SET NAMA LENGKAP AYAH LAMA
                $nama = '';
                if($ayah_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ayah_lama->penduduk->nama;
                if($ayah_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
                }
                $ayah_lama->penduduk->nama = $nama;
            }
            if($ibu_lama){
                //SET NAMA LENGKAP IBU LAMA
                $nama = '';
                if($ibu_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ibu_lama->penduduk->nama;
                if($ibu_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
                }
                $ibu_lama->penduduk->nama = $nama;
            }

            //KRAMA MIPIL BARU
            $krama_mipil_baru = KramaMipil::with('cacah_krama_mipil.penduduk')->find($maperas->krama_mipil_baru_id);
            //SET NAMA KRAMA MIPIL BARU
            $nama = '';
            if($krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil_baru->cacah_krama_mipil->penduduk->nama;
            if($krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $krama_mipil_baru->cacah_krama_mipil->penduduk->nama = $nama;

            //DECLARE COLLECTION CALON AYAH DAN IBU BARU
            $ayah_baru = new Collection();
            $ibu_baru = new Collection();

            if($krama_mipil_baru->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                $ayah_baru->push($krama_mipil_baru->cacah_krama_mipil);
            }else{
                $ibu_baru->push($krama_mipil_baru->cacah_krama_mipil);
            }

            //GET ANGGOTA KRAMA MIPIL BARU AS AYAH IBU
            $anggota_krama_mipil_baru = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
            ->where('krama_mipil_id', $krama_mipil_baru->id)->where('status', '1')->get();
            //SET NAMA LENGKAP ANGGOTA + ASSIGN AS AYAH IBU
            foreach($anggota_krama_mipil_baru as $anggota){
                //SET NAMA LENGKAP
                $nama = '';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;

                //ASSIGN AS AYAH IBU
                if($anggota->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                    $ayah_baru->push($anggota->cacah_krama_mipil);
                }else{
                    $ibu_baru->push($anggota->cacah_krama_mipil);
                }
            }

            //MASTER DAERAH
            $banjar_adat = BanjarAdat::find($maperas->banjar_adat_lama_id);
            $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
            $kecamatan = Kecamatan::find($desa_adat->kecamatan_id);
            $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);

            $kabupatens = Kabupaten::where('provinsi_id', '51')->get();
            $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
            $desa_adats = DesaAdat::where('kecamatan_id', $kecamatan->id)->get();
            $banjar_adats = BanjarAdat::where('desa_adat_id', $desa_adat->id)->where('id', '!=', $banjar_adat_id)->get();
            return view('pages.banjar.maperas.edit_beda_banjar_adat', compact(
                'maperas', 'krama_mipil_lama', 'anggota_krama_mipil_lama', 'ayah_lama', 'ibu_lama', 
                'krama_mipil_baru', 'ayah_baru', 'ibu_baru', 
                'kabupatens', 'kecamatans', 'desa_adats', 'banjar_adats',
                'kabupaten', 'kecamatan', 'desa_adat', 'banjar_adat' 
            ));
        }
        elseif($maperas->jenis_maperas == 'campuran_masuk'){
            //VALIDASI
            if($maperas->banjar_adat_baru_id != $banjar_adat_id){
                return redirect()->back();
            }
            if($maperas->status_maperas == '3'){
                return redirect()->back();
            }

            $cacah_krama_mipil = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
            $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
            //SET NAMA LENGKAP PENDUDUK
            $nama = '';
            if($penduduk->gelar_depan != ''){
                $nama = $nama.$penduduk->gelar_depan.' ';
            }
            $nama = $nama.$penduduk->nama;
            if($penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$penduduk->gelar_belakang;
            }
            $penduduk->nama_tercetak = $nama;

            //KRAMA MIPIL BARU
            $krama_mipil_baru = KramaMipil::with('cacah_krama_mipil.penduduk')->find($maperas->krama_mipil_baru_id);
            //SET NAMA KRAMA MIPIL BARU
            $nama = '';
            if($krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil_baru->cacah_krama_mipil->penduduk->nama;
            if($krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$krama_mipil_baru->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $krama_mipil_baru->cacah_krama_mipil->penduduk->nama = $nama;

            //DECLARE COLLECTION CALON AYAH DAN IBU BARU
            $ayah_baru = new Collection();
            $ibu_baru = new Collection();

            if($krama_mipil_baru->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                $ayah_baru->push($krama_mipil_baru->cacah_krama_mipil);
            }else{
                $ibu_baru->push($krama_mipil_baru->cacah_krama_mipil);
            }

            //GET ANGGOTA KRAMA MIPIL BARU AS AYAH IBU
            $anggota_krama_mipil_baru = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
            ->where('krama_mipil_id', $krama_mipil_baru->id)->where('status', '1')->get();
            //SET NAMA LENGKAP ANGGOTA + ASSIGN AS AYAH IBU
            foreach($anggota_krama_mipil_baru as $anggota){
                //SET NAMA LENGKAP
                $nama = '';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;

                //ASSIGN AS AYAH IBU
                if($anggota->cacah_krama_mipil->penduduk->jenis_kelamin == 'laki-laki'){
                    $ayah_baru->push($anggota->cacah_krama_mipil);
                }else{
                    $ibu_baru->push($anggota->cacah_krama_mipil);
                }
            }

            //MASTER LAINNYA
            $desa_asal = DesaDinas::find($maperas->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

            $desa_asals = DesaDinas::where('kecamatan_id', $kecamatan_asal->id)->get();
            $kecamatan_asals = Kecamatan::where('kabupaten_id', $kabupaten_asal->id)->get();
            $kabupaten_asals = Kabupaten::where('provinsi_id', $provinsi_asal->id)->get();

            $pekerjaans = Pekerjaan::get();
            $pendidikans = Pendidikan::get();
            $provinsis = Provinsi::get();
            $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
            return view('pages.banjar.maperas.edit_campuran_masuk', compact(
                'maperas', 'cacah_krama_mipil', 'penduduk',
                'krama_mipil_baru', 'ayah_baru', 'ibu_baru',
                'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal',
                'desa_asals', 'kecamatan_asals', 'kabupaten_asals', 
                'pekerjaans', 'pendidikans', 'provinsis', 'desas')); 

        }
        elseif($maperas->jenis_maperas == 'campuran_keluar'){
            //VALIDASI
            if($maperas->banjar_adat_lama_id != $banjar_adat_id){
                return redirect()->back();
            }
            if($maperas->status_maperas == '3'){
                return redirect()->back();
            }

            $cacah_krama_mipil = CacahKramaMipil::find($maperas->cacah_krama_mipil_lama_id);
            $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
            //SET NAMA LENGKAP PENDUDUK
            $nama = '';
            if($penduduk->gelar_depan != ''){
                $nama = $nama.$penduduk->gelar_depan.' ';
            }
            $nama = $nama.$penduduk->nama;
            if($penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$penduduk->gelar_belakang;
            }
            $penduduk->nama_tercetak = $nama;

            //KRAMA MIPIL LAMA
            $krama_mipil_lama = KramaMipil::with('cacah_krama_mipil.penduduk')->find($maperas->krama_mipil_lama_id);
            //SET NAMA KRAMA MIPIL LAMA
            $nama = '';
            if($krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$krama_mipil_lama->cacah_krama_mipil->penduduk->nama;
            if($krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$krama_mipil_lama->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $krama_mipil_lama->cacah_krama_mipil->penduduk->nama = $nama;

            //GET ANGGOTA KRAMA MIPIL LAMA AS AYAH IBU
            $anggota_krama_mipil_lama = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')
            ->where('krama_mipil_id', $krama_mipil_lama->id)->where('status', '1')->get();
            //SET NAMA LENGKAP ANGGOTA + ASSIGN AS AYAH IBU
            foreach($anggota_krama_mipil_lama as $anggota){
                //SET NAMA LENGKAP
                $nama = '';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;
            }

            //DATA ORANG TUA LAMA
            $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
            $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
            if($ayah_lama){
                //SET NAMA LENGKAP AYAH LAMA
                $nama = '';
                if($ayah_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ayah_lama->penduduk->nama;
                if($ayah_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
                }
                $ayah_lama->penduduk->nama = $nama;
            }
            if($ibu_lama){
                //SET NAMA LENGKAP IBU LAMA
                $nama = '';
                if($ibu_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ibu_lama->penduduk->nama;
                if($ibu_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
                }
                $ibu_lama->penduduk->nama = $nama;
            }

            //MASTER LAINNYA
            $desa_asal = DesaDinas::find($maperas->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

            $desa_asals = DesaDinas::where('kecamatan_id', $kecamatan_asal->id)->get();
            $kecamatan_asals = Kecamatan::where('kabupaten_id', $kabupaten_asal->id)->get();
            $kabupaten_asals = Kabupaten::where('provinsi_id', $provinsi_asal->id)->get();

            $pekerjaans = Pekerjaan::get();
            $pendidikans = Pendidikan::get();
            $provinsis = Provinsi::get();
            $desas = DesaDinas::where('kecamatan_id', $desa_adat->kecamatan_id)->get();
            return view('pages.banjar.maperas.edit_campuran_keluar', compact(
                'maperas', 'cacah_krama_mipil', 'penduduk',
                'krama_mipil_lama', 'anggota_krama_mipil_lama', 'ayah_lama', 'ibu_lama',
                'desa_asal', 'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal',
                'desa_asals', 'kecamatan_asals', 'kabupaten_asals', 
                'pekerjaans', 'pendidikans', 'provinsis', 'desas')); 

        }
    }

    public function update_satu_banjar_adat($id, $status, Request $request){
        $maperas = Maperas::find($id);
        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'nomor_bukti_maperas' => [
                Rule::unique('tb_maperas', 'nomor_maperas')->ignore($maperas->id),
            ],
            'krama_mipil_lama' => 'required',
            'krama_mipil_baru' => 'required',
            'anak' => 'required',
            'ayah_baru' => 'required',
            'ibu_baru' => 'required',
            'nama_pemuput' => 'required',
            'tanggal_maperas' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'nomor_akta_pengangkatan_anak' => [
                Rule::unique('tb_maperas', 'nomor_akta_pengangkatan_anak')->ignore($maperas->id),
            ],
        ],[
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'ayah_baru.required' => "Ayah Baru wajib dipilih",
            'ibu_baru.required' => "Ibu Baru wajib dipilih",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($request->krama_mipil_lama);
        $krama_mipil_baru = KramaMipil::find($request->krama_mipil_baru);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($request->anak);
        $penduduk_anak = Penduduk::find($cacah_krama_mipil_lama->penduduk_id);
        $cacah_krama_mipil_baru = CacahKramaMipil::find($request->anak);

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
        $ayah_baru = CacahKramaMipil::find($request->ayah_baru);
        $ibu_baru = CacahKramaMipil::find($request->ibu_baru);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);

        //STATUS MAPERAS DRAFT/SAH
        if($status == '0'){
            $maperas->nomor_maperas = $request->nomor_bukti_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
            $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
            $maperas->cacah_krama_mipil_lama_id = $request->anak;
            $maperas->cacah_krama_mipil_baru_id = $request->anak;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $request->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $request->ayah_baru;
            $maperas->ibu_baru_id = $request->ibu_baru;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
            $maperas->nama_pemuput = $request->nama_pemuput;
            $maperas->status_maperas = '0';
            if($request->file('file_bukti_maperas')!=""){
                $file = $request->file('file_bukti_maperas');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($maperas->file_bukti_maperas != NULL){
                    $old_path = str_replace("/storage","",$maperas->file_bukti_maperas);
                    Storage::disk('public')->delete($old_path);
                }
                $maperas->file_bukti_maperas = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_akta_pengangkatan_anak')!=""){
                $file = $request->file('file_akta_pengangkatan_anak');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($maperas->file_akta_pengangkatan_anak != NULL){
                    $old_path = str_replace("/storage","",$maperas->file_akta_pengangkatan_anak);
                    Storage::disk('public')->delete($old_path);
                }
                $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $maperas->update();
            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil diperbaharui');
        }else{
            //SIMPAN MAPERAS
            $maperas->nomor_maperas = $request->nomor_bukti_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
            $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
            $maperas->cacah_krama_mipil_lama_id = $request->anak;
            $maperas->cacah_krama_mipil_baru_id = $request->anak;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $request->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $request->ayah_baru;
            $maperas->ibu_baru_id = $request->ibu_baru;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
            $maperas->nama_pemuput = $request->nama_pemuput;
            $maperas->status_maperas = '3';
            if($request->file('file_bukti_maperas')!=""){
                $file = $request->file('file_bukti_maperas');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($maperas->file_bukti_maperas != NULL){
                    $old_path = str_replace("/storage","",$maperas->file_bukti_maperas);
                    Storage::disk('public')->delete($old_path);
                }
                $maperas->file_bukti_maperas = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_akta_pengangkatan_anak')!=""){
                $file = $request->file('file_akta_pengangkatan_anak');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($maperas->file_akta_pengangkatan_anak != NULL){
                    $old_path = str_replace("/storage","",$maperas->file_akta_pengangkatan_anak);
                    Storage::disk('public')->delete($old_path);
                }
                $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $maperas->save();

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
                        $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_maperas));
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
                $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_maperas));
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

            //UBAH MAPERAS
            $maperas->krama_mipil_baru_id = $krama_mipil_baru_copy->id;
            $maperas->update();

            //RETURN BACK
            return redirect()->route('banjar-maperas-home')->with('success', 'Data Maperas berhasil diperbaharui');
        }
    }

    public function update_beda_banjar_adat($id, $status, Request $request){
        $maperas = Maperas::find($id);
        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'nomor_bukti_maperas' => [
                Rule::unique('tb_maperas', 'nomor_maperas')->ignore($maperas->id),
            ],
            'krama_mipil_lama' => 'required',
            'krama_mipil_baru' => 'required',
            'anak' => 'required',
            'ayah_baru' => 'required',
            'ibu_baru' => 'required',
            'nama_pemuput' => 'required',
            'tanggal_maperas' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'nomor_akta_pengangkatan_anak' => [
                Rule::unique('tb_maperas', 'nomor_akta_pengangkatan_anak')->ignore($maperas->id),
            ],
        ],[
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'ayah_baru.required' => "Ayah Baru wajib dipilih",
            'ibu_baru.required' => "Ibu Baru wajib dipilih",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        } 

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

        //GET KRAMA MIPIL
        $krama_mipil_lama = KramaMipil::find($request->krama_mipil_lama);
        $krama_mipil_baru = KramaMipil::find($request->krama_mipil_baru);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ANAK
        $cacah_krama_mipil_lama = CacahKramaMipil::find($request->anak);
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
        $ayah_baru = CacahKramaMipil::find($request->ayah_baru);
        $ibu_baru = CacahKramaMipil::find($request->ibu_baru);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);

        if($maperas->alasan_penolakan != NULL){
            /**
             * create notif baru
             */
            
            $notifikasi = new Notifikasi();
            $notifikasi->notif_edit_maperas_beda_banjar_adat($maperas->id);
        }

        //STATUS MAPERAS DRAFT/SAH
        if($status == '0'){
            $maperas->nomor_maperas = $request->nomor_bukti_maperas;
            $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
            $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
            $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
            $maperas->cacah_krama_mipil_lama_id = $request->anak;
            $maperas->cacah_krama_mipil_baru_id = $request->anak;
            $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
            $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
            $maperas->desa_adat_lama_id = $desa_adat_lama->id;
            $maperas->desa_adat_baru_id = $desa_adat_baru->id;
            $maperas->keterangan = $request->keterangan;
            if($ayah_lama){
                $maperas->ayah_lama_id = $ayah_lama->id;
            }
            if($ibu_lama){
                $maperas->ibu_lama_id = $ibu_lama->id;
            }
            $maperas->ayah_baru_id = $request->ayah_baru;
            $maperas->ibu_baru_id = $request->ibu_baru;
            $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
            $maperas->nama_pemuput = $request->nama_pemuput;
            $maperas->status_maperas = '0';
            if($request->file('file_bukti_maperas')!=""){
                $file = $request->file('file_bukti_maperas');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($maperas->file_bukti_maperas != NULL){
                    $old_path = str_replace("/storage","",$maperas->file_bukti_maperas);
                    Storage::disk('public')->delete($old_path);
                }
                $maperas->file_bukti_maperas = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            if($request->file('file_akta_pengangkatan_anak')!=""){
                $file = $request->file('file_akta_pengangkatan_anak');
                $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                if($maperas->file_akta_pengangkatan_anak != NULL){
                    $old_path = str_replace("/storage","",$maperas->file_akta_pengangkatan_anak);
                    Storage::disk('public')->delete($old_path);
                }
                $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $maperas->alasan_penolakan = NULL;
            $maperas->update();
            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil diperbaharui');
        }
    }

    public function update_campuran_masuk($id, $status, Request $request){
        //GET DATA
        $maperas = Maperas::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'nomor_bukti_maperas' => [
                Rule::unique('tb_maperas', 'nomor_maperas')->ignore($maperas->id),
            ],
            'krama_mipil_baru' => 'required',
            'ayah_baru' => 'required',
            'ibu_baru' => 'required',
            'nama_pemuput' => 'required',
            'tanggal_maperas' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'nomor_akta_pengangkatan_anak' => [
                Rule::unique('tb_maperas', 'nomor_akta_pengangkatan_anak')->ignore($maperas->id),
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
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'krama_mipil_baru.required' => "Krama Mipil Baru wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'ayah_baru.required' => "Ayah Baru wajib dipilih",
            'ibu_baru.required' => "Ibu Baru wajib dipilih",
            'nama_pemuput.required' => "Nama Pemuput wajib diisi",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
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
            'status_maperas.required' => "Status Maperas wajib dipilih",
            'alamat.required' => "Alamat Asal wajib diisi",
            'provinsi.required' => "Provinsi Asal wajib dipilih",
            'kabupaten.required' => "Kabupaten Asal wajib dipilih",
            'kecamatan.required' => "Kecamatan Asal wajib dipilih",
            'desa.required' => "Desa/Kelurahan Asal wajib dipilih",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

        //GET KRAMA MIPIL
        $krama_mipil_baru = KramaMipil::find($request->krama_mipil_baru);

        //GET BANJAR DAN DESA ADAT
        $banjar_adat_baru = BanjarAdat::find($krama_mipil_baru->banjar_adat_id);
        $desa_adat_baru = DesaAdat::find($banjar_adat_baru->desa_adat_id);

        //GET ORTU BARU
        $ayah_baru = CacahKramaMipil::find($request->ayah_baru);
        $ibu_baru = CacahKramaMipil::find($request->ibu_baru);
        $penduduk_ayah_baru = Penduduk::find($ayah_baru->penduduk_id);

        //CONVERT NOMOR MAPERAS
        $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);

        //INSERT PENDUDUK
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
        $penduduk->koordinat_alamat = $penduduk_ayah_baru->koordinat_alamat;
        $penduduk->alamat = $penduduk_ayah_baru->alamat;
        $penduduk->desa_id = $penduduk_ayah_baru->desa_id;
        $penduduk->ayah_kandung_id = $ayah_baru->penduduk->id;
        $penduduk->ibu_kandung_id = $ibu_baru->penduduk->id;
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

        //INSERT CACAH KRAMA MIPIL
        $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
        $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
        $cacah_krama_mipil->tempekan_id = $ayah_baru->tempekan_id;
        $cacah_krama_mipil->penduduk_id = $penduduk->id;
        $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_maperas));
        $cacah_krama_mipil->jenis_kependudukan = $ayah_baru->jenis_kependudukan;
        $cacah_krama_mipil->status = '0';
        if($ayah_baru->jenis_kependudukan == 'adat_&_dinas'){
            $cacah_krama_mipil->banjar_dinas_id = $ayah_baru->banjar_dinas_id;
        }
        $cacah_krama_mipil->update();

        $maperas->nomor_maperas = $request->nomor_bukti_maperas;
        $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
        $maperas->krama_mipil_baru_id = $request->krama_mipil_baru;
        $maperas->cacah_krama_mipil_baru_id = $cacah_krama_mipil->id;
        $maperas->banjar_adat_baru_id = $banjar_adat_baru->id;
        $maperas->desa_adat_baru_id = $desa_adat_baru->id;
        $maperas->ayah_baru_id = $request->ayah_baru;
        $maperas->ibu_baru_id = $request->ibu_baru;
        $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $maperas->nama_pemuput = $request->nama_pemuput;
        $maperas->keterangan = $request->keterangan;
        $maperas->status_maperas = '0';
        if($request->file('file_bukti_maperas')!=""){
            $file = $request->file('file_bukti_maperas');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($maperas->file_bukti_maperas != NULL){
                $old_path = str_replace("/storage","",$maperas->file_bukti_maperas);
                Storage::disk('public')->delete($old_path);
            }
            $maperas->file_bukti_maperas = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_pengangkatan_anak')!=""){
            $file = $request->file('file_akta_pengangkatan_anak');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($maperas->file_akta_pengangkatan_anak != NULL){
                $old_path = str_replace("/storage","",$maperas->file_akta_pengangkatan_anak);
                Storage::disk('public')->delete($old_path);
            }
            $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }

        //INSERT ASAL
        $maperas->nik_ayah_lama = $request->nik_ayah;
        $maperas->nik_ibu_lama = $request->nik_ibu;
        $maperas->nama_ayah_lama = $request->nama_ayah;
        $maperas->nama_ibu_lama = $request->nama_ibu;
        $maperas->alamat_asal = $request->alamat;
        $maperas->desa_asal_id = $request->desa;
        $maperas->agama_lama = $request->agama;
        if($request->file('file_sudhi_wadhani')!=""){
            $file = $request->file('file_sudhi_wadhani');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_sudhi_wadhani';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($maperas->file_sudhi_wadhani != NULL){
                $old_path = str_replace("/storage","",$maperas->file_sudhi_wadhani);
                Storage::disk('public')->delete($old_path);
            }
            $maperas->file_sudhi_wadhani = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        $maperas->update();

        if($status == '0'){
            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil diperbaharui');
        }else{
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
            $anggota_krama_mipil_lama_copy->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_maperas));
            $anggota_krama_mipil_lama_copy->status = '1';
            $anggota_krama_mipil_lama_copy->save();

            //UBAH MAPERAS
            $maperas->krama_mipil_baru_id = $krama_mipil_baru_copy->id;
            $maperas->update();

            //MASUKKAN ANAK SBG ANGGOTA
            return redirect()->route('banjar-maperas-home')->with('success', 'Maperas berhasil diperbaharui');
        }
    }

    public function update_campuran_keluar($id, $status, Request $request){
        $maperas = Maperas::find($id);
        $validator = Validator::make($request->all(), [
            'nomor_bukti_maperas' => 'required|unique:tb_maperas,nomor_maperas|max:50',
            'nomor_bukti_maperas' => [
                Rule::unique('tb_maperas', 'nomor_maperas')->ignore($maperas->id),
            ],
            'krama_mipil_lama' => 'required',
            'anak' => 'required',
            'tanggal_maperas' => 'required',
            'nik_ayah' => 'required',
            'nik_ibu' => 'required',
            'nama_ayah' => 'required',
            'nama_ibu' => 'required',
            'agama' => 'required',
            'alamat' => 'required',
            'desa_asal' => 'required',
            'nomor_akta_pengangkatan_anak' => 'unique:tb_maperas|nullable|max:21',
            'nomor_akta_pengangkatan_anak' => [
                Rule::unique('tb_maperas', 'nomor_akta_pengangkatan_anak')->ignore($maperas->id),
            ],
        ],[
            'nomor_bukti_maperas.required' => "No. Bukti Maperas wajib diisi",
            'nomor_bukti_maperas.unique' => "No. Bukti Maperas telah terdaftar",
            'krama_mipil_lama.required' => "Krama Mipil Lama wajib dipilih",
            'anak.required' => "Anak wajib dipilih",
            'tanggal_maperas.required' => "Tanggal Maperas wajib diisi",
            'nik_ayah.required' => "NIK Ayah Baru wajib diisi",
            'nik_ibu.required' => "NIK Ibu Baru wajib diisi",
            'nama_ayah.required' => "Nama Ayah Baru wajib diisi",
            'nama_ibu.required' => "Nama Ibu Baru wajib diisi",
            'alamat.required' => "Alamat Asal wajib diisi",
            'agama.required' => "Agama wajib dipilih",
            'desa_asal.required' => "Desa/Kelurahan Asal wajib dipilih",
            'nomor_akta_pengangkatan_anak.unique' => "Nomor Akta Pengangkatan Anak telah terdaftar",
            'nomor_akta_pengangkatan_anak.max' => "Nomor Akta Pengangkatan Anak maksimal terdiri dari 21 karakter",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Validasi Tanggal 
        $tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
        $tanggal_sekarang = Carbon::now()->toDateString();
        if($tanggal_maperas > $tanggal_sekarang){
            return back()->withInput()->withErrors(['tanggal_maperas' => 'Tanggal maperas tidak boleh melebihi tanggal sekarang']);
        }

         //GET KRAMA MIPIL
         $krama_mipil_lama = KramaMipil::find($request->krama_mipil_lama);

         //GET BANJAR DAN DESA ADAT
         $banjar_adat_lama = BanjarAdat::find($krama_mipil_lama->banjar_adat_id);
         $desa_adat_lama = DesaAdat::find($banjar_adat_lama->desa_adat_id);
 
         //GET ANAK
         $cacah_krama_mipil_lama = CacahKramaMipil::find($request->anak);
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
         $convert_nomor_maperas = str_replace("/","-",$request->nomor_bukti_maperas);
         $maperas->nomor_maperas = $request->nomor_bukti_maperas;
         $maperas->nomor_akta_pengangkatan_anak = $request->nomor_akta_pengangkatan_anak;
         $maperas->krama_mipil_lama_id = $request->krama_mipil_lama;
         $maperas->cacah_krama_mipil_lama_id = $request->anak;
         $maperas->banjar_adat_lama_id = $banjar_adat_lama->id;
         $maperas->desa_adat_lama_id = $desa_adat_lama->id;
         $maperas->keterangan = $request->keterangan;
         if($ayah_lama){
             $maperas->ayah_lama_id = $ayah_lama->id;
         }
         if($ibu_lama){
             $maperas->ibu_lama_id = $ibu_lama->id;
         }
         $maperas->tanggal_maperas = date("Y-m-d", strtotime($request->tanggal_maperas));
         $maperas->status_maperas = '0';
         if($request->file('file_bukti_maperas')!=""){
            $file = $request->file('file_bukti_maperas');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_bukti_maperas';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($maperas->file_bukti_maperas != NULL){
                $old_path = str_replace("/storage","",$maperas->file_bukti_maperas);
                Storage::disk('public')->delete($old_path);
            }
            $maperas->file_bukti_maperas = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
        if($request->file('file_akta_pengangkatan_anak')!=""){
            $file = $request->file('file_akta_pengangkatan_anak');
            $fileLocation = '/file/'.$desa_adat_baru->id.'/maperas/'.$convert_nomor_maperas.'/file_akta_pengangkatan_anak';
            $filename = $file->getClientOriginalName();
            $path = $fileLocation."/".$filename;
            if($maperas->file_akta_pengangkatan_anak != NULL){
                $old_path = str_replace("/storage","",$maperas->file_akta_pengangkatan_anak);
                Storage::disk('public')->delete($old_path);
            }
            $maperas->file_akta_pengangkatan_anak = '/storage'.$path;
            Storage::disk('public')->put($path, file_get_contents($file));
        }
 
         //DATA ORANG TUA BARU
         $maperas->nik_ayah_baru = $request->nik_ayah;
         $maperas->nik_ibu_baru = $request->nik_ibu;
         $maperas->nama_ayah_baru = $request->nama_ayah;
         $maperas->nama_ibu_baru = $request->nama_ibu;
         $maperas->agama_baru = $request->agama;
         $maperas->alamat_asal = $request->alamat;
         $maperas->desa_asal_id = $request->desa_asal;

        if($status == '0'){
             //UPDATE MAPERAS
            $maperas->status_maperas = '0';
            $maperas->update();

            return redirect()->route('banjar-maperas-home')->with('success', 'Draft Maperas berhasil diperbaharui');
        }else{
            //UPDATE MAPERAS
            $maperas->status_maperas = '3';
            $maperas->update();

            //NONAKTIFKAN CACAH
            $cacah_krama_mipil_lama->status = '0';
            $cacah_krama_mipil_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_maperas));
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
                    $anggota_lama->tanggal_nonaktif = date("Y-m-d", strtotime($request->tanggal_maperas));
                    $anggota_lama->alasan_keluar = 'Maperas (Campuran Keluar)';
                }
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }

            //LEBUR KK LAMA
            $krama_mipil_lama->status = '0';
            $krama_mipil_lama->update();
            return redirect()->route('banjar-maperas-home')->with('success', 'Maperas berhasil diperbaharui');
        }
    }

    public function detail($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $maperas = Maperas::find($id);

        if($maperas->jenis_maperas == 'satu_banjar_adat'){
            //VALIDASI
            if(($maperas->banjar_adat_lama_id != $banjar_adat_id) && ($maperas->banjar_adat_baru_id != $banjar_adat_id)){
                return redirect()->back();
            }

            //DATA ANAK
            $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
            //SET NAMA LENGKAP ANAK
            $nama = '';
            if($anak->penduduk->gelar_depan != ''){
                $nama = $nama.$anak->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$anak->penduduk->nama;
            if($anak->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anak->penduduk->gelar_belakang;
            }
            $anak->penduduk->nama = $nama;

            //DATA ORANG TUA LAMA
            $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
            $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
            if($ayah_lama){
                //SET NAMA LENGKAP AYAH LAMA
                $nama = '';
                if($ayah_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ayah_lama->penduduk->nama;
                if($ayah_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
                }
                $ayah_lama->penduduk->nama = $nama;
            }
            if($ibu_lama){
                //SET NAMA LENGKAP IBU LAMA
                $nama = '';
                if($ibu_lama->penduduk->gelar_depan != ''){
                    $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ibu_lama->penduduk->nama;
                if($ibu_lama->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
                }
                $ibu_lama->penduduk->nama = $nama;
            }

            //DATA ORANG TUA BARU
            $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
            $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);
            //SET NAMA LENGKAP AYAH BARU
            $nama = '';
            if($ayah_baru->penduduk->gelar_depan != ''){
                $nama = $nama.$ayah_baru->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ayah_baru->penduduk->nama;
            if($ayah_baru->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ayah_baru->penduduk->gelar_belakang;
            }
            $ayah_baru->penduduk->nama = $nama;
            //SET NAMA LENGKAP IBU BARU
            $nama = '';
            if($ibu_baru->penduduk->gelar_depan != ''){
                $nama = $nama.$ibu_baru->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ibu_baru->penduduk->nama;
            if($ibu_baru->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ibu_baru->penduduk->gelar_belakang;
            }
            $ibu_baru->penduduk->nama = $nama;

            return view('pages.banjar.maperas.detail_satu_banjar_adat', compact('maperas', 'anak', 'ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru'));
        }
        else if($maperas->jenis_maperas == 'beda_banjar_adat'){
            if($maperas->banjar_adat_lama_id == $banjar_adat_id){
                //DATA ANAK
                $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
                //SET NAMA LENGKAP ANAK
                $nama = '';
                if($anak->penduduk->gelar_depan != ''){
                    $nama = $nama.$anak->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anak->penduduk->nama;
                if($anak->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anak->penduduk->gelar_belakang;
                }
                $anak->penduduk->nama = $nama;

                //DATA ORANG TUA LAMA
                $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
                $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
                if($ayah_lama){
                    //SET NAMA LENGKAP AYAH LAMA
                    $nama = '';
                    if($ayah_lama->penduduk->gelar_depan != ''){
                        $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
                    }
                    $nama = $nama.$ayah_lama->penduduk->nama;
                    if($ayah_lama->penduduk->gelar_belakang != ''){
                        $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
                    }
                    $ayah_lama->penduduk->nama = $nama;
                }
                if($ibu_lama){
                    //SET NAMA LENGKAP IBU LAMA
                    $nama = '';
                    if($ibu_lama->penduduk->gelar_depan != ''){
                        $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
                    }
                    $nama = $nama.$ibu_lama->penduduk->nama;
                    if($ibu_lama->penduduk->gelar_belakang != ''){
                        $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
                    }
                    $ibu_lama->penduduk->nama = $nama;
                }

                //DATA ORANG TUA BARU
                $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
                $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);
                //SET NAMA LENGKAP AYAH BARU
                $nama = '';
                if($ayah_baru->penduduk->gelar_depan != ''){
                    $nama = $nama.$ayah_baru->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ayah_baru->penduduk->nama;
                if($ayah_baru->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ayah_baru->penduduk->gelar_belakang;
                }
                $ayah_baru->penduduk->nama = $nama;
                //SET NAMA LENGKAP IBU BARU
                $nama = '';
                if($ibu_baru->penduduk->gelar_depan != ''){
                    $nama = $nama.$ibu_baru->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ibu_baru->penduduk->nama;
                if($ibu_baru->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ibu_baru->penduduk->gelar_belakang;
                }
                $ibu_baru->penduduk->nama = $nama;

                return view('pages.banjar.maperas.detail_keluar_banjar_adat', compact('maperas', 'anak', 'ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru'));
            }else{
                //DATA ANAK
                $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
                //SET NAMA LENGKAP ANAK
                $nama = '';
                if($anak->penduduk->gelar_depan != ''){
                    $nama = $nama.$anak->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$anak->penduduk->nama;
                if($anak->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anak->penduduk->gelar_belakang;
                }
                $anak->penduduk->nama = $nama;

                //DATA ORANG TUA LAMA
                $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
                $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
                if($ayah_lama){
                    //SET NAMA LENGKAP AYAH LAMA
                    $nama = '';
                    if($ayah_lama->penduduk->gelar_depan != ''){
                        $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
                    }
                    $nama = $nama.$ayah_lama->penduduk->nama;
                    if($ayah_lama->penduduk->gelar_belakang != ''){
                        $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
                    }
                    $ayah_lama->penduduk->nama = $nama;
                }
                if($ibu_lama){
                    //SET NAMA LENGKAP IBU LAMA
                    $nama = '';
                    if($ibu_lama->penduduk->gelar_depan != ''){
                        $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
                    }
                    $nama = $nama.$ibu_lama->penduduk->nama;
                    if($ibu_lama->penduduk->gelar_belakang != ''){
                        $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
                    }
                    $ibu_lama->penduduk->nama = $nama;
                }

                //DATA ORANG TUA BARU
                $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
                $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);
                //SET NAMA LENGKAP AYAH BARU
                $nama = '';
                if($ayah_baru->penduduk->gelar_depan != ''){
                    $nama = $nama.$ayah_baru->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ayah_baru->penduduk->nama;
                if($ayah_baru->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ayah_baru->penduduk->gelar_belakang;
                }
                $ayah_baru->penduduk->nama = $nama;
                //SET NAMA LENGKAP IBU BARU
                $nama = '';
                if($ibu_baru->penduduk->gelar_depan != ''){
                    $nama = $nama.$ibu_baru->penduduk->gelar_depan.' ';
                }
                $nama = $nama.$ibu_baru->penduduk->nama;
                if($ibu_baru->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$ibu_baru->penduduk->gelar_belakang;
                }
                $ibu_baru->penduduk->nama = $nama;

                return view('pages.banjar.maperas.detail_masuk_banjar_adat', compact('maperas', 'anak', 'ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru'));
            }
        }
        else if($maperas->jenis_maperas == 'campuran_masuk'){
            if($maperas->banjar_adat_baru_id != $banjar_adat_id){
                return redirect()->back();
            }

            //DATA ANAK
            $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
            //SET NAMA LENGKAP ANAK
            $nama = '';
            if($anak->penduduk->gelar_depan != ''){
                $nama = $nama.$anak->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$anak->penduduk->nama;
            if($anak->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anak->penduduk->gelar_belakang;
            }
            $anak->penduduk->nama = $nama;

            //DATA ORANG TUA BARU
            $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
            $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);
            //SET NAMA LENGKAP AYAH BARU
            $nama = '';
            if($ayah_baru->penduduk->gelar_depan != ''){
                $nama = $nama.$ayah_baru->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ayah_baru->penduduk->nama;
            if($ayah_baru->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ayah_baru->penduduk->gelar_belakang;
            }
            $ayah_baru->penduduk->nama = $nama;
            //SET NAMA LENGKAP IBU BARU
            $nama = '';
            if($ibu_baru->penduduk->gelar_depan != ''){
                $nama = $nama.$ibu_baru->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ibu_baru->penduduk->nama;
            if($ibu_baru->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ibu_baru->penduduk->gelar_belakang;
            }
            $ibu_baru->penduduk->nama = $nama;

            $desa_asal = DesaDinas::find($maperas->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

            return view('pages.banjar.maperas.detail_campuran_masuk',compact(
                'maperas', 'anak', 'ayah_baru', 'ibu_baru', 'desa_asal', 
                'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
        }
        else if($maperas->jenis_maperas == 'campuran_keluar'){
            if($maperas->banjar_adat_lama_id != $banjar_adat_id){
                return redirect()->back();
            }

            //DATA ANAK
            $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_lama_id);
            //SET NAMA LENGKAP ANAK
            $nama = '';
            if($anak->penduduk->gelar_depan != ''){
                $nama = $nama.$anak->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$anak->penduduk->nama;
            if($anak->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anak->penduduk->gelar_belakang;
            }
            $anak->penduduk->nama = $nama;

            //DATA ORANG TUA lama
            $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
            $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
            //SET NAMA LENGKAP AYAH lama
            $nama = '';
            if($ayah_lama->penduduk->gelar_depan != ''){
                $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ayah_lama->penduduk->nama;
            if($ayah_lama->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
            }
            $ayah_lama->penduduk->nama = $nama;
            //SET NAMA LENGKAP IBU lama
            $nama = '';
            if($ibu_lama->penduduk->gelar_depan != ''){
                $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ibu_lama->penduduk->nama;
            if($ibu_lama->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
            }
            $ibu_lama->penduduk->nama = $nama;

            $desa_asal = DesaDinas::find($maperas->desa_asal_id);
            $kecamatan_asal = Kecamatan::find($desa_asal->kecamatan_id);
            $kabupaten_asal = Kabupaten::find($kecamatan_asal->kabupaten_id);
            $provinsi_asal = Provinsi::find($kabupaten_asal->provinsi_id);

            return view('pages.banjar.maperas.detail_campuran_keluar',compact(
                'maperas', 'anak', 'ayah_lama', 'ibu_lama', 'desa_asal', 
                'kecamatan_asal', 'kabupaten_asal', 'provinsi_asal'));
        }
    }

    public function destroy($id){
        $maperas = Maperas::find($id);
        if($maperas->status_maperas == '0' || $maperas->status_maperas == '2'){
            if($maperas->jenis_maperas == 'campuran_masuk'){
                $cacah_baru = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
                $penduduk_baru = Penduduk::find($cacah_baru->penduduk_id);
    
                //DELETE SEMUA
                $cacah_baru->delete();
                $penduduk_baru->delete();
            }
            $maperas->delete();
            return redirect()->back()->with('success', 'Draft Maperas berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Maperas yang telah sah tidak dapat dihapus');
        }
    }

    //MAPERAS BEDA BANJAR ADAT (KELUAR)
    public function detail_keluar_banjar($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $maperas = Maperas::find($id);
        //VALIDASI
        if(($maperas->banjar_adat_lama_id != $banjar_adat_id) && ($maperas->banjar_adat_baru_id == $banjar_adat_id)){
            return redirect()->back();
        }
        if($maperas->status_maperas != '0'){
            return redirect()->back();
        }
        if($maperas->jenis_maperas != 'beda_banjar_adat'){
            return redirect()->back();
        }

        //DATA ANAK
        $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
        //SET NAMA LENGKAP ANAK
        $nama = '';
        if($anak->penduduk->gelar_depan != ''){
            $nama = $nama.$anak->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$anak->penduduk->nama;
        if($anak->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$anak->penduduk->gelar_belakang;
        }
        $anak->penduduk->nama = $nama;

        //DATA ORANG TUA LAMA
        $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
        $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
        if($ayah_lama){
            //SET NAMA LENGKAP AYAH LAMA
            $nama = '';
            if($ayah_lama->penduduk->gelar_depan != ''){
                $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ayah_lama->penduduk->nama;
            if($ayah_lama->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
            }
            $ayah_lama->penduduk->nama = $nama;
        }
        if($ibu_lama){
            //SET NAMA LENGKAP IBU LAMA
            $nama = '';
            if($ibu_lama->penduduk->gelar_depan != ''){
                $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ibu_lama->penduduk->nama;
            if($ibu_lama->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
            }
            $ibu_lama->penduduk->nama = $nama;
        }

        //DATA ORANG TUA BARU
        $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);
        //SET NAMA LENGKAP AYAH BARU
        $nama = '';
        if($ayah_baru->penduduk->gelar_depan != ''){
            $nama = $nama.$ayah_baru->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$ayah_baru->penduduk->nama;
        if($ayah_baru->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$ayah_baru->penduduk->gelar_belakang;
        }
        $ayah_baru->penduduk->nama = $nama;
        //SET NAMA LENGKAP IBU BARU
        $nama = '';
        if($ibu_baru->penduduk->gelar_depan != ''){
            $nama = $nama.$ibu_baru->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$ibu_baru->penduduk->nama;
        if($ibu_baru->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$ibu_baru->penduduk->gelar_belakang;
        }
        $ibu_baru->penduduk->nama = $nama;

        return view('pages.banjar.maperas.detail_keluar_banjar_adat', compact('maperas', 'anak', 'ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru'));
    }

    public function tolak_keluar_banjar($id, Request $request){
        $validator = Validator::make($request->all(), [
            'alasan_penolakan' => 'required',
        ],[
            'alasan_penolakan.required' => "Alasan wajib diisi",
        ]);
    
        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $maperas = Maperas::find($id);
        $maperas->alasan_penolakan = $request->alasan_penolakan;
        $maperas->status_maperas = '2';
        $maperas->update();

        /**
         * create notif baru
         */
        
        $notifikasi = new Notifikasi();
        $notifikasi->notif_tolak_maperas_beda_banjar_adat($maperas->id);

        return redirect()->route('banjar-maperas-home')->with('success', 'Maperas keluar berhasil diperbaharui');   
    }
    
    public function konfirmasi_keluar_banjar($id){
        //UPDATE MAPERAS
        $maperas = Maperas::find($id);
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
    
        return redirect()->route('banjar-maperas-home')->with('success', 'Maperas keluar berhasil dikonfirmasi');   
    }
    //MAPERAS BEDA BANJAR ADAT (KELUAR)

    //MAPERAS BEDA BANJAR ADAT (MASUK)
    public function detail_masuk_banjar($id){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $maperas = Maperas::find($id);
        //VALIDASI
        if(($maperas->banjar_adat_lama_id == $banjar_adat_id) && ($maperas->banjar_adat_baru_id != $banjar_adat_id)){
            return redirect()->back();
        }
        if($maperas->status_maperas != '1'){
            return redirect()->back();
        }
        if($maperas->jenis_maperas != 'beda_banjar_adat'){
            return redirect()->back();
        }

        //DATA ANAK
        $anak = CacahKramaMipil::find($maperas->cacah_krama_mipil_baru_id);
        //SET NAMA LENGKAP ANAK
        $nama = '';
        if($anak->penduduk->gelar_depan != ''){
            $nama = $nama.$anak->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$anak->penduduk->nama;
        if($anak->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$anak->penduduk->gelar_belakang;
        }
        $anak->penduduk->nama = $nama;

        //DATA ORANG TUA LAMA
        $ayah_lama = CacahKramaMipil::find($maperas->ayah_lama_id);
        $ibu_lama = CacahKramaMipil::find($maperas->ibu_lama_id);
        if($ayah_lama){
            //SET NAMA LENGKAP AYAH LAMA
            $nama = '';
            if($ayah_lama->penduduk->gelar_depan != ''){
                $nama = $nama.$ayah_lama->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ayah_lama->penduduk->nama;
            if($ayah_lama->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ayah_lama->penduduk->gelar_belakang;
            }
            $ayah_lama->penduduk->nama = $nama;
        }
        if($ibu_lama){
            //SET NAMA LENGKAP IBU LAMA
            $nama = '';
            if($ibu_lama->penduduk->gelar_depan != ''){
                $nama = $nama.$ibu_lama->penduduk->gelar_depan.' ';
            }
            $nama = $nama.$ibu_lama->penduduk->nama;
            if($ibu_lama->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$ibu_lama->penduduk->gelar_belakang;
            }
            $ibu_lama->penduduk->nama = $nama;
        }

        //DATA ORANG TUA BARU
        $ayah_baru = CacahKramaMipil::find($maperas->ayah_baru_id);
        $ibu_baru = CacahKramaMipil::find($maperas->ibu_baru_id);
        //SET NAMA LENGKAP AYAH BARU
        $nama = '';
        if($ayah_baru->penduduk->gelar_depan != ''){
            $nama = $nama.$ayah_baru->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$ayah_baru->penduduk->nama;
        if($ayah_baru->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$ayah_baru->penduduk->gelar_belakang;
        }
        $ayah_baru->penduduk->nama = $nama;
        //SET NAMA LENGKAP IBU BARU
        $nama = '';
        if($ibu_baru->penduduk->gelar_depan != ''){
            $nama = $nama.$ibu_baru->penduduk->gelar_depan.' ';
        }
        $nama = $nama.$ibu_baru->penduduk->nama;
        if($ibu_baru->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$ibu_baru->penduduk->gelar_belakang;
        }
        $ibu_baru->penduduk->nama = $nama;

        return view('pages.banjar.maperas.detail_masuk_banjar_adat', compact('maperas', 'anak', 'ayah_lama', 'ibu_lama', 'ayah_baru', 'ibu_baru'));
    }

    public function konfirmasi_masuk_banjar($id){
        $maperas = Maperas::find($id);

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

        return redirect()->route('banjar-maperas-home')->with('success', 'Maperas berhasil disahkan');
        
    }
    //MAPERAS BEDA BANJAR ADAT (MASUK)
}