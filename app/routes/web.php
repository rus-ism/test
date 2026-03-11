<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test/start', [TestController::class, 'start']);
Route::post('/test/start', [TestController::class, 'startTest']);

Route::get('/test/{attempt}', [TestController::class, 'showTest']);

Route::post('/test/{attempt}/submit', [TestController::class, 'submitTest']);


Route::get('/api/schools/{district}', [TestController::class, 'getSchools']);

Route::get('/test/result/{attempt}', [TestController::class, 'result']);