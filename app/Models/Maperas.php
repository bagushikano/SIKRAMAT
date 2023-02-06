<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maperas extends Model
{
    use SoftDeletes;
    protected $table = 'tb_maperas';

    public function krama_mipil_lama()
    {
        return $this->belongsTo(KramaMipil::class, 'krama_mipil_lama_id', 'id');
    }

    public function krama_mipil_baru()
    {
        return $this->belongsTo(KramaMipil::class, 'krama_mipil_baru_id', 'id');
    }

    public function cacah_krama_mipil_lama()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_lama_id', 'id');
    }

    public function cacah_krama_mipil_baru()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_baru_id', 'id');
    }

    public function banjar_adat_lama()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_lama_id', 'id');
    }

    public function banjar_adat_baru()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_baru_id', 'id');
    }

    public function desa_adat_lama()
    {
        return $this->belongsTo(DesaAdat::class, 'desa_adat_lama_id', 'id');
    }

    public function desa_adat_baru()
    {
        return $this->belongsTo(DesaAdat::class, 'desa_adat_baru_id', 'id');
    }

    public function ayah_baru()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'ayah_baru_id', 'id');
    }

    public function ibu_baru()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'ibu_baru_id', 'id');
    }

    public function ayah_lama()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'ayah_lama_id', 'id');
    }

    public function ibu_lama()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'ibu_lama_id', 'id');
    }

    public function desa_dinas_asal()
    {
        return $this->belongsTo(DesaDinas::class, 'desa_asal_id', 'id');
    }
}
