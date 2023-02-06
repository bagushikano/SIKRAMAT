<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CacahTamiu extends Model
{
    use SoftDeletes;
    protected $table = 'tb_cacah_tamiu';

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id', 'id');
    }

    public function wna()
    {
        return $this->belongsTo(WNA::class, 'wna_id', 'id');
    }

    public function banjar_dinas()
    {
        return $this->belongsTo(BanjarDinas::class, 'banjar_dinas_id', 'id');
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }
}