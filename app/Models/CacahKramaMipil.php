<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CacahKramaMipil extends Model
{
    use SoftDeletes;
    protected $table = 'tb_cacah_krama_mipil';

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id', 'id')->withTrashed();
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }

    public function banjar_dinas()
    {
        return $this->belongsTo(BanjarDinas::class, 'banjar_dinas_id', 'id');
    }

    public function tempekan()
    {
        return $this->belongsTo(Tempekan::class, 'tempekan_id', 'id');
    }
}
