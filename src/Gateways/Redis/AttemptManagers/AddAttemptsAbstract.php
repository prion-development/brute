<?php

namespace Brute\Gateways\Redis\AttemptManagers;

use Brute\Gateways\Redis\AttemptResource;

abstract class AddAttemptsAbstract
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var AttemptResource
     */
    protected $attemptResource;

    public function __construct(AttemptResource $attemptResource)
    {
        $this->attemptResource = $attemptResource;
    }

    protected function redis()
    {
        if (!empty($this->redis)) {
            return $this->redis;
        }

        $redisConnection = config('');
        return app('redis')->connection($redisConnection);
    }

    public function setTtl(int $ttl): AddAttemptsInterface
    {
        $this->ttl = $ttl;
        return $this;
    }

    public function ttl(): int
    {
        return (int) $this->ttl > 0 ? $this->ttl : config('brute.attempt_ttl');
    }
}
