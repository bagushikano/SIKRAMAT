<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tempekan extends Model
{
    use SoftDeletes;
    protected $table = 'tb_m_tempekan';

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }


    public function cacah_mipil()
    {
        return $this->hasMany(CacahKramaMipil::class, 'id', 'tempekan_id');
    }
}
