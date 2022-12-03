<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class RateLimitedMail
{
    /**
     * @var string
     */
    protected string $key;

    /**
     * @var int
     */
    protected int $maxAttempts;

    /**
     * @var int
     */
    protected int $decaySeconds;

    /**
     * @var int
     */
    protected int $releaseSeconds;

    public function __construct(string $key, int $maxAttempts, int $decaySeconds, int $releaseSeconds)
    {
        $this->key = $key;
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
        $this->releaseSeconds = $releaseSeconds;
    }

    /**
     * @param  mixed  $job
     * @param  \Closure  $next
     * @return void
     */
    public function handle($job, Closure $next): void
    {
        Redis::throttle($this->key)
            ->block(0)
            ->allow($this->maxAttempts)
            ->every($this->decaySeconds)
            ->then(fn () => $next($job), fn () => $this->releaseJob($job));
    }

    /**
     * @param  mixed  $job
     * @return void
     */
    protected function releaseJob($job): void
    {
        $job->release($this->releaseSeconds);
    }
}
