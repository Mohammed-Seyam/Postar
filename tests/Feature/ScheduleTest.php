<?php

namespace Tests\Feature;

use App\Models\ScheduledPost;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    // Use RefreshDatabase to ensure clean state
    // But since user is using local serve, this might wipe their DB if they run it locally.
    // I will assume standard testing environment.
    use RefreshDatabase; 

    public function test_cannot_schedule_same_video_twice()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $video = Video::factory()->create(['user_id' => $user->id, 'status' => 'draft']);

        // First Schedule
        $response = $this->postJson('/api/auth/schedule', [
            'video_id' => $video->id,
            'platform' => 'tiktok',
            'publish_at' => now()->addDay()->toDateTimeString(),
            'caption' => 'First schedule',
        ]);

        $response->assertStatus(201);

        // Second Schedule (Duplicate)
        $response2 = $this->postJson('/api/auth/schedule', [
            'video_id' => $video->id,
            'platform' => 'instagram', // Even if different platform, if we enforce unique video_id globally
            'publish_at' => now()->addDay()->toDateTimeString(),
            'caption' => 'Second schedule',
        ]);

        // Expect 422 Validation Error
        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors(['video_id']);
    }

    public function test_can_update_scheduled_post()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $video = Video::factory()->create(['user_id' => $user->id, 'status' => 'draft']);
        
        $post = ScheduledPost::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'video_id' => $video->id,
            'platform' => 'tiktok',
            'publish_at' => now()->addDay(),
            'caption' => 'Original Caption',
            'status' => 'pending'
        ]);

        $updateData = [
            'caption' => 'Updated Caption',
        ];

        $response = $this->putJson("/api/auth/schedule/{$post->id}", $updateData);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('scheduled_posts', [
            'id' => $post->id,
            'caption' => 'Updated Caption'
        ]);
        
        $response->assertJsonPath('data.caption', 'Updated Caption');
    }
}
