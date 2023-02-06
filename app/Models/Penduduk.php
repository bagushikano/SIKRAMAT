<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penduduk extends Model
{
    use SoftDeletes;
    protected $table = 'tb_penduduk';

    public function ayah()
    {
        return $this->belongsTo(Penduduk::class, 'ayah_kandung_id', 'id');
    }

    public function ibu()
    {
        return $this->belongsTo(Penduduk::class, 'ibu_kandung_id', 'id');
    }

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class, 'profesi_id', 'id');
    }

    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id', 'id');
    }

    public function cacah_krama_tamiu()
    {
        return $this->hasMany(CacahKramaTamiu::class, 'penduduk_id')->whereNull('tanggal_keluar')->orderBy('created_at');
    }

    public function cacah_krama_mipil()
    {
        return $this->hasMany(CacahKramaMipil::class, 'penduduk_id')->where('status', 1)->orderBy('created_at');
    }

    public  function scopeLike($query, $field, $value)
    {
        return $query->where($field, 'LIKE', "%$value%");
    }
}
