<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

trait ThrottlesAttempts
{
    protected function validateAttempts(Request $request): void
    {
        $over = $this->limiter()->tooManyAttempts($this->throttleKey($request), $this->maxAttempts());
        if ($over) {
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));
            $minutes = ceil($seconds / 60);
            throw new TooManyRequestsHttpException($seconds, __('auth.throttle', ['minutes' => $minutes]));
        }
    }

    protected function clearAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    protected function incrementAttempts(Request $request): void
    {
        $this->limiter()->hit($this->throttleKey($request), $this->decayMinutes() * 60);
        $this->validateAttempts($request);
    }

    protected function throttleKeyPrefix(): string
    {
        return 'login';
    }

    protected function throttleKey(Request $request): string
    {
        return $this->throttleKeyPrefix() . '|' . Str::lower($request->input($this->username())) . '|' . $request->ip();
    }

    protected function username(): string
    {
        return 'username';
    }

    protected function maxAttempts(): int
    {
        return 5;
    }

    protected function decayMinutes(): int
    {
        return 10;
    }

    protected function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }
}
