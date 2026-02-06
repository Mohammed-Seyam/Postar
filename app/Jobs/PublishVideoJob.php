<?php

namespace App\Jobs;

use App\Interfaces\PlatformInterface;
use App\Models\PlatformAccount;
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

    public $tries = 3;
    public $backoff = 30;

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
            // Retrieve User's Platform Account
            $video = $this->scheduledPost->video;
            if (!$video) {
                 throw new \Exception("Video not found for scheduled post.");
            }
            
            $platformAccount = PlatformAccount::where('user_id', $video->user_id)
                ->where('platform', $this->scheduledPost->platform)
                ->first();
                
            if (!$platformAccount) {
                throw new \Exception("Platform account not found for user {$video->user_id} and platform {$this->scheduledPost->platform}");
            }

            // Check Token Expiry
            if ($platformAccount->token_expires_at && $platformAccount->token_expires_at->isPast()) {
                 Log::info("Token expired for account {$platformAccount->id}. Refreshing...");
                 $this->refreshAccessToken($platformAccount);
            }

            $platformService = $this->getPlatformService($this->scheduledPost->platform);


            
            $externalId = $platformService->publish($this->scheduledPost, $platformAccount->access_token);

            $this->scheduledPost->update([
                'status' => 'published',
                'external_id' => $externalId,
            ]);
            


            Log::info("Successfully published post: {$this->scheduledPost->id}");

        } catch (\Exception $e) {
            Log::error("Failed to publish post {$this->scheduledPost->id}: " . $e->getMessage());
            
            $this->scheduledPost->update(['status' => 'failed']);

            
            throw $e;
        }
    }

    private function getPlatformService(string $platform): PlatformInterface
    {
        return match ($platform) {
            'tiktok' => new TikTokService(),
            default => throw new \Exception("Platform {$platform} not supported"),
        };
    }

    private function refreshAccessToken(PlatformAccount $account): void
    {
        // Mock refresh logic
        // In a real app, this would use the refresh_token to get a new access token from the platform API
        
        $newAccessToken = "mock_refreshed_token_" . uniqid();
        
        $account->update([
            'access_token' => $newAccessToken,
            'token_expires_at' => now()->addDays(30), // Extended validity
        ]);

        Log::info("Token refreshed for account {$account->id}");
    }
}
