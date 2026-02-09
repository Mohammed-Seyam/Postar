<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessVideoJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public \App\Models\Video $video)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mock processing logic
        // In reality, this would use FFMpeg to get duration and transcode
        
        $this->video->update([
            'status' => 'ready',
            'duration' => 60, // Mock duration
        ]);
        
        \Illuminate\Support\Facades\Log::info("Video processed: {$this->video->id}");
    }
}
