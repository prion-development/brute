<?php

namespace Brute\Gateways\Cache;

use \Illuminate\Cache\TaggedCache;

abstract class CacheAbstract
{
    public $type = '_brute_attempt_';

    const TAG_BLOCK = 'brute_block:';
    const TAG_ATTEMPT = 'brute_attempt:';

    public $cache;

    public $prefix = '';
    public $item = '';

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
     * @return mixed
     */
    protected function exists($key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * Run $key through a filter
     *
     * @param string|null $key
     *
     * @return string
     */
    protected function filter(?string $key): string
    {
        $find = [
            self::TAG_BLOCK,
            self::TAG_ATTEMPT,
        ];

        $key = str_replace($find, '', $key);
        $key = str_replace("::::", "::", $key);
        $key = $this->type . $key;
        $key = $this->item . $key;
        $key = $this->prefix . $key;

        return trim($key);
    }

    /**
     * Set the Prefix
     *
     * @param $prefix
     * @return mixed
     */
    public function prefix($prefix)
    {
        $this->prefix = $prefix . "::";
        $this->prefix = str_replace("::::", "::", $this->prefix);
        return $this;
    }


    /**
     * Set the Item
     *
     * @param $app
     * @return mixed
     */
    public function item($item)
    {
        $this->item = $item . "::";
        $this->item = str_replace("::::", "::", $this->item);
        return $this;
    }


    /**
     * Reset the Brute Prefix and Item
     *
     * @param $prefix
     * @param $item
     * @return $this
     */
    public function reset($prefix, $item)
    {
        $this->prefix($prefix);
        $this->item($item);

        return $this;
    }
}