<?php

use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::apiResource('students', StudentController::class);
    Route::apiResource('courses', CourseController::class);

    Route::post('enrollments', [EnrollmentController::class, 'store']);
    Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show']);
    Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy']);
});
