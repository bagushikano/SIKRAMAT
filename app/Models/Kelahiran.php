<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelahiran extends Model
{
    use SoftDeletes;
    protected $table = 'tb_kelahiran';

    public function krama_mipil()
    {
        return $this->belongsTo(KramaMipil::class, 'krama_mipil_id', 'id');
    }

    public function cacah_krama_mipil()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_id', 'id')->withTrashed();
    }

    public function kelahiran_ajuan()
    {
        return $this->hasOne(KelahiranAjuan::class, 'id', 'kelahiran_id')->withTrashed();
    }
}
