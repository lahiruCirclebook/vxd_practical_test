<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\SubmissionController;


Route::middleware('api')->group(function () {
    // Form management routes
    Route::apiResource('forms', FormController::class);

    // Form submission routes
    Route::post('forms/{form}/submit', [SubmissionController::class, 'store']);
    Route::get('forms/{form}/submissions', [SubmissionController::class, 'index']);
    Route::get('forms/{form}/submissions/{submission}', [SubmissionController::class, 'show']);
});