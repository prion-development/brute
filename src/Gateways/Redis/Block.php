<?php

namespace Brute\Gateways\Redis;

use Brute\BlockInterface;

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
        $expire = (int) config('brute.block_ttl', 15);
        $token = $this->token($key);
        $this->redis()->set($token, 1);
        $this->redis()->expire($token, $expire);
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

        $this->redis()->forget($attempt);
        $this->redis()->del($block);
        return $this;
    }
}
