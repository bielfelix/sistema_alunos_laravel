<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $enrollment = DB::transaction(function () use ($validated): Enrollment {
            $course = Course::query()
                ->whereKey($validated['course_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! $course->active) {
                throw ValidationException::withMessages([
                    'course_id' => ['The selected course is inactive.'],
                ]);
            }

            $alreadyEnrolled = Enrollment::query()
                ->where('student_id', $validated['student_id'])
                ->where('course_id', $course->id)
                ->exists();

            if ($alreadyEnrolled) {
                throw ValidationException::withMessages([
                    'student_id' => ['The student already has an enrollment record for this course.'],
                ]);
            }

            if ($course->capacity !== null) {
                $activeEnrollments = Enrollment::query()
                    ->where('course_id', $course->id)
                    ->where('status', EnrollmentStatus::Active->value)
                    ->count();

                if ($activeEnrollments >= $course->capacity) {
                    throw ValidationException::withMessages([
                        'course_id' => ['The course has reached its enrollment capacity.'],
                    ]);
                }
            }

            return Enrollment::create([
                ...$validated,
                'status' => EnrollmentStatus::Active,
                'enrolled_at' => $validated['enrolled_at'] ?? now(),
            ]);
        });

        return (new EnrollmentResource($enrollment->load(['student', 'course'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Enrollment $enrollment): EnrollmentResource
    {
        return new EnrollmentResource($enrollment->load(['student', 'course']));
    }

    public function destroy(Enrollment $enrollment): Response
    {
        if ($enrollment->status !== EnrollmentStatus::Cancelled) {
            $enrollment->update([
                'status' => EnrollmentStatus::Cancelled,
                'completed_at' => null,
            ]);
        }

        return response()->noContent();
    }
}
