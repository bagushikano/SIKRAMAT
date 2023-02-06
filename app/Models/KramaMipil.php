<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KramaMipil extends Model
{
    use SoftDeletes;
    protected $table = 'tb_krama_mipil';

    public function cacah_krama_mipil(){
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_id', 'id');
    }

    public function anggota() {
        return $this->hasMany(AnggotaKramaMipil::class, 'krama_mipil_id', 'id');
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }
}
