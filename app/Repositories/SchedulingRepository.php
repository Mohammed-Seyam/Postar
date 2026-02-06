<?php

namespace App\Repositories;

use App\Models\ScheduledPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SchedulingRepository
{
    public function create(array $data): ScheduledPost
    {
        return ScheduledPost::create($data);
    }

    public function find(string $id): ?ScheduledPost
    {
        return ScheduledPost::find($id);
    }

    public function update(ScheduledPost $post, array $data): bool
    {
        return $post->update($data);
    }

    public function delete(ScheduledPost $post): bool
    {
        return $post->delete();
    }

    public function listUpcomingForUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return ScheduledPost::whereHas('video', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'pending')
            ->orderBy('publish_at')
            ->paginate($perPage);
    }

    public function findDuePosts(): Collection
    {
        return ScheduledPost::where('status', 'pending')
            ->where('publish_at', '<=', now())
            ->get();
    }
}
