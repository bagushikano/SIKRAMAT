<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tamiu extends Model
{
    use SoftDeletes;
    protected $table = 'tb_tamiu';

    public function cacah_tamiu(){
        return $this->belongsTo(CacahTamiu::class, 'cacah_tamiu_id', 'id');
    }

    public function anggota() {
        return $this->hasMany(AnggotaTamiu::class, 'tamiu_id', 'id');
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }
}
