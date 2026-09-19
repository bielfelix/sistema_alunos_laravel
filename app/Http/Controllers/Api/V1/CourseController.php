<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CourseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 15), 1), 100);

        return CourseResource::collection(
            Course::query()
                ->withCount('enrollments')
                ->when(
                    $request->has('active'),
                    fn ($query) => $query->where('active', $request->boolean('active'))
                )
                ->orderBy('code')
                ->paginate($perPage)
                ->withQueryString()
        );
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        return (new CourseResource(Course::create($request->validated())))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Course $course): CourseResource
    {
        return new CourseResource($course->loadCount('enrollments'));
    }

    public function update(UpdateCourseRequest $request, Course $course): CourseResource
    {
        $course->update($request->validated());

        return new CourseResource($course->refresh()->loadCount('enrollments'));
    }

    public function destroy(Course $course): Response
    {
        $course->delete();

        return response()->noContent();
    }
}
