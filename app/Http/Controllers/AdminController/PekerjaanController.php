<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pekerjaan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PekerjaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $pekerjaans = Pekerjaan::get();
        return view('pages.admin.m_pekerjaan.m_pekerjaan', compact('pekerjaans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profesi' => 'required|unique:tb_m_profesi|regex:/^[a-zA-Z]+$/u'
        ],[
            'profesi.required' => "Pekerjaan wajib diisi",
            'profesi.unique' => "Pekerjaan yang dimasukkan telah terdaftar",
            'profesi.regex' => "Pekerjaan yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $pekerjaan = new Pekerjaan();
        $pekerjaan->profesi = $request->profesi;
        $pekerjaan->save();

        return back()->with('success', 'Pekerjaan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pekerjaan = Pekerjaan::find($id);
        return response()->json(['success' => 'Berhasil', 'pekerjaan' => $pekerjaan]);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profesi' => 'required|unique:tb_m_profesi|regex:/^[a-zA-Z]+$/u',
            'profesi' => [
                Rule::unique('tb_m_profesi')->ignore($id),
            ],
        ],[
            'profesi.required' => "Pekerjaan wajib diisi",
            'profesi.unique' => "Pekerjaan yang dimasukkan telah terdaftar",
            'profesi.regex' => "Pekerjaan yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $pekerjaan = Pekerjaan::find($id);
        $pekerjaan->profesi = $request->profesi;
        $pekerjaan->update();

        return back()->with('success', 'Pekerjaan berhasil diperbaharui');
    }


    public function destroy($id)
    {
        $pekerjaan = Pekerjaan::find($id);
        $pekerjaan->delete();
        return back()->with('success', 'Pekerjaan berhasil dihapus');
    }
}
