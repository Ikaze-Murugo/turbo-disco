<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->handleFailedLogin();
            return;
        }

        $this->handleSuccessfulLogin();
    }

    /**
     * Handle failed login attempt.
     */
    protected function handleFailedLogin(): void
    {
        // Increment rate limiting counters
        $this->incrementRateLimitCounters();
        
        // Log failed login attempt
        $this->logFailedLogin();
        
        // Clear any existing rate limit for successful login
        RateLimiter::clear($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle successful login.
     */
    protected function handleSuccessfulLogin(): void
    {
        // Clear all rate limiting counters on successful login
        $this->clearAllRateLimitCounters();
        
        // Log successful login
        $this->logSuccessfulLogin();
        
        // Regenerate session for security
        request()->session()->regenerate();
    }

    /**
     * Increment rate limiting counters for failed login.
     */
    protected function incrementRateLimitCounters(): void
    {
        // IP-based rate limiting (10 attempts per 15 minutes)
        $ipKey = 'login_attempts_ip:' . $this->ip();
        RateLimiter::hit($ipKey, 900); // 15 minutes

        // Email-based rate limiting (5 attempts per 15 minutes)
        $emailKey = 'login_attempts_email:' . strtolower($this->input('email'));
        RateLimiter::hit($emailKey, 900); // 15 minutes

        // Combined rate limiting (3 attempts per 15 minutes)
        RateLimiter::hit($this->throttleKey(), 900); // 15 minutes
    }

    /**
     * Clear all rate limiting counters.
     */
    protected function clearAllRateLimitCounters(): void
    {
        $ipKey = 'login_attempts_ip:' . $this->ip();
        $emailKey = 'login_attempts_email:' . strtolower($this->input('email'));
        
        RateLimiter::clear($ipKey);
        RateLimiter::clear($emailKey);
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // Check IP-based rate limiting
        $ipKey = 'login_attempts_ip:' . $this->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $this->handleRateLimitExceeded($ipKey, 'IP address');
        }

        // Check email-based rate limiting
        $emailKey = 'login_attempts_email:' . strtolower($this->input('email'));
        if (RateLimiter::tooManyAttempts($emailKey, 5)) {
            $this->handleRateLimitExceeded($emailKey, 'email address');
        }

        // Check combined rate limiting
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            $this->handleRateLimitExceeded($this->throttleKey(), 'account');
        }
    }

    /**
     * Handle rate limit exceeded.
     */
    protected function handleRateLimitExceeded(string $key, string $type): void
    {
        event(new Lockout($this));
        
        $seconds = RateLimiter::availableIn($key);
        $minutes = ceil($seconds / 60);
        
        $this->logRateLimitExceeded($type, $seconds);

        throw ValidationException::withMessages([
            'email' => "Too many login attempts for this {$type}. Please try again in {$minutes} minutes.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $email = strtolower($this->input('email', 'unknown'));
        $ip = $this->ip();
        $userAgent = $this->userAgent();
        
        return 'login_attempts_combined:' . md5($email . '|' . $ip . '|' . substr($userAgent, 0, 50));
    }

    /**
     * Log failed login attempt.
     */
    protected function logFailedLogin(): void
    {
        Log::warning('Failed login attempt', [
            'ip' => $this->ip(),
            'email' => $this->input('email'),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
            'url' => $this->fullUrl()
        ]);
    }

    /**
     * Log successful login.
     */
    protected function logSuccessfulLogin(): void
    {
        Log::info('Successful login', [
            'ip' => $this->ip(),
            'email' => $this->input('email'),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
            'url' => $this->fullUrl()
        ]);
    }

    /**
     * Log rate limit exceeded.
     */
    protected function logRateLimitExceeded(string $type, int $seconds): void
    {
        Log::warning('Login rate limit exceeded', [
            'ip' => $this->ip(),
            'email' => $this->input('email'),
            'user_agent' => $this->userAgent(),
            'rate_limit_type' => $type,
            'retry_after_seconds' => $seconds,
            'timestamp' => now(),
            'url' => $this->fullUrl()
        ]);
    }
}
