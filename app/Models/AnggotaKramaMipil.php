<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnggotaKramaMipil extends Model
{
    use SoftDeletes;
    protected $table = 'tb_anggota_krama_mipil';

    public function cacah_krama_mipil()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_id', 'id');
    }

    public function krama_mipil()
    {
        return $this->belongsTo(KramaMipil::class, 'krama_mipil_id', 'id');
    }
}
