<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EnrollmentController extends Controller
{
    public function __construct(
        private readonly EnrollmentService $enrollments,
    ) {
    }

    public function store(StoreEnrollmentRequest $request): JsonResponse
    {
        $enrollment = $this->enrollments->create($request->validated());

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
        $this->enrollments->cancel($enrollment);

        return response()->noContent();
    }
}
