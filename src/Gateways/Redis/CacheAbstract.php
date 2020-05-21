<?php

namespace Brute\Gateways\Redis;

use Brute\Exception\BruteBlockedException;
use Carbon\Carbon;
use Illuminate\Redis\Connections\PredisConnection;

abstract class CacheAbstract
{
    /**
     * @var string
     */
    public $type = 'brute_attempt:';

    const TAG_BLOCK = 'brute_block:';
    const TAG_ATTEMPT = 'brute_attempt:';

    /**
     * @var null|string
     */
    private $key = null;

    /**
     * @var PredisConnection
     */
    private $redis;

    /**
     * @var array
     */
    protected $tags = [];

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
        return $this->redis()->exists($token);
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
        $key = $this->prefix();
        $key .= $this->cleanValue($value);
        return $key;
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
     * Set the User Defined Key
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
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

    protected function redis(): PredisConnection
    {
        if (!empty($this->redis)) {
            return $this->redis;
        }

        $redisConnection = config('');
        return app('redis')->connection($redisConnection);
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
        $this->redis()->del($token);
        return $this;
    }

    /**
     * Make sure the timestamp is a Carbon timestamp
     *
     * @param string|Carbon $timestamp
     *
     * @return Carbon
     * @throws \Exception
     */
    public function toCarbon($timestamp): Carbon
    {
        return ($timestamp instanceof Carbon) ? $timestamp : Carbon::parse($timestamp, 'UTC');
    }
}