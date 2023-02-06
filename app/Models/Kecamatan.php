<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kecamatan extends Model
{
    use SoftDeletes;
    protected $table = 'tb_m_kecamatan';
    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id', 'id');
    }
}
