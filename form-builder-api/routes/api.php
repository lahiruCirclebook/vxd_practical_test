<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Form Builder API routes
Route::prefix('forms')->group(function () {
    // Add your form builder routes here
    Route::get('/', function () {
        return response()->json(['message' => 'Form Builder API is working!']);
    });
});
