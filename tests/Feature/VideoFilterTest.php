<?php

namespace Tests\Feature;

use App\Models\ScheduledPost;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VideoFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_videos_by_platform()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create videos with scheduled posts for different platforms
        $youtubeVideo = Video::factory()->create(['user_id' => $user->id]);
        ScheduledPost::factory()->create([
            'video_id' => $youtubeVideo->id,
            'platform' => 'youtube',
        ]);

        $tiktokVideo = Video::factory()->create(['user_id' => $user->id]);
        ScheduledPost::factory()->create([
            'video_id' => $tiktokVideo->id,
            'platform' => 'tiktok',
        ]);

        $instagramVideo = Video::factory()->create(['user_id' => $user->id]);
        ScheduledPost::factory()->create([
            'video_id' => $instagramVideo->id,
            'platform' => 'instagram',
        ]);

        // Test filtering by youtube
        $response = $this->getJson('/api/videos?platform=youtube');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $youtubeVideo->id]);
        $response->assertJsonMissing(['id' => $tiktokVideo->id]);
        $response->assertJsonMissing(['id' => $instagramVideo->id]);

        // Test filtering by tiktok
        $response = $this->getJson('/api/videos?platform=tiktok');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tiktokVideo->id]);
        $response->assertJsonMissing(['id' => $youtubeVideo->id]);
    }
    
    public function test_returns_all_videos_when_no_platform_filter_provided()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Video::factory()->create(['user_id' => $user->id]);
        Video::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/videos');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }
}
