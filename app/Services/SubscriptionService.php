<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class SubscriptionService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createCheckoutSession(User $user, string $priceId): string
    {
        // Mock implementation for MVP
        Log::info("Creating checkout session for user {$user->id} for price {$priceId}");
        
        return 'https://checkout.stripe.com/mock-session';
    }

    public function handleWebhook(array $payload): void
    {
        // Handle Stripe webhooks (subscription.created, etc.)
        Log::info('Stripe webhook received', $payload);
    }

    public function isActive(User $user): bool
    {
        // For MVP, assume everyone is active or check DB
        $subscription = $user->subscription;
        return $subscription && $subscription->status === 'active';
    }
}
