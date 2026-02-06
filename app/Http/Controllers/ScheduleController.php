<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduledPostResource;
use App\Models\ScheduledPost;
use App\Services\SchedulingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScheduleController extends Controller
{
    public function __construct(protected SchedulingService $schedulingService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $posts = $this->schedulingService->listUpcoming($request->user());
        return ScheduledPostResource::collection($posts);
    }

    public function store(StoreScheduleRequest $request): ScheduledPostResource
    {
        $post = $this->schedulingService->schedule($request->user(), $request->validated());
        return new ScheduledPostResource($post);
    }

    public function show(ScheduledPost $schedule): ScheduledPostResource
    {
        $this->authorize('view', $schedule);
        return new ScheduledPostResource($schedule);
    }

    public function update(UpdateScheduleRequest $request, ScheduledPost $schedule): ScheduledPostResource
    {
        $this->authorize('update', $schedule);
        $post = $this->schedulingService->update($schedule, $request->validated());
        return new ScheduledPostResource($post);
    }

    public function destroy(ScheduledPost $schedule): JsonResponse
    {
        $this->authorize('delete', $schedule);
        $this->schedulingService->cancel($schedule);
        return response()->json(null, 204);
    }
}
