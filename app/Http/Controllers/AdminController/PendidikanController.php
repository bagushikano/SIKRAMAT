<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pendidikan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PendidikanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $pendidikans = Pendidikan::get();
        return view('pages.admin.m_pendidikan.m_pendidikan', compact('pendidikans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenjang_pendidikan' => 'required|unique:tb_m_pendidikan|regex:/^[a-zA-Z]+$/u'
        ],[
            'jenjang_pendidikan.required' => "Jenjang pendidikan wajib diisi",
            'jenjang_pendidikan.unique' => "Jenjang pendidikan yang dimasukkan telah terdaftar",
            'jenjang_pendidikan.regex' => "Jenjang pendidikan yang dimasukkan tidak hanya boleh mengandung huruf",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $pendidikan = new Pendidikan();
        $pendidikan->jenjang_pendidikan = $request->jenjang_pendidikan;
        $pendidikan->save();

        return back()->with('success', 'Jenjang pendidikan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pendidikan = Pendidikan::find($id);
        return response()->json(['success' => 'Berhasil', 'pendidikan' => $pendidikan]);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenjang_pendidikan' => 'required|unique:tb_m_pendidikan',
            'jenjang_pendidikan' => [
                Rule::unique('tb_m_pendidikan')->ignore($id),
            ],
        ],[
            'jenjang_pendidikan.required' => "Jenjang pendidikan wajib diisi",
            'jenjang_pendidikan.unique' => "Jenjang pendidikan yang dimasukkan telah terdaftar",
        ]);

        if($validator->fails()){
            return back()->withInput()->withErrors($validator);
        }

        $pendidikan = Pendidikan::find($id);
        $pendidikan->jenjang_pendidikan = $request->jenjang_pendidikan;
        $pendidikan->update();

        return back()->with('success', 'Jenjang pendidikan berhasil diperbaharui');
    }


    public function destroy($id, Request $request)
    {
        $pendidikan = Pendidikan::find($id);
        $pendidikan->delete();
        return back()->with('success', 'Jenjang pendidikan berhasil dihapus');
    }
}
