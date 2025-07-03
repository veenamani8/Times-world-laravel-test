<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestLogController;

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
    return view('logs');
});

Route::get('/logs', [RequestLogController::class, 'index']);
Route::delete('/logs/clear', [RequestLogController::class, 'clear']);

// Routes that should be logged
Route::middleware('log.request')->prefix('request')->group(function () {
    Route::get('1', fn () => response()->json(['message' => 'Request 1 done']));
    Route::get('2', fn () => response()->json(['message' => 'Request 2 done']));
    Route::get('3', fn () => response()->json(['message' => 'Request 3 done']));
});
