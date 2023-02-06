<?php

namespace App\Http\Controllers\BanjarAdatController;

use App\Models\Penduduk;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\PendudukService;
use App\Services\PekerjaanService;
use App\Services\PendidikanService;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\CacahKramaMipil;
use App\Models\CacahKramaTamiu;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PharIo\Manifest\Email;
use Illuminate\Validation\Rule;


class ProfileKramaController extends Controller
{
    protected $servicePenduduk, $serviceUser, $servicePekerjaan, $servicePendidikan;
    
    public function __construct(PendudukService $servicePenduduk, UserService $serviceUser, PendidikanService $servicePendidikan, PekerjaanService $servicePekerjaan)
    {
        $this->servicePenduduk = $servicePenduduk;
        $this->serviceUser = $serviceUser;
        $this->servicePendidikan = $servicePendidikan;
        $this->servicePekerjaan = $servicePekerjaan;
    }

    public function profile()
    {
        $user_id = auth()->user()->id;
        $penduduk = $this->servicePenduduk->find($user_id);

        //Get Cacah Mipil Tamiu
        $cacahKramaMipil = CacahKramaMipil::where('penduduk_id', $penduduk->id)
                                            ->where('status', 1)
                                            ->whereNull("tanggal_nonaktif")
                                            ->with("banjar_adat.desa_adat", "tempekan")
                                            ->first();
        $cacahKramaTamiu = CacahKramaTamiu::where('penduduk_id', $penduduk->id)
                                            ->whereNull('tanggal_keluar')
                                            ->with("banjar_adat.desa_adat")
                                            ->first();
        
        $data = [
            'penduduk' => $penduduk,
            'user' => $this->serviceUser->find($user_id),
            'pendidikan' => $this->servicePendidikan->all(),
            'pekerjaan' => $this->servicePekerjaan->all(),
            'cacah_krama_mipil' => $cacahKramaMipil,
            'cacah_krama_tamiu' => $cacahKramaTamiu,
        ];

        return view('pages.banjar.profile.profile-krama', compact('data'));
    }

    public function changePassword(Request $request, $user_id)
    {
        $this->validate($request,[
            'password_lama' => "required|min:8",
            'password' => "required|confirmed|min:8",
        ],
        [
            'password_lama.required' => "Password lama wajib diisi",
            'password_lama.min' => "Password lama minimal berjumlah 8 karakter",
            'password.required' => "Password baru wajib diisi",
            'password.confirmed' => "Konfirmasi password baru tidak sesuai",
            'password.min' => "Password baru minimal berjumlah 8 karakter",
        ]);
        
        if (Hash::check($request->password_lama, $this->serviceUser->find($user_id)->password)) {
            $updatePassword = $this->serviceUser->changePassword($request->password, $user_id);
            if ($updatePassword['status'] == 'success') {
                return redirect()->back()->with('success', $updatePassword['message']);
            } else {
                return redirect()->back()->with('failed', 'Password anda gagal diubah');
            }
        } else {
            return redirect()->back()->with('failed', 'Password lama anda tidak sesuai');
        }
    }

    public function changeProfileImage(Request $request, $penduduk_id)
    {
        $this->validate($request,[
            'profile_img' => "required",
        ],
        [
            'profile_img.required' => "Password lama wajib diisi",
        ]);
        
        $updateProfileImg = $this->servicePenduduk->changeProfileImg($request->profile_img, $penduduk_id);
        
        if ($updateProfileImg['status'] == 'success') {
            return redirect()->back()->with('success', $updateProfileImg['message']);
        } else {
            return redirect()->back()->with('failed', 'Foto profile anda gagal diubah');
        }
    }

    public function showProfileImage($penduduk_id)
    {
        $penduduk = Penduduk::where('id', $penduduk_id)->first();
        if ($penduduk->foto != NULL) {
            // if(File::exists(public_path($penduduk->foto))) {
                return response()->file(
                    public_path($penduduk->foto)
                );
            // } else {
            //     return response()->file(
            //         public_path('assets/admin/assets/img/foto_placeholder.png')
            //     );
            // }
        } else {
            return response()->file(
                public_path('assets/admin/assets/img/foto_placeholder.png')
            );
        }
    }

    public function changeProfile(Request $request, $penduduk_id){
        $user = User::find(auth()->user()->id);
        $penduduk = Penduduk::find($penduduk_id);

        //Validator
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:tb_user',
            'email' => [
                Rule::unique('tb_user')->ignore($user->id),
            ],
            'telepon' => 'numeric|nullable',

            'gelar_depan' => 'regex:/^[a-zA-Z.\s]*$/|nullable',
            'nama' => 'required|regex:/^[a-zA-Z.\s]*$/',
            'gelar_belakang' => 'regex:/^[a-zA-Z.\s]*$/|nullable',
            'nama_alias' => 'regex:/^[a-zA-Z.\s]*$/|nullable',

            'pekerjaan' => 'required',
            'pendidikan' => 'required',
            'alamat' => 'required',
            
        ],[
            'email.required' => "Email wajib diisi",
            'email.unique' => "Email telah terdaftar",
            'telepon.numeric' => "Nomor telepon hanya boleh mengandung angka",

            'gelar_depan.regex' => "Gelar depan hanya boleh mengandung huruf",
            'nama.required' => "Nama wajib diisi",
            'nama.regex' => "Nama hanya boleh mengandung huruf",
            'gelar_belakang.regex' => "Gelar belakang hanya boleh mengandung huruf",
            'nama_alias.regex' => "Nama alias hanya boleh mengandung huruf",

            'pekerjaan.required' => "Pekerjaan wajib dipilih",
            'pendidikan.required' => "Pendidikan Terakhir wajib dipilih",
            'alamat.required' => "Alamat wajib diisi",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        //Update email
        $user->email = $request->email;
        $user->update();

        //Update profile
        $penduduk->telepon = $request->telepon;
        $penduduk->gelar_depan = $request->gelar_depan;
        $penduduk->nama = $request->nama;
        $penduduk->gelar_belakang = $request->gelar_belakang;
        $penduduk->nama_alias = $request->nama_alias;
        $penduduk->profesi_id = $request->pekerjaan;
        $penduduk->pendidikan_id = $request->pendidikan;
        $penduduk->alamat = $request->alamat;
        $penduduk->koordinat_alamat = $request->koordinat_alamat;
        $penduduk->update();

        return redirect()->back()->with('success', 'Profile berhasil diperbaharui');
    }
}
