<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/forms', [FormController::class, 'store']);
Route::get('/forms', [FormController::class, 'index']);
Route::get('/forms/{id}', [FormController::class, 'show']);
Route::delete('/forms/{id}', [FormController::class, 'destroy']);
Route::put('/forms/{id}', [FormController::class, 'update']);