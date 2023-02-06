<?php

namespace App\Services;

use App\Models\Pendidikan;

class PendidikanService
{
    public function all()
    {
        $pendidikan = Pendidikan::get();
        return $pendidikan;
    }

    public function find($pendidikan_id)
    {
        $pendidikan = Pendidikan::where('id', $pendidikan_id)->get();
        return $pendidikan;
    }
}
