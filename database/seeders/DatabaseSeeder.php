<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::factory(30)->create();
        $courses = Course::factory(5)->create();

        foreach ($students->take(20) as $index => $student) {
            Enrollment::factory()->create([
                'student_id' => $student->id,
                'course_id' => $courses[$index % $courses->count()]->id,
            ]);
        }
    }
}
