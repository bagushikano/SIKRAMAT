<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelahiranAjuan extends Model
{
    use SoftDeletes;
    protected $table = 'tb_kelahiran_ajuan';

    public function krama_mipil()
    {
        return $this->belongsTo(KramaMipil::class, 'krama_mipil_id', 'id');
    }

    public function cacah_krama_mipil()
    {
        return $this->belongsTo(CacahKramaMipil::class, 'cacah_krama_mipil_id', 'id')->withTrashed();
    }

    public function kelahiran()
    {
        return $this->belongsTo(Kelahiran::class, 'kelahiran_id', 'id')->withTrashed();
    }
    
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
