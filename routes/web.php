<?php

use App\Http\Controllers\AdminController\DesaAdatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//  AUTH ROUTES
    Route::get('/login', 'AuthController\AuthController@loginForm')->name('login-form')->middleware('guest');
    Route::post('/login', 'AuthController\AuthController@login')->name('login-post');
    Route::get('/logout', 'AuthController\AuthController@logout')->name('logout');
    Route::get('/reload-captcha', 'AuthController\AuthController@reload_captcha')->name('reload-captcha');
    Route::get('/email/verified', 'AuthController\AuthController@email_verified')->name('email-verified')->middleware('auth','verified');
    Auth::routes(['register'=>false, 'login'=>false, 'verify'=>true]);
    Route::middleware('verified')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::group(['middleware' => ['role:krama,admin_banjar_adat,kelihan_adat,pangliman_banjar,penyarikan_banjar,patengen_banjar', 'verified'], 'prefix' => 'notifikasi'], function () {
        Route::get('/get-notifikasi/{role}', 'NotifikasiController\NotifikasiController@getNotifikasi')->name('get-notifikasi');
        Route::get('/read-notifikasi/{id}', 'NotifikasiController\NotifikasiController@readNotifikasi')->name('read-notifikasi');
        Route::get('/read-all-notifikasi/{role}', 'NotifikasiController\NotifikasiController@markAllAsRead')->name('read-all-notifikasi');
    });

// AKHIR AUTH ROUTES



Route::get('/', function () {
    return redirect()->route('login-post');
});

Route::get('/home', 'HomeController@index')->name('home');
