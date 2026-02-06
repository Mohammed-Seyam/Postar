
dump([
    'users' => \App\Models\User::count(),
    'subscriptions' => \App\Models\Subscription::count(),
    'videos' => \App\Models\Video::count(),
    'scheduled_posts' => \App\Models\ScheduledPost::count(),
]);
