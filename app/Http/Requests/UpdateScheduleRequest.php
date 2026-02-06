<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
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
            'publish_at' => ['sometimes', 'date', 'after:now'],
            'caption' => ['sometimes', 'nullable', 'string', 'max:2200'],
            'hashtags' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
