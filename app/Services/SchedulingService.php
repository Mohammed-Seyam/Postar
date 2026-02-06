<?php

namespace App\Services;

use App\Models\ScheduledPost;
use App\Models\User;
use App\Models\Video;
use App\Repositories\SchedulingRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class SchedulingService
{
    public function __construct(protected SchedulingRepository $schedulingRepository)
    {
    }

    public function schedule(User $user, array $data): ScheduledPost
    {
        $video = Video::where('id', $data['video_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        if ($video->status !== 'draft' && $video->status !== 'ready') {
            // Logic to check if video is ready to be scheduled
        }

        $data['status'] = 'pending';
        
        $post = $this->schedulingRepository->create($data);
        
        $video->update(['status' => 'scheduled']);

        return $post;
    }

    public function update(ScheduledPost $post, array $data): ScheduledPost
    {
        if ($post->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Cannot update a post that is already publishing or published.'],
            ]);
        }

        if (empty($data)) {
            $receivedKeys = implode(', ', array_keys(request()->all()));
            throw ValidationException::withMessages([
                'status' => ["No valid fields provided for update. Received keys: [{$receivedKeys}]. Allowed: caption, hashtags, publish_at."],
            ]);
        }

        \Illuminate\Support\Facades\Log::info('Updating post', ['id' => $post->id, 'data' => $data]);
        $updated = $this->schedulingRepository->update($post, $data);
        \Illuminate\Support\Facades\Log::info('Update result', ['updated' => $updated]);

        return $post->refresh();
    }

    public function cancel(ScheduledPost $post): void
    {
        if ($post->status !== 'pending') {
             throw ValidationException::withMessages([
                'status' => ['Cannot cancel a post that is already publishing or published.'],
            ]);
        }
        
        $post->video->update(['status' => 'draft']);
        $this->schedulingRepository->delete($post);
    }

    public function listUpcoming(User $user): LengthAwarePaginator
    {
        return $this->schedulingRepository->listUpcomingForUser($user->id);
    }
}
