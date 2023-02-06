<?php

namespace App\Http\Controllers\KramaController\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class DashboardKramaController extends Controller
{
    public function index()
    {
        return view('pages.krama.dashboard.dashboard-krama');
    }
}
