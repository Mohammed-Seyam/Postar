<?php


require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ScheduledPost;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Ensure we have a user and video
$user = User::first();
if (!$user) {
    $user = User::factory()->create();
}
Auth::login($user);

// Create a dummy video if needed
$video = Video::first();
if (!$video) {
    $video = Video::create([
        'user_id' => $user->id,
        'title' => 'Test Video',
        'status' => 'draft',
        'path' => 'videos/test.mp4',
        'platform' => 'youtube',
    ]);
}


// Clean up existing posts for this video to avoid unique constraint violations
ScheduledPost::where('video_id', $video->id)->forceDelete();

// Create a scheduled post
$post = ScheduledPost::create([
    'video_id' => $video->id,
    'platform' => 'youtube',
    'publish_at' => now()->addDay(),
    'caption' => 'Original Caption',
    'status' => 'pending',
]);

echo "Created post ID: " . $post->id . "\n";
echo "Original Caption: " . $post->caption . "\n";

// Emulate request with invalid/unknown keys
$data = [
    'invalid_key' => 'This should be ignored',
    // 'caption' => 'New Caption', // commented out to simulate missing valid fields
];

echo "Attempting update with invalid keys...\n";

// Call the service directly to simulate the controller/request flow, 
// or simpler: use the repository directly if we want to show Eloquent behavior,
// BUT the issue is likely in the Service receiving filtered data from FormRequest.
// Let's simulate what the Controller does: calls Service with valid data.
// If FormRequest filters it, Service receives empty array.

$service = app(\App\Services\SchedulingService::class);

try {
    $updatedPost = $service->update($post, $data); 
    // In the real app, $data comes from $request->validated(). 
    // If $request->validated() is empty (because keys are invalid), $data is [].
    
    // Let's explicitly pass empty array to simulate $request->validated() result on invalid input
    $updatedPost = $service->update($post, []);
    
    echo "Update call completed.\n";
    echo "New Caption: " . $updatedPost->caption . "\n";
    
    if ($updatedPost->caption === 'Original Caption') {
        echo "FAIL: Post was not updated, but no error was thrown (200 OK equivalent).\n";
    } else {
        echo "SUCCESS: Post was updated (unexpected for empty data).\n";
    }
} catch (\Exception $e) {
    echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
    if ($e instanceof \Illuminate\Validation\ValidationException) {
        echo "Validation Exception Messages: " . json_encode($e->errors()) . "\n";
    }
}

echo "\nTest 2: Attempting update with VALID keys...\n";
try {
    $validData = ['caption' => 'Updated Caption Success'];
    $updatedPost = $service->update($post, $validData);
    
    echo "Update call completed.\n";
    echo "New Caption: " . $updatedPost->caption . "\n";
    
    if ($updatedPost->caption === 'Updated Caption Success') {
        echo "SUCCESS: Post was updated with valid data.\n";
    } else {
        echo "FAIL: Post was NOT updated with valid data.\n";
    }

} catch (\Exception $e) {
    echo "CAUGHT UNEXPECTED EXCEPTION: " . $e->getMessage() . "\n";
}


echo "\nTest 3: Attempting update with mixed valid/invalid keys...\n";
try {
    // 'platform' is NOT in UpdateScheduleRequest, so it should be stripped
    // 'hashtags' IS in rules, so it should be kept
    $mixedData = [
        'platform' => 'instagram',
        'hashtags' => '#newtag'
    ];
    
    // We need to simulate Validator behavior here roughly, or just test the Service with what we EXPECT validated() to return.
    // If 'platform' is not in rules, validated() won't have it.
    // If 'hashtags' is in rules, validated() WILL have it.
    
    // So if the user says they sent 2 fields and got error, implies BOTH were stripped.
    // Maybe they sent 'platform' and 'video_id'?
    
    $simulatedValidatedData = ['hashtags' => '#newtag']; // platform stripped
    $updatedPost = $service->update($post, $simulatedValidatedData);
    
    echo "Test 3 Result: Success (hashtags updated).\n";

} catch (\Exception $e) {
    echo "Test 3 Result: FAILED with " . $e->getMessage() . "\n";
}

echo "\nTest 4: Attempting update with ONLY fields NOT in rules...\n";
try {
    $invalidFields = ['platform' => 'instagram', 'video_id' => 'some-uuid'];
    // validated() would be empty
    $service->update($post, []);
    echo "Test 4 Result: Should not reach here.\n";
} catch (\Exception $e) {
    echo "Test 4 Result: CAUGHT EXPECTED " . $e->getMessage() . "\n";
}

// Cleanup
$post->delete();
