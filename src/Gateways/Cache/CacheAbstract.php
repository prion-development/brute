<?php

namespace Brute\Gateways\Cache;

use Brute\Exception\BruteBlockedException;
use Carbon\Carbon;
use \Illuminate\Cache\TaggedCache;

abstract class CacheAbstract
{
    public $type = 'brute_attempt:';

    const TAG_BLOCK = 'brute_block:';
    const TAG_ATTEMPT = 'brute_attempt:';

    public $cache;

    protected $tags = [];

    /**
     * Load the Cache Method
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function cache(): TaggedCache
    {
        if ($this->cache) {
            return $this->cache;
        }

        $this->setCache();
        return $this->cache;
    }

    /**
     * Set the Cache
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return TaggedCache
     */
    protected function setCache(): TaggedCache
    {
        $tag = config('brute.cache.tag');
        $this->cache = app('cache')->tags($tag);
        return $this->cache;
    }

    /**
     * Check if a Key Exists
     *
     * @param $key
     *
     * @return bool
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function exists(string $key): bool
    {
        $token = $this->token($key);
        return $this->cache()->has($token);
    }

    /**
     * Create a key $key
     *
     * {type}::{tag1}::{tag2}::{$value}
     *
     * @param string|null $key
     *
     * @return string
     */
    protected function token(string $value): string
    {
        $token = $this->prefix();
        $token .= $this->cleanValue($value);
        return $token;
    }

    /**
     * Filter the Value we Store
     *
     * @param string $value
     *
     * @return string
     */
    protected function cleanValue(string $value): string
    {
        $find = [
            self::TAG_BLOCK,
            self::TAG_ATTEMPT,
        ];

        $value = trim($value);
        $value = str_replace($find, '', $value);
        $value = bruteKeyFilter($value);

        return $value;
    }

    public function exceptionBlocked($key)
    {
        throw new BruteBlockedException('The tagged key ('. implode(', ', $this->tags) .') is blocked: ' . $key);
    }

    /**
     * Use the Brute Class Type and User Tags to Build the Prefix
     *
     * @return string
     */
    protected function prefix(): string
    {
        $prefix = $this->type;
        foreach ($this->tags as $tag) {
            $prefix .= $tag;
        }

        return $prefix;
    }

    /**
     * Set the Prefix
     *
     * Use a prefix to define how the brute key is used. Examples:
     *  - Login
     *  - Password Reset
     *
     * @param $tag
     * @return mixed
     */
    public function tag($tags)
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        foreach ($tags as $tag) {
            $tag = trim($tag) . "::";
            $tag = bruteKeyFilter($tag);
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function tags()
    {
        return $this->tags;
    }

    /**
     * Reset Tags
     *
     * @return $this
     */
    public function tagReset()
    {
        $this->tags = [];
        return $this;
    }

    /**
     * Reset the Attempts and Blocks
     *
     * @param string $key
     *
     * @return $this
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function reset(string $key)
    {
        $token = $this->token($key);
        $this->cache()->forget($token);
        return $this;
    }

    /**
     * Make sure the timestamp is a Carbon timestamp
     *
     * @param $timestamp
     *
     * @return Carbon
     */
    public function toCarbon($timestamp)
    {
        return ($timestamp instanceof Carbon) ? $timestamp : Carbon::parse($timestamp, 'UTC');
    }
}