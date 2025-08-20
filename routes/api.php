<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\wekar\CommonController;
use App\Http\Controllers\Api\wekar\AuthController;

Route::get('/get/nav', [CommonController::class, 'nav']);
Route::get('/get/website/setting', [CommonController::class, 'setting']);
Route::get('/get/website/page/{slug}', [CommonController::class, 'page']);
Route::get('/get/service/detail/{slug}', [CommonController::class, 'getService']);
Route::post('/checkUser', [AuthController::class, 'checkUser']);
Route::post('/doLogin', [AuthController::class, 'doLogin']);
Route::post('/doSignup', [AuthController::class, 'doSignup']);