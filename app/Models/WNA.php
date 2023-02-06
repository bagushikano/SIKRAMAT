<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WNA extends Model
{
    use SoftDeletes;
    protected $table = 'tb_wna';

    public function negara() { 
        return $this->belongsTo(Negara::class, 'negara_id', 'id');
    }

    public function banjar_adat()
    {
        return $this->belongsTo(BanjarAdat::class, 'banjar_adat_id', 'id');
    }

    public function banjar_dinas()
    {
        return $this->belongsTo(BanjarDinas::class, 'banjar_dinas_id', 'id');
    }
}
