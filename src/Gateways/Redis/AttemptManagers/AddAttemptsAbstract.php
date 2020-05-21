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

    public function deleteAll(): AddAttemptsInterface
    {
        $tokens = $this->tokens();
        foreach ($tokens as $token) {
            $this->redis()->del($token);
        }

        return $this;
    }

    protected function redis()
    {
        if (!empty($this->redis)) {
            return $this->redis;
        }

        $redisConnection = config('');
        return app('redis')->connection($redisConnection);
    }

    public function setResource(AttemptResource $attemptResource): AddAttemptsInterface
    {
        $this->attemptResource = $attemptResource;
        return $this;
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
