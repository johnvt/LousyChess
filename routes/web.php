<?php

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

Route::view('/', 'home')->name('home');

Route::get('games', 'GameController@index')->name('games.index');
Route::get('games/create', 'GameController@create')->name('games.create');
Route::get('games/run', 'GameController@run')->name('games.run');
Route::get('games/seeds', 'GameController@seeds');
Route::get('games/{game}', 'GameController@show')->name('games.show');



Route::get('engines', 'EngineController@index')->name('engines.index');
