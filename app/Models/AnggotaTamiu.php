<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnggotaTamiu extends Model
{
    use SoftDeletes;
    protected $table = 'tb_anggota_tamiu';

    public function cacah_tamiu()
    {
        return $this->belongsTo(CacahTamiu::class, 'cacah_tamiu_id', 'id');
    }

    public function tamiu()
    {
        return $this->belongsTo(Tamiu::class, 'tamiu_id', 'id');
    }
}
