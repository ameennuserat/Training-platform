<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\DiscountController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});

Route::post('/addspecial',[SpecializationController::class,'addspecial'])->middleware('auth:api');
Route::get('/getallspecil',[SpecializationController::class,'getallspecil']);



Route::post('/oldepassword',[AuthController::class,'oldepassword'])->middleware('auth:api');
Route::post('/updatepassword',[AuthController::class,'updatepassword'])->middleware('auth:api');
Route::post('/forgotpassword',[AuthController::class,'forgetpassword']);
Route::post('/checkcode/email',[AuthController::class,'checkcode']);
Route::post('/checkcode/password',[AuthController::class,'__invoke']);
Route::post('/resetpassword',[AuthController::class,'resetpassword']);
Route::post('/updatephon',[AuthController::class,'updatephon'])->middleware('auth:api');
Route::post('/UpdateCertificateHolder',[AuthController::class,'UpdateCertificateHolder'])->middleware('auth:api');
Route::post('/addvideo/{id}',[AuthController::class,'addvideo'])->middleware('auth:api');
Route::post('/watching/{id}',[AuthController::class,'watching']);
Route::get('/fnishcourse',[AuthController::class,'fnishcourse']);


Route::post('/registcourse/{id}',[CoursesController::class,'registcourse'])->middleware('auth:api');
Route::post('/addcourse',[CoursesController::class,'addcourse'])->middleware('auth:api');
Route::get('/getallcourse',[CoursesController::class,'getallcourse']);
Route::post('/searchcourse',[CoursesController::class,'searchcourse']);
Route::post('/Archives',[CoursesController::class,'Archives'])->middleware('auth:api');
Route::get('/getvideo/{id}',[CoursesController::class,'getvideo']);
Route::get('/getcourse/{id}',[CoursesController::class,'getcourse']);


Route::post('/adddiscount/{id}',[DiscountController::class,'adddiscount'])->middleware('auth:api');
Route::post('/retreatdiscount/{id}',[DiscountController::class,'retreatdiscount'])->middleware('auth:api');
Route::get('/coursesdiscount',[DiscountController::class,'coursesdiscount']);
