<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perceraian extends Model
{
    use SoftDeletes;
    protected $table = 'tb_perceraian';

    public function banjar_adat_purusa()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_purusa_id', 'id');
    }

    public function desa_adat_purusa()
    {
        return $this->belongsTo(DesaAdat::class, 'desa_adat_purusa_id', 'id');
    }

    public function banjar_adat_pradana()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_pradana_id', 'id');
    }

    public function desa_adat_pradana()
    {
        return $this->belongsTo(DesaAdat::class, 'desa_adat_pradana_id', 'id');
    }

    public function purusa(){
        return $this->belongsTo(CacahKramaMipil::class, 'purusa_id', 'id');
    }

    public function pradana(){
        return $this->belongsTo(CacahKramaMipil::class, 'pradana_id', 'id');
    }
}
