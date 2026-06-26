<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/workers/gamedata', 'WorkerController@gameData');
Route::get('/workers/metrics', 'WorkerController@metrics');
Route::get('/workers/usersfinished', 'WorkerController@listUsersFinished');
Route::get('/workers/usersnotfinished', 'WorkerController@listUsersNotFinished');
Route::get('/workers/usersnotplayed', 'WorkerController@listUsersNotPlayed');
Route::get('/workers', 'WorkerController@index');
Route::get('/workers/{dni}', 'WorkerController@show');
Route::get('/workers/{dni}/report', 'WorkerController@report');
Route::post('/workers', 'WorkerController@store');
Route::put('/workers/{dni}', 'WorkerController@update');
Route::delete('/workers/{dni}', 'WorkerController@delete');
Route::delete('/workers/{dni}/clean', 'WorkerController@cleanRegister');

Route::post('/registry/{dni}', 'RegistryController@store');

Route::get('/selectoras', 'SelectoraController@selectoras');
Route::put('/selectoras/{id}', 'SelectoraController@update');
Route::delete('/selectoras/{id}', 'SelectoraController@delete');
Route::post('/selectoras', 'SelectoraController@save');

