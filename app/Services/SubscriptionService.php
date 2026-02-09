<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Illuminate\Validation\ValidationException;

class SubscriptionService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createCheckoutSession(User $user, string $planSlug): string
    {
        try {
            // Mock implementation for MVP
            Log::info("Creating checkout session for user {$user->id} for plan {$planSlug}");
            
            $plan = Plan::where('slug', $planSlug)->first();
            
            if (!$plan) {
                 throw new \Exception("Invalid plan: {$planSlug}");
            }
            
            $price = $plan->price;
            
            // In real app, create session with dynamic price data or Stripe Price ID lookup
            
            return 'https://checkout.stripe.com/mock-session';
        } catch (\Exception $e) {
            Log::error("Subscription error: " . $e->getMessage());
            throw ValidationException::withMessages([
                'plan_slug' => ['Unable to initialize payment. Please try again later.'],
            ]);
        }
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
