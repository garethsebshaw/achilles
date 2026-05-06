<?php

namespace App\Modules\Logging\Support;

class LogContextSanitizer
{
    public function sanitize(mixed $value): mixed
    {
        if (is_array($value)) {
            $sanitized = [];

            foreach ($value as $key => $item) {
                if ($this->shouldRedact((string) $key)) {
                    $sanitized[$key] = '[redacted]';
                    continue;
                }

                $sanitized[$key] = $this->sanitize($item);
            }

            return $sanitized;
        }

        if (is_object($value)) {
            return $this->sanitize((array) $value);
        }

        return $value;
    }

    protected function shouldRedact(string $key): bool
    {
        $redactedKeys = config('rsc_logging.redact_context_keys', []);
        $normalized = strtolower($key);

        foreach ($redactedKeys as $candidate) {
            if ($normalized === strtolower($candidate)) {
                return true;
            }
        }

        return false;
    }
}
