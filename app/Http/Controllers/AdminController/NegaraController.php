<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use App\Models\Negara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Provinsi;
use Illuminate\Support\Facades\Validator;

class NegaraController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function search(Request $request)
    {
        $negaras = Negara::where('code', 'LIKE', '%'.$request->input('term', '').'%')->orWhere('name', 'LIKE', '%'.$request->input('term', '').'%')->get(['id', 'code', 'name']);
        $response = array();
        foreach($negaras as $negara){
            $response[] = array(
                "id"=>$negara->id,
                "text"=>$negara->code.' - '.$negara->name
            );
        }
        return ['results' => $response];
    }
}
