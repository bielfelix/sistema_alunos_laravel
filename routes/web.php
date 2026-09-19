<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'service' => 'student-management-api',
    'version' => 'v1',
    'health' => '/up',
    'api' => '/api/v1',
]));
