<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Negara extends Model
{
    use SoftDeletes;
    protected $table = 'tb_m_negara';
}
