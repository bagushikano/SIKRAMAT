<?php

namespace App\Http\Controllers\BanjarAdatController;

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
use App\Models\KeluargaKrama;
use App\Models\Kematian;
use App\Models\KramaTamiu;
use App\Models\Tamiu;
use App\Models\Tempekan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class KelahiranController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function datatable(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kelahiran = Kelahiran::with('cacah_krama_mipil.penduduk', 'krama_mipil.cacah_krama_mipil', 'cacah_krama_mipil.tempekan')->where('banjar_adat_id', $banjar_adat_id);
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
            }
        }else{
            $kelahiran->where(function ($query) {
                $query->where('status', '0')
                      ->orWhere('status', '1');
            });
        }

        $kelahiran->orderBy('tanggal_lahir', 'DESC');
        
        return DataTables::of($kelahiran)
            ->addIndexColumn()
            ->addColumn('status', function ($data) {
                $return = '';
                if($data->status == '0'){
                    $return .= '<span class="badge badge-warning px-3 py-1"> Draft </span>';
                }else if($data->status == '1'){
                    $return .= '<span class="badge badge-success px-3 py-1"> Sah </span>';
                }
                return $return;
            })
            ->addColumn('link', function ($data) {
                $return = '';
                if($data->status == '0'){
                    $return .= '<a class="btn btn-warning btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-kelahiran-edit', $data->id).'"><i class="fas fa-edit"></i></a>';
                    $return .= '<button button type="button" class="btn btn-danger btn-sm my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Hapus" onclick="delete_kelahiran('.$data->id.')"><i class="fas fa-trash"></i></button>';
                }else if($data->status == '1'){
                    $return .= '<a class="btn btn-primary btn-sm mr-1 my-1"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Detail" href="'.route('banjar-kelahiran-detail', $data->id).'"><i class="fas fa-eye"></i></a>';
                }
                return $return;
            })
            ->rawColumns(['status', 'link'])
            ->make(true);
    }

    public function datatable_krama_mipil(Request $request){
        $banjar_adat_id = session()->get('banjar_adat_id');
        $kramas = KramaMipil::with('cacah_krama_mipil.penduduk', 'cacah_krama_mipil.tempekan', 'banjar_adat')->where('banjar_adat_id', $banjar_adat_id)->where('status', '1')->orderBy('tanggal_registrasi', 'DESC')->get()->map(function ($item){
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
    
    public function index(){
        return view('pages.banjar.kelahiran.kelahiran');
    }

    public function get_anggota_keluarga($id){
        $krama_mipil = KramaMipil::with('cacah_krama_mipil.penduduk')->find($id);
        $anggota_krama_mipil = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->where('krama_mipil_id', $krama_mipil->id)->get();

        //SET NAMA LENGKAP KRAMA MIPIL
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        //GET ANGGOTA MENINGGAL
        $krama_mipil_lengkap_id =  KramaMipil::where('nomor_krama_mipil', $krama_mipil->nomor_krama_mipil)->where('tanggal_nonaktif' , NULL)->pluck('id')->toArray();
        $anggota_krama_mipil_lengkap = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->whereIn('krama_mipil_id', $krama_mipil_lengkap_id)->groupBy('cacah_krama_mipil_id')->get();

        $anggota_krama_mipil_meninggal = collect();

        foreach($anggota_krama_mipil_lengkap as $anggota){
            if($anggota->cacah_krama_mipil->penduduk->tanggal_kematian != NULL){
                $nama = '(Alm)';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan;
                }
                $nama = $nama.' '.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;
                $anggota_krama_mipil_meninggal->push($anggota);
            }
        }


        //SET NAMA LENGKAP ANGGOTA
        foreach($anggota_krama_mipil as $anggota){
            $nama = '';
            if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$anggota->cacah_krama_mipil->penduduk->nama;
            if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $anggota->cacah_krama_mipil->penduduk->nama = $nama;
        }
        return response()->json([
            'krama_mipil' => $krama_mipil,
            'anggota_krama_mipil'=> $anggota_krama_mipil,
            'anggota_krama_mipil_meninggal' => $anggota_krama_mipil_meninggal,
            'nomor_krama_mipil' => $krama_mipil->nomor_krama_mipil
        ]);
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
        return view('pages.banjar.kelahiran.create', compact('pekerjaans', 'pendidikans', 'provinsis', 'tempekan', 'banjar_dinas'));
    }

    public function store($status, Request $request)
    {
        //GET MASTER
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);

        $validator = Validator::make($request->all(), [
            'nik' => 'unique:tb_penduduk|regex:/^[0-9]*$/|nullable',
            'nomor_akta_kelahiran' => 'unique:tb_kelahiran|nullable',
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'golongan_darah' => 'required',
            'krama_mipil' => 'required',
            'ayah_kandung' => 'required',
            'ibu_kandung' => 'required'
        ],[
            'nik.regex' => "NIK hanya boleh mengandung angka",
            'nik.unique' => "NIK yang dimasukkan telah terdaftar",
            'nomor_akta_kelahiran.unique' => "No. Akta Kelahiran yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'golongan_darah.required' => "Golongan Darah wajib dipilih",
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'ayah_kandung.required' => "Ayah wajib dipilih",
            'ibu_kandung.required' => "Ibu Wajib Dipilih"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $penduduk_ayah = Penduduk::find($request->ayah_kandung);
        $cacah_ayah = CacahKramaMipil::where('penduduk_id', $penduduk_ayah->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_id)->first();

        //INSERT PENDUDUK
        $penduduk = new Penduduk();
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $penduduk_ayah->desa_id;
        $penduduk->profesi_id = 1;
        $penduduk->pendidikan_id = 1;
        $penduduk->nama = $request->nama;
        $penduduk->agama = $request->agama;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->alamat = $penduduk_ayah->alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        $penduduk->status_perkawinan = 'belum_kawin';
        $penduduk->koordinat_alamat = $penduduk_ayah->koordinat_alamat;
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

        //GET STATUS KELAHIRAN
        if($status == '1'){
            //INSERT CACAH KRAMA MIPIL
            $cacah_krama_mipil = new CacahKramaMipil();
            $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
            $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
            $cacah_krama_mipil->tempekan_id = $cacah_ayah->tempekan_id;
            $cacah_krama_mipil->penduduk_id = $penduduk->id;
            $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_lahir));
            $cacah_krama_mipil->jenis_kependudukan = $cacah_ayah->jenis_kependudukan;
            $cacah_krama_mipil->status = '1';
            if($cacah_ayah->jenis_kependudukan == 'adat_&_dinas'){
                $cacah_krama_mipil->banjar_dinas_id = $cacah_ayah->banjar_dinas_id;
            }
            $cacah_krama_mipil->save();

            //UPDATE KELUARGA
            $krama_mipil_lama = KramaMipil::find($request->krama_mipil);
            $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

            //COPY DATA
            $krama_mipil_baru = new KramaMipil();
            $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
            $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
            $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
            $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
            $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
            $krama_mipil_baru->status = '1';
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
    
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }

            //INSERT ANGGOTA BARU (YANG BARU LAHIR)
            $anggota_krama_mipil = new AnggotaKramaMipil();
            $anggota_krama_mipil->krama_mipil_id = $krama_mipil_baru->id;
            $anggota_krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            $anggota_krama_mipil->status_hubungan = 'anak';
            $anggota_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_lahir));
            $anggota_krama_mipil->status = '1';
            $anggota_krama_mipil->save();

            //NONAKTIFKAN KK LAMA
            $krama_mipil_lama->status = '0';
            $krama_mipil_lama->update();

            //INSERT KELAHIRAN
            $kelahiran = new Kelahiran();
            $kelahiran->nomor_akta_kelahiran = $request->nomor_akta_kelahiran;
            $kelahiran->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            $kelahiran->banjar_adat_id = $banjar_adat_id;
            $kelahiran->krama_mipil_id = $krama_mipil_baru->id;
            $kelahiran->status = '1';
            $kelahiran->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $kelahiran->keterangan = $request->keterangan;
            $convert_nomor_akta_kelahiran = str_replace("/","-",$request->nomor_akta_kelahiran);
            if($request->file('file_akta_kelahiran')!=""){
                $file = $request->file('file_akta_kelahiran');
                $fileLocation = '/file/'.$desa_adat->id.'/kelahiran/'.$convert_nomor_akta_kelahiran.'/lampiran';
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kelahiran->file_akta_kelahiran = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kelahiran->save();
            return redirect()->route('banjar-kelahiran-home')->with('success', 'Kelahiran berhasil ditambahkan');
        }else{
            //INSERT CACAH KRAMA MIPIL
            $cacah_krama_mipil = new CacahKramaMipil();
            $cacah_krama_mipil->nomor_cacah_krama_mipil = $nomor_cacah_krama_mipil;
            $cacah_krama_mipil->banjar_adat_id = $banjar_adat_id;
            $cacah_krama_mipil->tempekan_id = $cacah_ayah->tempekan_id;
            $cacah_krama_mipil->penduduk_id = $penduduk->id;
            $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_lahir));
            $cacah_krama_mipil->jenis_kependudukan = $cacah_ayah->jenis_kependudukan;
            $cacah_krama_mipil->status = '0';
            if($cacah_ayah->jenis_kependudukan == 'adat_&_dinas'){
                $cacah_krama_mipil->banjar_dinas_id = $cacah_ayah->banjar_dinas_id;
            }
            $cacah_krama_mipil->save();

            //INSERT KELAHIRAN
            $kelahiran = new Kelahiran();
            $kelahiran->nomor_akta_kelahiran = $request->nomor_akta_kelahiran;
            $kelahiran->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            $kelahiran->banjar_adat_id = $banjar_adat_id;
            $kelahiran->krama_mipil_id = $request->krama_mipil;
            $kelahiran->status = '0';
            $kelahiran->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $convert_nomor_akta_kelahiran = str_replace("/","-",$request->nomor_akta_kelahiran);
            $kelahiran->keterangan = $request->keterangan;
            if($request->file('file_akta_kelahiran')!=""){
                $file = $request->file('file_akta_kelahiran');
                $fileLocation = '/file/'.$desa_adat->id.'/kelahiran/'.$convert_nomor_akta_kelahiran.'/lampiran';;
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kelahiran->file_akta_kelahiran = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kelahiran->save();
            return redirect()->route('banjar-kelahiran-home')->with('success', 'Kelahiran berhasil ditambahkan');
        }
    }

    public function edit($id){
        $kelahiran = Kelahiran::find($id);
        $banjar_adat_id = session()->get('banjar_adat_id');
        if($kelahiran->status == '1'){
            return redirect()->back()->with('error', 'Kelahiran yang telah sah tidak dapat diedit');
        }
        if($kelahiran->banjar_adat_id != $banjar_adat_id){
            return redirect()->back()->with('error', 'Akses Dilarang');
        }
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $krama_mipil = KramaMipil::find($kelahiran->krama_mipil_id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
        $anggota_krama_mipil = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil->id)->orderBy('status_hubungan', 'ASC')->where('status', '1')->get();
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat->id)->get();

        $desa = DesaDinas::find($penduduk->desa_id);
        $kecamatan = Kecamatan::find($desa->kecamatan_id);
        $kabupaten = Kabupaten::find($kecamatan->kabupaten_id);
        $provinsi = Provinsi::find($kabupaten->provinsi_id);

        $provinsis = Provinsi::get();
        $kabupatens = Kabupaten::where('provinsi_id', $provinsi->id)->get();
        $kecamatans = Kecamatan::where('kabupaten_id', $kabupaten->id)->get();
        $desas = DesaDinas::where('kecamatan_id', $kecamatan->id)->get();

        //SET NAMA LENGKAP KRAMA MIPIL
        $nama = '';
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_depan != ''){
            $nama = $nama.$krama_mipil->cacah_krama_mipil->penduduk->gelar_depan;
        }
        $nama = $nama.' '.$krama_mipil->cacah_krama_mipil->penduduk->nama;
        if($krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang != ''){
            $nama = $nama.', '.$krama_mipil->cacah_krama_mipil->penduduk->gelar_belakang;
        }
        $krama_mipil->cacah_krama_mipil->penduduk->nama = $nama;

        //SET NAMA LENGKAP ANGGOTA
        foreach($anggota_krama_mipil as $anggota){
            $nama = '';
            if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan;
            }
            $nama = $nama.' '.$anggota->cacah_krama_mipil->penduduk->nama;
            if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
            }
            $anggota->cacah_krama_mipil->penduduk->nama = $nama;
        }

        //GET ANGGOTA MENINGGAL
        $krama_mipil_lengkap_id =  KramaMipil::where('nomor_krama_mipil', $krama_mipil->nomor_krama_mipil)->where('tanggal_nonaktif' , NULL)->pluck('id')->toArray();
        $anggota_krama_mipil_lengkap = AnggotaKramaMipil::with('cacah_krama_mipil.penduduk')->whereIn('krama_mipil_id', $krama_mipil_lengkap_id)->groupBy('cacah_krama_mipil_id')->get();

        $anggota_krama_mipil_meninggal = collect();

        foreach($anggota_krama_mipil_lengkap as $anggota){
            if($anggota->cacah_krama_mipil->penduduk->tanggal_kematian != NULL){
                $nama = '(Alm)';
                if($anggota->cacah_krama_mipil->penduduk->gelar_depan != ''){
                    $nama = $nama.$anggota->cacah_krama_mipil->penduduk->gelar_depan;
                }
                $nama = $nama.' '.$anggota->cacah_krama_mipil->penduduk->nama;
                if($anggota->cacah_krama_mipil->penduduk->gelar_belakang != ''){
                    $nama = $nama.', '.$anggota->cacah_krama_mipil->penduduk->gelar_belakang;
                }
                $anggota->cacah_krama_mipil->penduduk->nama = $nama;
                $anggota_krama_mipil_meninggal->push($anggota);
            }
        }
        return view('pages.banjar.kelahiran.edit', compact('provinsis', 'kabupatens', 'kecamatans', 'desas', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'krama_mipil', 'cacah_krama_mipil', 'tempekan', 'banjar_dinas', 'kelahiran', 'penduduk', 'anggota_krama_mipil', 'anggota_krama_mipil_meninggal'));
    }

    public function update($id, $status, Request $request){
        //GET MASTER
        $banjar_adat_id = session()->get('banjar_adat_id');
        $banjar_adat = BanjarAdat::find($banjar_adat_id);
        $desa_adat = DesaAdat::find($banjar_adat->desa_adat_id);
        $jumlah_tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->count();

        //GET ALL DATA KELAHIRAN
        $kelahiran = Kelahiran::find($id);
        $krama_mipil = KramaMipil::find($kelahiran->krama_mipil_id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);

        $validator = Validator::make($request->all(), [
            'nik' => 'unique:tb_penduduk|regex:/^[0-9]*$/|nullable',
            'nik' => [
                Rule::unique('tb_penduduk')->ignore($penduduk->id),
            ],
            'nomor_akta_kelahiran' => 'unique:tb_kelahiran|nullable',
            'nomor_akta_kelahiran' => [
                Rule::unique('tb_kelahiran')->ignore($kelahiran->id),
            ],
            'nama' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tempat_lahir' => 'required|regex:/^[a-zA-Z\s]*$/',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'golongan_darah' => 'required',
            'krama_mipil' => 'required',
            'ayah_kandung' => 'required',
            'ibu_kandung' => 'required'
        ],[
            'nik.regex' => "NIK hanya boleh mengandung angka",
            'nik.unique' => "NIK yang dimasukkan telah terdaftar",
            'nomor_akta_kelahiran.unique' => "No. Akta Kelahiran yang dimasukkan telah terdaftar",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'tempat_lahir.required' => "Tempat Lahir wajib diisi",
            'tempat_lahir.regex' => "Tempat Lahir hanya boleh mengandung huruf",
            'tanggal_lahir.required' => "Tanggal Lahir wajib diisi",
            'jenis_kelamin.required' => "Jenis Kelamin wajib dipilih",
            'golongan_darah.required' => "Golongan Darah wajib dipilih",
            'krama_mipil.required' => "Krama Mipil wajib dipilih",
            'ayah_kandung.required' => "Ayah wajib dipilih",
            'ibu_kandung.required' => "Ibu Wajib Dipilih"
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $penduduk_ayah = Penduduk::find($request->ayah_kandung);
        $cacah_ayah = CacahKramaMipil::where('penduduk_id', $penduduk_ayah->id)->where('status', '1')->where('banjar_adat_id', $banjar_adat_id)->first();

        //UPDATE DATA PENDUDUK
        $penduduk->nik = $request->nik;
        $penduduk->desa_id = $penduduk_ayah->desa_id;
        $penduduk->profesi_id = 1;
        $penduduk->pendidikan_id = 1;
        $penduduk->nama = $request->nama;
        $penduduk->agama = $request->agama;
        $penduduk->tempat_lahir = $request->tempat_lahir;
        $penduduk->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
        $penduduk->jenis_kelamin = $request->jenis_kelamin;
        $penduduk->golongan_darah = $request->golongan_darah;
        $penduduk->alamat = $penduduk_ayah->alamat;
        $penduduk->ayah_kandung_id = $request->ayah_kandung;
        $penduduk->ibu_kandung_id = $request->ibu_kandung;
        $penduduk->status_perkawinan = 'belum_kawin';
        $penduduk->koordinat_alamat = $penduduk_ayah->koordinat_alamat;
        $penduduk->update();

        if($status == '1'){
            //UPDATE DATA CACAH
            $cacah_krama_mipil->tempekan_id = $cacah_ayah->tempekan_id;
            $cacah_krama_mipil->jenis_kependudukan = $cacah_ayah->jenis_kependudukan;
            $cacah_krama_mipil->status = '1';
            $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_lahir));
            if($cacah_ayah->jenis_kependudukan == 'adat_&_dinas'){
                $cacah_krama_mipil->banjar_dinas_id = $cacah_ayah->banjar_dinas_id;
            }
            $cacah_krama_mipil->update();

            //UPDATE KELUARGA
            $krama_mipil_lama = KramaMipil::find($request->krama_mipil);
            $anggota_krama_mipil_lama = AnggotaKramaMipil::where('krama_mipil_id', $krama_mipil_lama->id)->get();

            //COPY DATA
            $krama_mipil_baru = new KramaMipil();
            $krama_mipil_baru->nomor_krama_mipil = $krama_mipil_lama->nomor_krama_mipil;
            $krama_mipil_baru->banjar_adat_id = $krama_mipil_lama->banjar_adat_id;
            $krama_mipil_baru->cacah_krama_mipil_id = $krama_mipil_lama->cacah_krama_mipil_id;
            $krama_mipil_baru->kedudukan_krama_mipil = $krama_mipil_lama->kedudukan_krama_mipil;
            $krama_mipil_baru->jenis_krama_mipil = $krama_mipil_lama->jenis_krama_mipil;
            $krama_mipil_baru->status = '1';
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
    
                //NONAKTIFKAN ANGGOTA LAMA
                $anggota_lama->status = '0';
                $anggota_lama->update();
            }

            //INSERT ANGGOTA BARU (YANG BARU LAHIR)
            $anggota_krama_mipil = new AnggotaKramaMipil();
            $anggota_krama_mipil->krama_mipil_id = $krama_mipil_baru->id;
            $anggota_krama_mipil->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            $anggota_krama_mipil->status_hubungan = 'anak';
            $anggota_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_lahir));
            $anggota_krama_mipil->status = '1';
            $anggota_krama_mipil->save();

            //NONAKTIFKAN KK LAMA
            $krama_mipil_lama->status = '0';
            $krama_mipil_lama->update();

            //SAHKAN KELAHIRAN
            $kelahiran->nomor_akta_kelahiran = $request->nomor_akta_kelahiran;
            $kelahiran->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            $kelahiran->krama_mipil_id = $krama_mipil_baru->id;
            $kelahiran->status = '1';
            $kelahiran->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $convert_nomor_akta_kelahiran = str_replace("/","-",$request->nomor_akta_kelahiran);
            $kelahiran->keterangan = $request->keterangan;

            if($request->file('file_akta_kelahiran')!=""){
                $file = $request->file('file_akta_kelahiran');
                $fileLocation = '/file/'.$desa_adat->id.'/kelahiran/'.$convert_nomor_akta_kelahiran.'/lampiran';;
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kelahiran->file_akta_kelahiran = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kelahiran->update();
            return redirect()->route('banjar-kelahiran-home')->with('success', 'Kelahiran berhasil diperbaharui');
        }else if($status == '0'){
            //UPDATE DATA CACAH
            $cacah_krama_mipil->tempekan_id = $cacah_ayah->tempekan_id;
            $cacah_krama_mipil->penduduk_id = $penduduk->id;
            $cacah_krama_mipil->tanggal_registrasi = date("Y-m-d", strtotime($request->tanggal_lahir));
            $cacah_krama_mipil->jenis_kependudukan = $cacah_ayah->jenis_kependudukan;
            $cacah_krama_mipil->status = '0';
            if($cacah_ayah->jenis_kependudukan == 'adat_&_dinas'){
                $cacah_krama_mipil->banjar_dinas_id = $cacah_ayah->banjar_dinas_id;
            }
            $cacah_krama_mipil->update();

            //UPDATE DRAFT KELAHIRAN
            $kelahiran->nomor_akta_kelahiran = $request->nomor_akta_kelahiran;
            $kelahiran->cacah_krama_mipil_id = $cacah_krama_mipil->id;
            $kelahiran->krama_mipil_id = $request->krama_mipil;
            $kelahiran->status = '0';
            $kelahiran->tanggal_lahir = date("Y-m-d", strtotime($request->tanggal_lahir));
            $convert_nomor_akta_kelahiran = str_replace("/","-",$request->nomor_akta_kelahiran);
            $kelahiran->keterangan = $request->keterangan;
            if($request->file('file_akta_kelahiran')!=""){
                $file = $request->file('file_akta_kelahiran');
                $fileLocation = '/file/'.$desa_adat->id.'/kelahiran/'.$convert_nomor_akta_kelahiran.'/lampiran';;
                $filename = $file->getClientOriginalName();
                $path = $fileLocation."/".$filename;
                $kelahiran->file_akta_kelahiran = '/storage'.$path;
                Storage::disk('public')->put($path, file_get_contents($file));
            }
            $kelahiran->update();
            return redirect()->route('banjar-kelahiran-home')->with('success', 'Draft Kelahiran berhasil diperbaharui');
        }
    }

    public function detail($id){
        $kelahiran = Kelahiran::find($id);
        $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
        $penduduk = Penduduk::with('ayah', 'ibu')->find($cacah_krama_mipil->penduduk_id);

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
        return view('pages.banjar.kelahiran.detail', compact('kelahiran', 'cacah_krama_mipil', 'penduduk'));
    }

    public function destroy($id){
        $kelahiran = Kelahiran::find($id);
        if($kelahiran->status == '0'){
            $cacah_krama_mipil = CacahKramaMipil::find($kelahiran->cacah_krama_mipil_id);
            $penduduk = Penduduk::find($cacah_krama_mipil->penduduk_id);
            $kelahiran->delete();
            $cacah_krama_mipil->delete();
            $penduduk->delete();
            return redirect()->back()->with('success', 'Draft Kelahiran berhasil dihapus');
        }else{
            return redirect()->back()->with('error', 'Kelahiran yang telah sah tidak dapat dihapus');
        }
    }
}