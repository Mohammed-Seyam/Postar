<?php

namespace App\Services\Platforms;

use App\Interfaces\PlatformInterface;
use App\Models\ScheduledPost;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokService implements PlatformInterface
{
    public function publish(ScheduledPost $post, string $accessToken): string
    {
        // Mock TikTok API implementation
        // In reality, this would exchange tokens, upload video, and create post
        
        Log::info("Publishing to TikTok: {$post->id}");
        
        // Simulate API call
        // $response = Http::withToken($token)->post('https://open.tiktokapis.com/v2/post/publish/video/init/', ...);
        
        // Return a mock external ID
        return 'tiktok_' . uniqid();
    }
}
