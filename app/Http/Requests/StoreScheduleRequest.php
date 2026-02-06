<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video_id' => ['required', 'exists:videos,id', 'unique:scheduled_posts,video_id'],
            'platform' => ['required', 'string', 'in:tiktok,instagram,youtube'],
            'publish_at' => ['required', 'date', 'after:now'],
            'caption' => ['nullable', 'string', 'max:2200'],
            'hashtags' => ['nullable', 'string'],
        ];
    }
}
