<?php

namespace App\Services;

use App\Models\Tempekan;

class TempekanService
{
    public function findByBanjar($banjar_adat_id)
    {
        $tempekan = Tempekan::where('banjar_adat_id', $banjar_adat_id)->get();
        return $tempekan;
    }
}
