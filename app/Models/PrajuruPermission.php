<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrajuruPermission extends Model
{
    use SoftDeletes;
    protected $table = 'tb_permission_prajuru';

    public function desa_adat()
    {
        return $this->belongsTo(DesaAdat::class, 'desa_adat_id', 'id');
    }
}
