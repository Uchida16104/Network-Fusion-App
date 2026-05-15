<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'network-fusion-backend',
        'message' => 'Use /api/health, /api/analyze, /api/repair',
    ]);
});
