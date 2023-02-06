<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KramaTamiu extends Model
{
    use SoftDeletes;
    protected $table = 'tb_krama_tamiu';

    public function cacah_krama_tamiu(){
        return $this->belongsTo(CacahKramaTamiu::class, 'cacah_krama_tamiu_id', 'id');
    }

    public function anggota() {
        return $this->hasMany(AnggotaKramaTamiu::class, 'krama_tamiu_id', 'id');
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }
}
