<?php

namespace Tests\Feature;

use App\Enums\StudentStatus;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_student(): void
    {
        $this->postJson('/api/v1/students', [
            'first_name' => 'Ana',
            'last_name' => 'Silva',
            'email' => 'ana@example.com',
            'birth_date' => '2001-03-10',
        ])
            ->assertCreated()
            ->assertJsonPath('data.email', 'ana@example.com')
            ->assertJsonPath('data.status', StudentStatus::Active->value);

        $this->assertDatabaseHas('students', ['email' => 'ana@example.com']);
    }

    public function test_email_must_be_unique(): void
    {
        Student::factory()->create(['email' => 'used@example.com']);

        $this->postJson('/api/v1/students', [
            'first_name' => 'João',
            'last_name' => 'Costa',
            'email' => 'used@example.com',
        ])->assertUnprocessable();
    }

    public function test_it_filters_students_by_status(): void
    {
        Student::factory()->count(2)->create(['status' => StudentStatus::Active]);
        Student::factory()->create(['status' => StudentStatus::Graduated]);

        $this->getJson('/api/v1/students?status=graduated')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'graduated');
    }

    public function test_it_soft_deletes_a_student(): void
    {
        $student = Student::factory()->create();

        $this->deleteJson("/api/v1/students/{$student->id}")
            ->assertNoContent();

        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }
}
