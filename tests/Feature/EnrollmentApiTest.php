<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_enrollment(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create(['capacity' => 2]);

        $this->postJson('/api/v1/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.student.id', $student->id)
            ->assertJsonPath('data.course.id', $course->id)
            ->assertJsonPath('data.status', 'active');
    }

    public function test_it_rejects_duplicate_enrollment(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create(['capacity' => 2]);

        $payload = ['student_id' => $student->id, 'course_id' => $course->id];

        $this->postJson('/api/v1/enrollments', $payload)->assertCreated();
        $this->postJson('/api/v1/enrollments', $payload)->assertUnprocessable();
    }

    public function test_it_enforces_course_capacity(): void
    {
        $course = Course::factory()->create(['capacity' => 1]);
        $firstStudent = Student::factory()->create();
        $secondStudent = Student::factory()->create();

        $this->postJson('/api/v1/enrollments', [
            'student_id' => $firstStudent->id,
            'course_id' => $course->id,
        ])->assertCreated();

        $this->postJson('/api/v1/enrollments', [
            'student_id' => $secondStudent->id,
            'course_id' => $course->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('course_id');
    }

    public function test_it_rejects_enrollment_in_inactive_course(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create(['active' => false]);

        $this->postJson('/api/v1/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('course_id');
    }

    public function test_it_cancels_an_enrollment_without_deleting_it(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create(['capacity' => 1]);

        $response = $this->postJson('/api/v1/enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ])->assertCreated();

        $enrollmentId = $response->json('data.id');

        $this->deleteJson("/api/v1/enrollments/{$enrollmentId}")
            ->assertNoContent();

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollmentId,
            'status' => 'cancelled',
        ]);
    }

    public function test_cancelled_enrollment_releases_course_capacity(): void
    {
        $course = Course::factory()->create(['capacity' => 1]);
        $firstStudent = Student::factory()->create();
        $secondStudent = Student::factory()->create();

        $firstEnrollment = $this->postJson('/api/v1/enrollments', [
            'student_id' => $firstStudent->id,
            'course_id' => $course->id,
        ])->assertCreated();

        $this->deleteJson('/api/v1/enrollments/'.$firstEnrollment->json('data.id'))
            ->assertNoContent();

        $this->postJson('/api/v1/enrollments', [
            'student_id' => $secondStudent->id,
            'course_id' => $course->id,
        ])->assertCreated();
    }
}
