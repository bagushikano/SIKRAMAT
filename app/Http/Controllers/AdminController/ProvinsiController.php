<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use Illuminate\Support\Facades\Validator;

class ProvinsiController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
        $provinsis = Provinsi::get();
        return view('pages.admin.m_provinsi.m_provinsi', compact('provinsis'));
    }
}
