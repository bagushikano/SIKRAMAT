<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeluargaKrama extends Model
{
    use SoftDeletes;
    protected $table = 'tb_keluarga_krama';

    public function anggota() { 
        return $this->hasMany(AnggotaKeluargaKrama::class, 'keluarga_krama_id', 'id');
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }
}
