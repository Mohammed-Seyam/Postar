<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests\StoreVideoRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VideoController extends Controller
{
    public function __construct(protected VideoService $videoService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $platform = $request->query('platform');
        $videos = $this->videoService->list($request->user(), $platform);
        return VideoResource::collection($videos);
    }

    public function store(StoreVideoRequest $request): VideoResource
    {
        $video = $this->videoService->upload($request->user(), $request->file('video'));
        return new VideoResource($video);
    }

    public function show(Video $video): VideoResource
    {
        $this->authorize('view', $video);
        return new VideoResource($video);
    }

    public function destroy(Video $video): JsonResponse
    {
        $this->authorize('delete', $video);
        $this->videoService->delete($video);
        return response()->json(null, 204);
    }
}
