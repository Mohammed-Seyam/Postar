<?php

namespace App\Services;

use App\Jobs\ProcessVideoJob;
use App\Models\User;
use App\Models\Video;
use App\Repositories\VideoRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VideoService
{
    public function __construct(protected VideoRepository $videoRepository)
    {
    }

    public function upload(User $user, UploadedFile $file): Video
    {
        $path = $file->store('videos/' . $user->id, 's3');

        try {
            $video = $this->videoRepository->create([
                'user_id' => $user->id,
                'file_path' => $path,
                'status' => 'draft',
                'duration' => 0, // Will be updated by job
            ]);

            ProcessVideoJob::dispatch($video);

            return $video;
        } catch (\Exception $e) {
            Storage::disk('s3')->delete($path);
            throw $e;
        }
    }

    public function list(User $user, ?string $platform = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->videoRepository->listForUser($user->id, 15, $platform);
    }

    public function delete(Video $video): bool
    {
        if ($video->scheduledPosts()->exists()) {
             throw ValidationException::withMessages([
                'video' => ['Cannot delete video that has scheduled posts.'],
            ]);
        }

        // Optionally delete from S3
        // Storage::disk('s3')->delete($video->file_path);
        
        return $this->videoRepository->delete($video);
    }
}
