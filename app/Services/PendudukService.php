<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\AkunKrama;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class PendudukService
{
    public function find($user_id)
    {
        $user_krama = AkunKrama::where('user_id', $user_id)->first();
        $penduduk = Penduduk::with('ayah', 'ibu')->where('id', $user_krama->penduduk_id)->first();

        return $penduduk;
    }

    public function changeProfileImg($raw_img, $penduduk_id)
    {
        $penduduk = Penduduk::find($penduduk_id);

        $image_parts = explode(';base64', $raw_img);
        $image_type_aux = explode('image/', $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $filename = uniqid().'.png';
        $fileLocation = '/image/penduduk/'.$penduduk->nik.'/foto';
        $path = $fileLocation."/".$filename;
        Storage::disk('public')->put($path, $image_base64);

        try {
            DB::beginTransaction();
                Penduduk::where('id', $penduduk_id)->update([
                    'foto' => '/storage'.$path
                ]);
            DB::commit();

            return([
                'status' => 'success',
                'message' => 'Foto profile berhasil dirubah'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();

            return([
                'status' => 'failed',
                'message' => $th->getMessage()
            ]);
        }
    }

}
