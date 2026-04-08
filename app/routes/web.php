<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Admin\StatsController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test/start', [TestController::class, 'start']);
Route::post('/test/start', [TestController::class, 'startTest']);

Route::get('/test/{attempt}', [TestController::class, 'showTest']);

Route::post('/test/{attempt}/submit', [TestController::class, 'submitTest']);


Route::get('/api/schools/{district}', [TestController::class, 'getSchools']);

Route::get('/test/result/{attempt}', [TestController::class, 'result']);

Route::get('/api/server-time', function () {
    return response()->json([
        'time' => now()->format('Y-m-d H:i:s')
    ]);
});


Route::get('/recalc-chemistry', [App\Http\Controllers\TestController::class, 'recalculateChemistry9Kaz']);


Route::prefix('admin')->group(function(){


    Route::get('/districts', [StatsController::class, 'districts']);

    Route::get('/district/{id}', [StatsController::class, 'district']);

    Route::get('/school/{school}/class/{grade}/subject/{subject}', 
        [StatsController::class, 'subjectStats']);    

     Route::get('/school/{id}', 
        [StatsController::class, 'school'])
        ->where('id','[0-9]+');

    Route::get('/admin/school/{id}', [StatsController::class, 'school']);

    

});