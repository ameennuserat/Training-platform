<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/amen', function () {
    echo( date('h:i A'));
});
Route::get('/a', function () {
    $date = new \DateTime();
$time = $date->format('h:i A');
    echo($date->format('h:i A'));
});
