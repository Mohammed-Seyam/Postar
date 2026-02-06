<?php

namespace App\Repositories;

use App\Models\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VideoRepository
{
    public function create(array $data): Video
    {
        return Video::create($data);
    }

    public function find(string $id): ?Video
    {
        return Video::find($id);
    }

    public function update(Video $video, array $data): bool
    {
        return $video->update($data);
    }

    public function delete(Video $video): bool
    {
        return $video->delete();
    }

    public function listForUser(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Video::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
