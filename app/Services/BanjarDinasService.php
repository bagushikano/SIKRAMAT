<?php

namespace App\Services;

use App\Models\BanjarDinas;

class BanjarDinasService
{
    public function findByDesaAdatId($desa_adat_id)
    {
        $banjar_dinas = BanjarDinas::where('desa_adat_id', $desa_adat_id)->get();
        return $banjar_dinas;
    }
}
