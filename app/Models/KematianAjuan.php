<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KematianAjuan extends Model
{
    use SoftDeletes;
    protected $table = 'tb_kematian_ajuan';

    public function cacah_krama_mipil()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_id', 'id');
    }

    public function kematian()
    {
        return $this->belongsTo(Kematian::class, 'kematian_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
