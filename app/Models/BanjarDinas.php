<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BanjarDinas extends Model
{
    use SoftDeletes;
    protected $table = 'tb_m_banjar_dinas';

    public function desa_adat()
    {
        return $this->belongsTo(DesaAdat::class, 'desa_adat_id', 'id');
    }

    public function desa_dinas()
    {
        return $this->belongsTo(DesaDinas::class, 'desa_dinas_id', 'id');
    }
}
