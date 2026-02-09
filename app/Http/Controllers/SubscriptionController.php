<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService)
    {
    }

    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'plan_slug' => 'required|string|exists:plans,slug',
        ]);
        
        $url = $this->subscriptionService->createCheckoutSession(
            $request->user(), 
            $request->input('plan_slug')
        );
        
        return response()->json(['url' => $url]);
    }

    public function webhook(Request $request): JsonResponse
    {
        $this->subscriptionService->handleWebhook($request->all());
        
        return response()->json(['status' => 'received']);
    }
}
