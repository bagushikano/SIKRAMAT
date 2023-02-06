<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnggotaKramaTamiu extends Model
{
    use SoftDeletes;
    protected $table = 'tb_anggota_krama_tamiu';

    public function cacah_krama_tamiu()
    {
        return $this->belongsTo(CacahKramaTamiu::class, 'cacah_krama_tamiu_id', 'id');
    }

    public function krama_tamiu()
    {
        return $this->belongsTo(KramaTamiu::class, 'krama_tamiu_id', 'id');
    }
}
