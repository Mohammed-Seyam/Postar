<?php

namespace App\Services;

use App\Models\ScheduledPost;
use App\Models\User;
use App\Models\Video;
use App\Repositories\SchedulingRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchedulingService
{
    public function __construct(protected SchedulingRepository $schedulingRepository)
    {
    }

    public function schedule(User $user, array $data): ScheduledPost
    {
        return DB::transaction(function () use ($user, $data) {
            $video = Video::where('id', $data['video_id'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();
                
            if ($video->status !== 'ready') {
                throw ValidationException::withMessages([
                    'video_id' => ['Video is not ready to be scheduled (must be processed).'],
                ]);
            }

            $data['status'] = 'pending';
            
            $post = $this->schedulingRepository->create($data);
            
            $video->update(['status' => 'scheduled']); // This might technically be redundant if we allow multiple posts per video, but let's keep logic
            
            return $post;
        });
    }

    public function update(ScheduledPost $post, array $data): ScheduledPost
    {
        return DB::transaction(function () use ($post, $data) {
            // Re-fetch and lock to prevent concurrent updates
            $post = ScheduledPost::lockForUpdate()->find($post->id);

            if ($post->status !== 'pending') {
                throw ValidationException::withMessages([
                    'status' => ['Cannot update a post that is already publishing or published.'],
                ]);
            }

            if (empty($data)) {
                 // ... same exception ...
                 $receivedKeys = implode(', ', array_keys(request()->all()));
                 throw ValidationException::withMessages([
                     'status' => ["No valid fields provided for update. Received keys: [{$receivedKeys}]. Allowed: caption, hashtags, publish_at."],
                 ]);
            }

            \Illuminate\Support\Facades\Log::info('Updating post', ['id' => $post->id, 'data' => $data]);
            $updated = $this->schedulingRepository->update($post, $data);
            \Illuminate\Support\Facades\Log::info('Update result', ['updated' => $updated]);

            return $post->refresh();
        });
    }

    public function cancel(ScheduledPost $post): void
    {
        DB::transaction(function () use ($post) {
            $post = ScheduledPost::lockForUpdate()->find($post->id);
            
            if ($post->status !== 'pending') {
                 throw ValidationException::withMessages([
                    'status' => ['Cannot cancel a post that is already publishing or published.'],
                ]);
            }
            
            // Release video? 
            // Only if no other pending posts? For now simple logic:
            $post->video->update(['status' => 'ready']); // Revert to ready, not draft
            
            $this->schedulingRepository->delete($post);
        });
    }

    public function listUpcoming(User $user): LengthAwarePaginator
    {
        return $this->schedulingRepository->listUpcomingForUser($user->id);
    }
}
