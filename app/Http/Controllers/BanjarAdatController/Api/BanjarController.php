<?php

namespace App\Http\Controllers\BanjarAdatController\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanjarAdat;
use App\Models\BanjarDinas;
use Auth;

class BanjarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
}
