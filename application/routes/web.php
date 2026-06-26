<?php

use Illuminate\Support\Facades\Mail;

use App\Mail\NoticeMessage;

Route::get('/', function () {
    return Redirect::to('/public/game/');
})->name('games');

Route::get('/login', 'LoginController@showLogin')->name('loginPage');

Route::post('/login', 'LoginController@doLogin')->name('login');

Route::get('/admin/users', 'HomeController@users')->name('users');
Route::get('/admin/selectorasList', 'HomeController@selectorasList')->name('selectorasList');
Route::get('/admin/listUser/{option}', 'HomeController@listUser')->name('addUser');
Route::get('/admin', 'HomeController@index')->name('admin');
Route::get('/logout', 'HomeController@doLogout')->name('logout');

Route::get('/sendemail', 'MessageController@index');

Route::get('/sendemail/{email}','MessageController@prueba');