<?php

namespace Brute;

use Brute\Exception\BruteException;

class Block extends BruteBase
{

    public $type = 'brute_block:';


    /**
     * Check if the key is blocked
     *
     * @param $key
     *
     * @return bool
     * @throws BruteException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function check($key): bool
    {
        $key = $this->key($key);
        if (!$this->cache()->has($key)) {
            return true;
        }

        throw new BruteException('The key is locked: ' . $key);
    }


    /**
     * Attempt to Block a Key
     *
     * @param $key
     * @param string $maxAttempts
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function attempt($key, $maxAttempts=''): bool
    {
        $attempts = app(\Brute\Attempt::class)->reset($this->prefix, $this->item)->add($key);
        $maxAttempts = $maxAttempts ?? config('brute.attempts');

        if ($attempts >= $maxAttempts) {
            return $this->block($key);
        }

        return false;
    }


    /**
     * Block a Key
     *
     * @param $key
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function block($key): bool
    {
        $expire = (int) config('prion.block_ttl');
        $key = $this->filter($key);
        $this->cache()->set($key, 1, $expire);

        return true;
    }


    /**
     * Unblock a Key
     *
     * @param $key
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function remove($key): void
    {
        $attempt = $this->key($this->attempt . $key);
        $block = $this->key($this->block . $key);

        $this->cache()->forget($attempt);
        $this->cache()->forget($block);
    }
}