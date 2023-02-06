<?php

namespace App\Services;

use App\Models\Pekerjaan;

class PekerjaanService
{
    public function all()
    {
        $pekerjaan = Pekerjaan::get();
        return $pekerjaan;
    }

    public function find($pekerjaan_id)
    {
        $pekerjaan = Pekerjaan::where('id', $pekerjaan_id)->get();
        return $pekerjaan;
    }
}
