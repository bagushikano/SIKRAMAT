<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kabupaten extends Model
{
    use SoftDeletes;
    protected $table = 'tb_m_kabupaten';

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }
}
