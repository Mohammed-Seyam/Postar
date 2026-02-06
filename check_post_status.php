<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ScheduledPost;

$id = '019c31ff-923b-7384-8c1b-87134bdd06ef';


// Check ScheduledPost
$post = ScheduledPost::withTrashed()->find($id);
if ($post) {
    echo "\n[ScheduledPost] FOUND. Deleted At: " . ($post->deleted_at ?? 'NULL') . "\n";
} else {
    echo "\n[ScheduledPost] NOT FOUND.\n";
}

// Broaden search to other models
use App\Models\Video;
use App\Models\User;

// Check Video
$video = Video::withTrashed()->find($id);
if ($video) {
    echo "[Video] FOUND. Deleted At: " . ($video->deleted_at ?? 'NULL') . "\n";
} else {
    echo "[Video] NOT FOUND.\n";
}

// Check User
$user = User::query()->where('id', $id)->first(); // User doesn't use SoftDeletes currently (schema check)
if ($user) {
    echo "[User] FOUND.\n";
} else {
    echo "[User] NOT FOUND.\n";
}

