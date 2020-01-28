<?php

namespace Brute;

use Carbon\Carbon;

class Attempt extends BruteBase
{
    public $type = 'brute_attempt:';

    /**
     * Remove an Attempt
     *
     * @param $key
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function remove($key): void
    {
        $key = $this->filter($key);
        $this->cache()->decrement($key);
    }

    /**
     * Remove All Attempts
     *
     * @param $key
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function removeAll($key): void
    {
        $key = $this->filter($key);
        $this->cache()->forget($key);
    }


    /**
     * Add An Attempt
     *
     * @param $key
     * @param string $maxAttempts
     *
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function add($key, $maxAttempts=''): int
    {
        $origialKey = $key;
        $key = $this->filter($key);
        $attempts = $this->cache()->get($key) ?? [];
        $attempts[] = Carbon::now('UTC')->toDateTimeString();

        $maxAttempts = $maxAttempts ?? config('brute.attempts');
        if (count($attempts) > $maxAttempts) {
            app(Block::class)->block($origialKey);
        }

        $expire = (int) config('brute.block_ttl');
        $this->cache()->set($key, $attempts, $expire);

        return $attempts;
    }

    public function total($key): int
    {

    }

    /**
     * Pull All Attempts from Cache
     *
     * @param $key
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function attempts($key): array
    {
        $origialKey = $key;
        $key = $this->filter($key);
        $attempts = $this->cache()->get($key) ?? [];
        $now = Carbon::now('UTC');

        foreach ($attempts as $key => $attempt) {
            $attempt = Carbon::parse($attempt, 'UTC');
        }
    }
}