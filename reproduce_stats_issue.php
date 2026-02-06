<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Video;
use App\Models\ScheduledPost;
use Illuminate\Support\Facades\DB;

// Create a user
$user = User::factory()->create(['email' => 'stats_test_' . microtime(true) . '@example.com']);

echo "Created User ID: {$user->id}\n";

// 1. Check initial stats (should be 0)
$stats = DB::table('user_dashboard_stats_view')->where('user_id', $user->id)->first();
echo "Initial Total Videos: {$stats->total_videos} (Expected: 0)\n";

// 2. Create a Video
$video = Video::create([
    'user_id' => $user->id,
    'title' => 'Stats Test Video',
    'status' => 'ready',
    'path' => 'videos/test.mp4',
    'platform' => 'youtube',
]);

$stats = DB::table('user_dashboard_stats_view')->where('user_id', $user->id)->first();
echo "After Create Video: Total Videos: {$stats->total_videos} (Expected: 1)\n";

// 3. Create a ScheduledPost
$post = ScheduledPost::create([
    'video_id' => $video->id,
    'platform' => 'youtube',
    'publish_at' => now()->addDay(),
    'caption' => 'Stats Test Caption',
    'status' => 'pending',
]);

$stats = DB::table('user_dashboard_stats_view')->where('user_id', $user->id)->first();
echo "After Create Post: Upcoming Posts: {$stats->upcoming_posts} (Expected: 1)\n";

// 4. Soft Delete the ScheduledPost
$post->delete();
echo "Soft Deleted the Post.\n";

$stats = DB::table('user_dashboard_stats_view')->where('user_id', $user->id)->first();
echo "After Delete Post: Upcoming Posts: {$stats->upcoming_posts}\n";

if ($stats->upcoming_posts > 0) {
    echo "FAIL: Upcoming posts count is still {$stats->upcoming_posts} after soft delete.\n";
} else {
    echo "SUCCESS: Upcoming posts count is 0.\n";
}

// 5. Soft Delete the Video
$video->delete();
echo "Soft Deleted the Video.\n";

$stats = DB::table('user_dashboard_stats_view')->where('user_id', $user->id)->first();
echo "After Delete Video: Total Videos: {$stats->total_videos}\n";

if ($stats->total_videos > 0) {
    echo "FAIL: Total videos count is still {$stats->total_videos} after soft delete.\n";
} else {
    echo "SUCCESS: Total videos count is 0.\n";
}

// Cleanup
$user->videos()->forceDelete(); // cascades to posts usually, but explicit is fine
$user->delete();
