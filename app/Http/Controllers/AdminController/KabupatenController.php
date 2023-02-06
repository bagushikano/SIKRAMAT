<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kabupaten;
use Illuminate\Support\Facades\Validator;

class KabupatenController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function index()
    {
        $kabupatens = Kabupaten::with('provinsi')->get();
        return view('pages.admin.m_kabupaten.m_kabupaten', compact('kabupatens'));
    }

    public function get($id){
        $kabupatens = Kabupaten::where('provinsi_id', $id)->get();
        return response()->json([
            $kabupatens
        ]);
    }
}
