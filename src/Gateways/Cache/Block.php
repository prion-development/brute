<?php

namespace Brute\Gateways\Cache;

use Brute\BlockInterface;
use Brute\Exception\BruteException;

class Block extends CacheAbstract implements BlockInterface
{
    public $type = 'brute_block:';

    /**
     * Check if the key is blocked
     *
     * @param $key
     *
     * @return bool
     * @throws \Brute\Exception\BruteBlockedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function check($key): bool
    {
        if (!$this->exists($key)) {
            return true;
        }

        $this->exceptionBlocked($key);
    }

    /**
     * Check if the key is blocked, extend the block before throwing an exception
     *
     * @param $key
     *
     * @return bool
     * @throws \Brute\Exception\BruteBlockedException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function checkAndExtend($key): bool
    {
        if (!$this->exists($key)) {
            return true;
        }

        $this->extend($key);
        $this->exceptionBlocked($key);
    }

    /**
     * Extend the Block
     *
     * @param $key
     *
     * @return Block
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function extend($key): BlockInterface
    {
        $this->add($key);
        return $this;
    }

    /**
     * Block a Key
     *
     * @param $key
     *
     * @return Block
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function add($key): BlockInterface
    {
        $expire = (int) config('brute.block_ttl');
        $token = $this->token($key);
        $this->cache()->set($token, 1, $expire);
        return $this;
    }

    /**
     * Unblock a Key
     *
     * @param $key
     *
     * @return Block
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function delete($key): BlockInterface
    {
        $attempt = $this->token(self::TAG_ATTEMPT . $key);
        $block = $this->token(self::TAG_BLOCK . $key);

        $this->cache()->forget($attempt);
        $this->cache()->forget($block);
        return $this;
    }
}
