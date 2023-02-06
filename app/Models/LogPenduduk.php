<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogPenduduk extends Model
{
    use SoftDeletes;
    protected $table = 'tb_log_penduduk';

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id', 'id');
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'profesi_id', 'id');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
    }
}
