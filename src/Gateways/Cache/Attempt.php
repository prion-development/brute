<?php

namespace Brute\Gateways\Cache;

use Brute\AttemptInterface;
use Carbon\Carbon;

class Attempt extends CacheAbstract implements AttemptInterface
{
    public $type = 'brute_attempt:';

    /**
     * Add An Attempt
     *
     * @param string $key
     * @param int|null $maxAttempts
     *
     * @return AttemptInterface
     * @throws \Brute\Exception\BruteBlockedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function add(string $key, ?int $maxAttempts = null): AttemptInterface
    {
        $maxAttempts = $maxAttempts ?? config('brute.max_attempts');
        $attempts = $this->all($key);
        $attempts[] = Carbon::now('UTC')->toDateTimeString();

        $expire = (int)config('brute.attempt_ttl');
        $this->cache()->set($this->key($key), $attempts, $expire);

        $this->addBlock($key, $attempts, $maxAttempts);
        return $this;
    }

    /**
     * Remove an Attempt
     *
     * This method removes the oldest valid attempt
     *
     * @param string $key
     *
     * @return AttemptInterface
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete(string $key): AttemptInterface
    {
        $attempts = $this->all($key);
        rsort($attempts);
        array_pop($attempts);

        $expire = (int) config('brute.attempt_ttl');
        $key = $this->key($key);
        $this->cache()->set($key, $attempts, $expire);
        return $this;
    }

    /**
     * Do we need to block the value?
     *
     * @param string $key
     * @param array $attempts
     * @param int $maxAttempts
     *
     * @throws \Brute\Exception\BruteBlockedException
     */
    private function addBlock(string $key, array $attempts, ?int $maxAttempts): void
    {
        $maxAttempts = $maxAttempts ?? config('brute.max_attempts');
        if (count($attempts) >= $maxAttempts) {
            app(Block::class)->tag($this->tags)->add($key);
            $this->exceptionBlocked($key);
        }
    }

    /**
     * Calculate the Total Number of Attempts
     *
     * @param $key
     *
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function total($key): int
    {
        $key = $this->key($key);
        $attempts = $this->cache()->get($key) ?? [];
        $this->removeInvalidTimestamps($attempts);
        return count($attempts);
    }

    /**
     * Pull All Attempts from Cache
     *
     * @param $key
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function all($key): array
    {
        $key = $this->key($key);
        $attempts = $this->cache()->get($key) ?? [];
        return $this->removeInvalidTimestamps($attempts);
    }

    /**
     * Remove Invalid Timestamps
     *
     * @param array $timestamps
     *
     * @return array
     */
    public function removeInvalidTimestamps(array $timestamps = []): array
    {
        foreach ($timestamps as $key => $timestamp) {
            $timestamp = $this->toCarbon($timestamp);
            if ($this->expireTimestamp($timestamp)) {
                unset($timestamps[$key]);
            }
        }

        return $timestamps;
    }

    /**
     * Check if a Timestamp is Invalid
     *
     * @param Carbon $timestamp
     *
     * @return bool
     */
    public function expireTimestamp(Carbon $timestamp): bool
    {
        $now = Carbon::now('UTC');
        return $timestamp->addMinutes(config('brute.attempt_ttl')) <= $now;
    }
}
