<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (! $user || ! $user->subscription || $user->subscription->status !== 'active') {
            // For MVP, we might want to allow some access, but per requirements:
            // "Middleware: check active subscription."
            // We'll return 402 Payment Required
            return response()->json(['message' => 'Active subscription required.'], 402);
        }

        return $next($request);
    }
}
