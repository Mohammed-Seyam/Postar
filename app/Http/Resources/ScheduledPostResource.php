<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VideoResource;

class ScheduledPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'video_id' => $this->video_id,
            'platform' => $this->platform,
            'publish_at' => $this->publish_at,
            'caption' => $this->caption,
            'hashtags' => $this->hashtags,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'video' => new VideoResource($this->whenLoaded('video')),
        ];
    }
}
