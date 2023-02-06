<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\BanjarAdat;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\CacahTamiu;
use App\Models\DesaAdat;
use App\Models\Kabupaten;
use App\Models\Kelahiran;
use App\Models\Kemutasian;
use App\Models\Kematian;
use App\Models\KramaMipil;
use App\Models\KramaTamiu;
use App\Models\Maperas;
use App\Models\Perceraian;
use App\Models\Perkawinan;
use App\Models\Tamiu;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PelaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $kabupaten = Kabupaten::where('provinsi_id', '51')->get();
        $data = [
            'kabupaten' => $kabupaten
        ];
        return view('pages.admin.pelaporan.index', compact('data'));
    }

    public function lapKrama(Request $request)
    {
        # Validasi
        if (!$request->kabupaten) {
            return redirect()->back()->with('failed', 'Kabupaten wajib dipilih');
        }

        if (!$request->kecamatan) {
            return redirect()->back()->with('failed', 'Kecamatan wajib dipilih');
        }

        if (!$request->desa_adat) {
            return redirect()->back()->with('failed', 'Desa Adat wajib dipilih');
        }

        # define variabel total
        $total_krama_mipil = 0;
        $total_krama_mipil_laki = 0;
        $total_krama_mipil_perempuan = 0;

        $total_krama_tamiu = 0;
        $total_krama_tamiu_laki = 0;
        $total_krama_tamiu_perempuan = 0;

        $total_tamiu = 0;
        $total_tamiu_laki = 0;
        $total_tamiu_perempuan = 0;

        # count
        foreach($request->desa_adat as $item)
        {
            $desa_adat = DesaAdat::find($item);

            # Get Arr Banjar Adat
            $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();

            # Get Krama Mipil
            $krama_mipil = KramaMipil::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->count();
            $total_krama_mipil = $total_krama_mipil + $krama_mipil;

            $krama_mipil_laki = KramaMipil::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_krama_mipil_laki = $total_krama_mipil_laki + $krama_mipil_laki;

            $krama_mipil_perempuan = KramaMipil::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_krama_mipil_perempuan = $total_krama_mipil_perempuan + $krama_mipil_perempuan;

            # Get Krama Tamiu
            $krama_tamiu = KramaTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->count();
            $total_krama_tamiu = $total_krama_tamiu + $krama_tamiu;

            $krama_tamiu_laki = KramaTamiu::with('cacah_krama_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_krama_tamiu_laki = $total_krama_tamiu_laki + $krama_tamiu_laki;

            $krama_tamiu_perempuan = KramaTamiu::with('cacah_krama_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_krama_tamiu_perempuan = $total_krama_tamiu_perempuan + $krama_tamiu_perempuan;

            # Get Tamiu
            $tamiu = Tamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->count();
            $total_tamiu = $total_tamiu + $tamiu;

            $tamiu_laki = Tamiu::with('cacah_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_tamiu_laki = $total_tamiu_laki + $tamiu_laki;

            $tamiu_perempuan = Tamiu::with('cacah_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_tamiu.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_tamiu_perempuan = $total_tamiu_perempuan + $tamiu_perempuan;

            # Assign jumlah ke Desa Adat
            $desa_adat->jumlah_krama_mipil = $krama_mipil;
            $desa_adat->jumlah_krama_mipil_laki = $krama_mipil_laki;
            $desa_adat->jumlah_krama_mipil_perempuan = $krama_mipil_perempuan;

            $desa_adat->jumlah_krama_tamiu = $krama_tamiu;
            $desa_adat->jumlah_krama_tamiu_laki = $krama_tamiu_laki;
            $desa_adat->jumlah_krama_tamiu_perempuan = $krama_tamiu_perempuan;

            $desa_adat->jumlah_tamiu = $tamiu;
            $desa_adat->jumlah_tamiu_laki = $tamiu_laki;
            $desa_adat->jumlah_tamiu_perempuan = $tamiu_perempuan;

            #Assign all data
            $data_desa_adat[] = $desa_adat;
        }

        $data = [
            'desa_adat' => $data_desa_adat,
            'total_krama_mipil' => $total_krama_mipil,
            'total_krama_tamiu' => $total_krama_tamiu,
            'total_tamiu' => $total_tamiu,
            'total_krama_mipil_laki' => $total_krama_mipil_laki,
            'total_krama_tamiu_laki' => $total_krama_tamiu_laki,
            'total_tamiu_laki' => $total_tamiu_laki,
            'total_krama_mipil_perempuan' => $total_krama_mipil_perempuan,
            'total_krama_tamiu_perempuan' => $total_krama_tamiu_perempuan,
            'total_tamiu_perempuan' => $total_tamiu_perempuan,
        ];

        return view('pages.admin.pelaporan.laporan-krama', compact('data'));
    }

    public function lapCacahKrama(Request $request)
    {
        # Validasi
        if (!$request->kabupaten_cacah) {
            return redirect()->back()->with(['failed' => 'Kabupaten wajib dipilih', 'tab' => 'cacah']);
        }

        if (!$request->kecamatan_cacah) {
            return redirect()->back()->with(['failed' => 'Kecamatan wajib dipilih', 'tab' => 'cacah']);
        }

        if (!$request->desa_adat_cacah) {
            return redirect()->back()->with(['failed' => 'Desa Adat wajib dipilih', 'tab' => 'cacah']);
        }

        # define variabel total
        $total_cacah_krama_mipil = 0;
        $total_cacah_krama_mipil_laki = 0;
        $total_cacah_krama_mipil_perempuan = 0;

        $total_cacah_krama_tamiu = 0;
        $total_cacah_krama_tamiu_laki = 0;
        $total_cacah_krama_tamiu_perempuan = 0;

        $total_cacah_tamiu = 0;
        $total_cacah_tamiu_laki = 0;
        $total_cacah_tamiu_perempuan = 0;

        # count
        foreach($request->desa_adat_cacah as $item)
        {
            $desa_adat = DesaAdat::find($item);

            # Get Arr Banjar Adat
            $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();
            $arr_kk_krama_mipil = KramaMipil::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->pluck('cacah_krama_mipil_id');
            $arr_kk_krama_tamiu = KramaTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->pluck('cacah_krama_tamiu_id');
            $arr_kk_tamiu = Tamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->pluck('cacah_tamiu_id');

            # Get CacahKrama Mipil
            $cacah_krama_mipil = CacahKramaMipil::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_mipil)->count();
            $total_cacah_krama_mipil = $total_cacah_krama_mipil + $cacah_krama_mipil;

            $cacah_krama_mipil_laki = CacahKramaMipil::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_mipil)
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_cacah_krama_mipil_laki = $total_cacah_krama_mipil_laki + $cacah_krama_mipil_laki;

            $cacah_krama_mipil_perempuan = CacahKramaMipil::with('penduduk')->whereNotIn('id', $arr_kk_krama_mipil)
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_cacah_krama_mipil_perempuan = $total_cacah_krama_mipil_perempuan + $cacah_krama_mipil_perempuan;
             
            # Get CacahKrama Tamiu
            $cacah_krama_tamiu = CacahKramaTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_tamiu)->count();
            $total_cacah_krama_tamiu = $total_cacah_krama_tamiu + $cacah_krama_tamiu;

            $cacah_krama_tamiu_laki = CacahKramaTamiu::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_tamiu)
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            })->count();
            $total_cacah_krama_tamiu_laki = $total_cacah_krama_tamiu_laki + $cacah_krama_tamiu_laki;

            $cacah_krama_tamiu_perempuan = CacahKramaTamiu::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_krama_tamiu)
            ->whereHas('penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_cacah_krama_tamiu_perempuan = $total_cacah_krama_tamiu_perempuan + $cacah_krama_tamiu_perempuan;

            # Get Tamiu
            $cacah_tamiu = CacahTamiu::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu)->count();
            $total_cacah_tamiu = $total_cacah_tamiu + $cacah_tamiu;


            $cacah_tamiu_laki = CacahTamiu::with('penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu)
            ->whereHas('penduduk', function ($query) use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'laki-laki');
            })
            ->orWhereHas('wna', function ($query)use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'laki-laki');
            })
            ->count();
            $total_cacah_tamiu_laki = $total_cacah_tamiu_laki + $cacah_tamiu_laki;

            $cacah_tamiu_perempuan = CacahTamiu::with('cacah_tamiu.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu)
            ->whereHas('penduduk', function ($query) use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'perempuan');
            })
            ->orWhereHas('wna', function ($query) use ($arr_banjar_adat_id, $arr_kk_tamiu){
                $query->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')->whereNotIn('id', $arr_kk_tamiu);
                return $query->where('jenis_kelamin', 'perempuan');
            })->count();
            $total_cacah_tamiu_perempuan = $total_cacah_tamiu_perempuan + $cacah_tamiu_perempuan;

            # Assign jumlah ke Desa Adat
            $desa_adat->jumlah_cacah_krama_mipil = $cacah_krama_mipil;
            $desa_adat->jumlah_cacah_krama_mipil_laki = $cacah_krama_mipil_laki;
            $desa_adat->jumlah_cacah_krama_mipil_perempuan = $cacah_krama_mipil_perempuan;

            $desa_adat->jumlah_cacah_krama_tamiu = $cacah_krama_tamiu;
            $desa_adat->jumlah_cacah_krama_tamiu_laki = $cacah_krama_tamiu_laki;
            $desa_adat->jumlah_cacah_krama_tamiu_perempuan = $cacah_krama_tamiu_perempuan;

            $desa_adat->jumlah_cacah_tamiu = $cacah_tamiu;
            $desa_adat->jumlah_cacah_tamiu_laki = $cacah_tamiu_laki;
            $desa_adat->jumlah_cacah_tamiu_perempuan = $cacah_tamiu_perempuan;

            #Assign all data
            $data_desa_adat[] = $desa_adat;
        }

        $data = [
            'desa_adat' => $data_desa_adat,
            'total_cacah_krama_mipil' => $total_cacah_krama_mipil,
            'total_cacah_krama_tamiu' => $total_cacah_krama_tamiu,
            'total_cacah_tamiu' => $total_cacah_tamiu,
            'total_cacah_krama_mipil_laki' => $total_cacah_krama_mipil_laki,
            'total_cacah_krama_tamiu_laki' => $total_cacah_krama_tamiu_laki,
            'total_cacah_tamiu_laki' => $total_cacah_tamiu_laki,
            'total_cacah_krama_mipil_perempuan' => $total_cacah_krama_mipil_perempuan,
            'total_cacah_krama_tamiu_perempuan' => $total_cacah_krama_tamiu_perempuan,
            'total_cacah_tamiu_perempuan' => $total_cacah_tamiu_perempuan,
        ];

        return view('pages.admin.pelaporan.laporan-cacah-krama', compact('data'));
    }

    public function lapMutasi(Request $request)
    {
        # Validasi
        if (!$request->kabupaten_mutasi) {
            return redirect()->back()->with(['failed' => 'Kabupaten wajib dipilih', 'tab' => 'mutasi']);
        }

        if (!$request->kecamatan_mutasi) {
            return redirect()->back()->with(['failed' => 'Kecamatan wajib dipilih', 'tab' => 'mutasi']);
        }

        if (!$request->desa_adat_mutasi) {
            return redirect()->back()->with(['failed' => 'Desa Adat wajib dipilih', 'tab' => 'mutasi']);
        }

        $today = Carbon::now()->toDateString();
        if ($request->tgl_mutasi_awal != NULL && $request->tgl_mutasi_akhir != NULL) {
            $tgl_mutasi_awal = date("Y-m-d", strtotime($request->tgl_mutasi_awal));
            $tgl_mutasi_akhir = date("Y-m-d", strtotime($request->tgl_mutasi_akhir));

            if ($tgl_mutasi_awal > $today) {
                return redirect()->back()->with(['failed' => 'Rentang awal tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
            }

            if ($tgl_mutasi_akhir > $today) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
            }

            if ($tgl_mutasi_akhir < $tgl_mutasi_awal) {
                return redirect()->back()->with(['failed' => 'Rentang akhir tanggal mutasi tidak dapat lebih kecil dari rentang awal tanggal mutasi', 'tab' => 'mutasi']);
            }
        } else {
            if ($request->tgl_mutasi_awal) {
                $tgl_mutasi_awal = date("Y-m-d", strtotime($request->tgl_mutasi_awal));
                if ($tgl_mutasi_awal > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang awal tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
                }
            }
    
            if ($request->tgl_mutasi_akhir) {
                $tgl_mutasi_akhir = date("Y-m-d", strtotime($request->tgl_mutasi_akhir));
                if ($tgl_mutasi_akhir > $today) {
                    return redirect()->back()->with(['failed' => 'Rentang akhir tanggal mutasi tidak dapat melebihi tanggal hari ini', 'tab' => 'mutasi']);
                }
            }
        }

        # define
        $total_kelahiran = 0;
        $total_kelahiran_laki = 0;
        $total_kelahiran_perempuan = 0;

        $total_kematian = 0;
        $total_kematian_laki = 0;
        $total_kematian_perempuan = 0;

        $total_perkawinan = 0;
        $total_perceraian = 0;
        $total_maperas = 0;

        # count
        foreach($request->desa_adat_mutasi as $item)
        {
            $desa_adat = DesaAdat::find($item);

            # Get Arr Banjar Adat
            $arr_banjar_adat_id = BanjarAdat::where('desa_adat_id', $desa_adat->id)->pluck('id')->toArray();

            # Get Kelahiran
            $kelahiran = Kelahiran::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1');
            if(isset($request->tgl_mutasi_awal)){
                $kelahiran->where('tanggal_lahir', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kelahiran->where('tanggal_lahir', '<=', $tgl_mutasi_akhir);
            }
            $kelahiran = $kelahiran->count();            
            $total_kelahiran = $total_kelahiran + $kelahiran;

            $kelahiran_laki = Kelahiran::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kelahiran_laki->where('tanggal_lahir', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kelahiran_laki->where('tanggal_lahir', '<=', $tgl_mutasi_akhir);
            }
            $kelahiran_laki = $kelahiran_laki->count();          
            $total_kelahiran_laki = $total_kelahiran_laki + $kelahiran_laki;

            $kelahiran_perempuan = Kelahiran::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kelahiran_perempuan->where('tanggal_lahir', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kelahiran_perempuan->where('tanggal_lahir', '<=', $tgl_mutasi_akhir);
            }
            $kelahiran_perempuan = $kelahiran_perempuan->count();          
            $total_kelahiran_perempuan = $total_kelahiran_perempuan + $kelahiran_perempuan;

            # Get Kematian
            $kematian = Kematian::whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1');
            if(isset($request->tgl_mutasi_awal)){
                $kematian->where('tanggal_kematian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kematian->where('tanggal_kematian', '<=', $tgl_mutasi_akhir);
            }
            $kematian = $kematian->count();     
            $total_kematian = $total_kematian + $kematian;

            $kematian_laki = Kematian::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'laki-laki');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kematian_laki->where('tanggal_kematian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kematian_laki->where('tanggal_kematian', '<=', $tgl_mutasi_akhir);
            }
            $kematian_laki = $kematian_laki->count();     
            $total_kematian_laki = $total_kematian_laki + $kematian_laki;

            $kematian_perempuan = Kematian::with('cacah_krama_mipil.penduduk')
            ->whereIn('banjar_adat_id', $arr_banjar_adat_id)->where('status', '1')
            ->whereHas('cacah_krama_mipil.penduduk', function ($query) {
                return $query->where('jenis_kelamin', 'perempuan');
            });
            if(isset($request->tgl_mutasi_awal)){
                $kematian_perempuan->where('tanggal_kematian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $kematian_perempuan->where('tanggal_kematian', '<=', $tgl_mutasi_akhir);
            }
            $kematian_perempuan = $kematian_perempuan->count(); 
            $total_kematian_perempuan = $total_kematian_perempuan + $kematian_perempuan;

            # Get Perkawinan
            $perkawinan = Perkawinan::where(function ($query) use ($arr_banjar_adat_id) {
                $query->whereIn('banjar_adat_purusa_id', $arr_banjar_adat_id)
                    ->orWhereIn('banjar_adat_pradana_id', $arr_banjar_adat_id);
            })->where('status_perkawinan', '3');
            if(isset($request->tgl_mutasi_awal)){
                $perkawinan->where('tanggal_perkawinan', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $perkawinan->where('tanggal_perkawinan', '<=', $tgl_mutasi_akhir);
            }
            $perkawinan = $perkawinan->count();
            $total_perkawinan = $total_perkawinan + $perkawinan;

            # Get Perceraian
            $perceraian = Perceraian::where(function ($query) use ($arr_banjar_adat_id) {
                $query->whereIn('banjar_adat_purusa_id', $arr_banjar_adat_id)
                    ->orWhereIn('banjar_adat_pradana_id', $arr_banjar_adat_id);
            })->where('status_perceraian', '3');
            if(isset($request->tgl_mutasi_awal)){
                $perceraian->where('tanggal_perceraian', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $perceraian->where('tanggal_perceraian', '<=', $tgl_mutasi_akhir);
            }
            $perceraian = $perceraian->count();
            $total_perceraian = $total_perceraian + $perceraian;

            # Get Maperas
            $maperas = Maperas::where(function ($query) use ($arr_banjar_adat_id) {
                $query->whereIn('banjar_adat_lama_id', $arr_banjar_adat_id)
                    ->orWhereIn('banjar_adat_baru_id', $arr_banjar_adat_id);
            })->where('status_maperas', '3');
            if(isset($request->tgl_mutasi_awal)){
                $maperas->where('tanggal_maperas', '>=', $tgl_mutasi_awal);
            }
            if(isset($request->tgl_mutasi_akhir)){
                $maperas->where('tanggal_maperas', '<=', $tgl_mutasi_akhir);
            }
            $maperas = $maperas->count();
            $total_maperas = $total_maperas + $maperas;

            # Assign jumlah ke Desa Adat
            $desa_adat->jumlah_kelahiran = $kelahiran;
            $desa_adat->jumlah_kelahiran_laki = $kelahiran_laki;
            $desa_adat->jumlah_kelahiran_perempuan = $kelahiran_perempuan;

            $desa_adat->jumlah_kematian = $kematian;
            $desa_adat->jumlah_kematian_laki = $kematian_laki;
            $desa_adat->jumlah_kematian_perempuan = $kematian_perempuan;

            $desa_adat->jumlah_perkawinan = $perkawinan;
            $desa_adat->jumlah_perceraian = $perceraian;
            $desa_adat->jumlah_maperas = $maperas;

            #Assign all data
            $data_desa_adat[] = $desa_adat;
        }

        $data = [
            'desa_adat' => $data_desa_adat,
            'total_kelahiran' => $total_kelahiran,
            'total_kelahiran_laki' => $total_kelahiran_laki,
            'total_kelahiran_perempuan' => $total_kelahiran_perempuan,
            'total_kematian' => $total_kematian,
            'total_kematian_laki' => $total_kematian_laki,
            'total_kematian_perempuan' => $total_kematian_perempuan,
            'total_perkawinan' => $total_perkawinan,
            'total_perceraian' => $total_perceraian,
            'total_maperas' => $total_maperas,
        ];

        return view('pages.admin.pelaporan.laporan-mutasi', compact('data'));
    }
}