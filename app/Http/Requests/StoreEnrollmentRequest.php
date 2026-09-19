<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'ulid', 'exists:students,id'],
            'course_id' => ['required', 'ulid', 'exists:courses,id'],
            'enrolled_at' => ['nullable', 'date'],
        ];
    }
}
