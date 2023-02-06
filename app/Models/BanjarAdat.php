<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanjarAdat extends Model
{
    use SoftDeletes;
    protected $table = 'tb_m_banjar_adat';

    public function desa_adat()
    {
        return $this->belongsTo(DesaAdat::class, 'desa_adat_id', 'id');
    }
}
