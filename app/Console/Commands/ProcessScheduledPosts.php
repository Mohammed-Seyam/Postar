<?php

namespace App\Console\Commands;

use App\Jobs\PublishVideoJob;
use App\Repositories\SchedulingRepository;
use Illuminate\Console\Command;

class ProcessScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postar:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find pending scheduled posts and dispatch publishing jobs';

    /**
     * Execute the console command.
     */
    public function handle(SchedulingRepository $schedulingRepository)
    {
        $this->info('Checking for due scheduled posts...');

        $duePosts = $schedulingRepository->findDuePosts();

        if ($duePosts->isEmpty()) {
            $this->info('No pending posts due for publishing.');
            return;
        }

        $this->info("Found {$duePosts->count()} posts. Dispatching jobs...");

        foreach ($duePosts as $post) {
            $post->update(['status' => 'processing']);
            PublishVideoJob::dispatch($post);
            $this->info("Dispatched job for post ID: {$post->id}");
        }

        $this->info('Done.');
    }
}
