<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NetworkController;

Route::get('/health', [NetworkController::class, 'health']);
Route::post('/analyze', [NetworkController::class, 'analyze']);
Route::post('/repair', [NetworkController::class, 'repair']);
