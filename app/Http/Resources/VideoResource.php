<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'file_path' => $this->file_path, // In a real app, generate a presigned URL here
            'thumbnail_path' => $this->thumbnail_path, // In a real app, generate a presigned URL here
            'duration' => $this->duration,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
