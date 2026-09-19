<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function create(array $data): Enrollment
    {
        return DB::transaction(function () use ($data): Enrollment {
            $course = Course::query()
                ->whereKey($data['course_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! $course->active) {
                throw ValidationException::withMessages([
                    'course_id' => ['The selected course is inactive.'],
                ]);
            }

            $alreadyEnrolled = Enrollment::query()
                ->where('student_id', $data['student_id'])
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
                ...$data,
                'status' => EnrollmentStatus::Active,
                'enrolled_at' => $data['enrolled_at'] ?? now(),
            ]);
        });
    }

    public function cancel(Enrollment $enrollment): void
    {
        if ($enrollment->status === EnrollmentStatus::Cancelled) {
            return;
        }

        $enrollment->update([
            'status' => EnrollmentStatus::Cancelled,
            'completed_at' => null,
        ]);
    }
}
