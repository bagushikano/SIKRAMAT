<?php

namespace App\Services;

use App\Models\BanjarAdat;

class BanjarAdatService
{
    public function findByDesaAdatId($desadat_id)
    {
        $banjar_adat = BanjarAdat::where('desa_adat_id', $desadat_id)->get();
        return $banjar_adat;
    }
}
