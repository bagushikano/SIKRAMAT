<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrajuruBanjarAdat extends Model
{
    use SoftDeletes;
    protected $table = 'tb_prajuru_banjar_adat';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }

    public function krama_mipil()
    {
        return $this->belongsTo(KramaMipil::class, 'krama_mipil_id', 'id');
    }
}
