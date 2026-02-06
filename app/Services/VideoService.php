<?php

namespace App\Services;

use App\Jobs\ProcessVideoJob;
use App\Models\User;
use App\Models\Video;
use App\Repositories\VideoRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoService
{
    public function __construct(protected VideoRepository $videoRepository)
    {
    }

    public function upload(User $user, UploadedFile $file): Video
    {
        $path = $file->store('videos/' . $user->id, 's3');

        $video = $this->videoRepository->create([
            'user_id' => $user->id,
            'file_path' => $path,
            'status' => 'draft',
            'duration' => 0, // Will be updated by job
        ]);

        ProcessVideoJob::dispatch($video);

        return $video;
    }

    public function list(User $user): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->videoRepository->listForUser($user->id);
    }

    public function delete(Video $video): bool
    {
        // Optionally delete from S3
        // Storage::disk('s3')->delete($video->file_path);
        
        return $this->videoRepository->delete($video);
    }
}
