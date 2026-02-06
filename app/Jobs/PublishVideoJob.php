<?php

namespace App\Jobs;

use App\Interfaces\PlatformInterface;
use App\Models\ScheduledPost;
use App\Services\Platforms\TikTokService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ScheduledPost $scheduledPost)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing Scheduled Post: {$this->scheduledPost->id}");

        $this->scheduledPost->update(['status' => 'publishing']);

        try {
            $platformService = $this->getPlatformService($this->scheduledPost->platform);
            
            $externalId = $platformService->publish($this->scheduledPost);

            $this->scheduledPost->update([
                'status' => 'published',
                // 'external_id' => $externalId, // If we had this column
            ]);
            
            $this->scheduledPost->video->update(['status' => 'published']);

            Log::info("Successfully published post: {$this->scheduledPost->id}");

        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->scheduledPost->id}: " . $e->getMessage());
            
            $this->scheduledPost->update(['status' => 'failed']);
            $this->scheduledPost->video->update(['status' => 'failed']);
            
            $this->fail($e);
        }
    }

    private function getPlatformService(string $platform): PlatformInterface
    {
        return match ($platform) {
            'tiktok' => new TikTokService(),
            default => throw new \Exception("Platform {$platform} not supported"),
        };
    }
}
