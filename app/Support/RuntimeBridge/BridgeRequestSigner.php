<?php

namespace App\Support\RuntimeBridge;

use Illuminate\Http\Request;

class BridgeRequestSigner
{
    public function signatureFor(Request $request, string $consumer, string $timestamp, string $secret): string
    {
        return hash_hmac('sha256', $this->payload($request, $consumer, $timestamp), $secret);
    }

    public function hasValidSignature(Request $request, ?string $secret): bool
    {
        if (! config('runtime_bridge.enabled', true) || blank($secret)) {
            return false;
        }

        $consumer = (string) $request->header('X-Bridge-Consumer', '');
        $timestamp = (string) $request->header('X-Bridge-Timestamp', '');
        $provided = (string) $request->header('X-Bridge-Signature', '');

        if ($consumer === '' || $timestamp === '' || $provided === '') {
            return false;
        }

        if ($consumer !== config('runtime_bridge.consumer', 'codex')) {
            return false;
        }

        if (! ctype_digit($timestamp)) {
            return false;
        }

        $maxSkew = (int) config('runtime_bridge.max_skew_seconds', 300);

        if (abs(now()->timestamp - (int) $timestamp) > $maxSkew) {
            return false;
        }

        $expected = $this->signatureFor($request, $consumer, $timestamp, $secret);

        return hash_equals($expected, $provided);
    }

    private function payload(Request $request, string $consumer, string $timestamp): string
    {
        $path = '/'.ltrim($request->path(), '/');
        $query = $request->getQueryString();
        $uri = $query ? $path.'?'.$query : $path;

        return implode("\n", [
            strtoupper($request->method()),
            $uri,
            $consumer,
            $timestamp,
        ]);
    }
}
