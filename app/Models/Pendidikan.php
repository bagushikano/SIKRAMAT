<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendidikan extends Model
{
    use SoftDeletes;
    protected $table = 'tb_m_pendidikan';
}
