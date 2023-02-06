<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kematian extends Model
{
    use SoftDeletes;
    protected $table = 'tb_kematian';

    public function cacah_krama_mipil()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_id', 'id');
    }

    public function kematian_ajuan()
    {
        return $this->hasOne(KematianAjuan::class, 'id', 'kematian_id')->withTrashed();
    }
}
