<?php

namespace App\Http\Middleware;

use App\Support\RuntimeBridge\BridgeRequestSigner;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyRuntimeBridgeSignature
{
    public function __construct(
        protected BridgeRequestSigner $signer,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('runtime_bridge.enabled', true)) {
            return $this->error('Runtime bridge is disabled.', 404);
        }

        $secret = config('runtime_bridge.shared_secret');

        if (blank($secret)) {
            return $this->error('Runtime bridge is not configured.', 503);
        }

        if (! $this->signer->hasValidSignature($request, $secret)) {
            return $this->error('Invalid runtime bridge signature.', 401);
        }

        return $next($request);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return response()->json([
            'error' => $message,
        ], $status);
    }
}
