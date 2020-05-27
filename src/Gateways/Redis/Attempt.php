<?php

namespace Brute\Gateways\Redis;

use Brute\AttemptInterface;
use Brute\Gateways\Redis\AttemptManagers\Minute;
use Brute\Gateways\Redis\AttemptManagers\Hour;

class Attempt extends CacheAbstract implements AttemptInterface
{
    public $type = 'brute_attempt:';

    private $maxAttempts;

    private $attemptManagers = [
        Minute::class,
        Hour::class,
    ];

    public function __construct()
    {
        $this->setMaxAttempts();
    }

    public function add(string $key): AttemptInterface
    {
        $resource = $this->createResource($key);
        foreach ($this->attemptManagers as $attemptManager) {
            app($attemptManager, ['attemptResource' => $resource])->add();
        }

        $this->addBlock($key, $resource->maxAttempts);
        return $this;
    }

    public function addBlock(string $key)
    {
        $maxAttempts = $this->getMaxAttempts();
        if ($maxAttempts <= $this->total($key)) {
            app(Block::class)->tag($this->tags)->add($key);
            $this->exceptionBlocked($key);
        }
    }

    public function createResource(?string $key=null): AttemptResource
    {
        $key = is_null($key) ? $this->getKey() : $key;
        $resource = new AttemptResource;
        $resource->token = $this->token($key);
        $resource->maxAttempts = $this->getMaxAttempts();

        return $resource;
    }

    public function delete(string $key): AttemptInterface
    {

    }

    public function deleteAll(): AttemptInterface
    {
        $resource = $this->createResource();
        foreach ($this->attemptManagers as $attemptManager) {
            app($attemptManager, ['attemptResource' => $resource])->deleteAll();
        }

        return $this;
    }

    /**
     * Count the total number of attempts
     *
     * @param $key
     *
     * @return int
     */
    public function total(string $key): int
    {
        $attempts = 0;
        $resource = $this->createResource($key);

        foreach ($this->attemptManagers as $attemptManager) {
            $attempts += app($attemptManager, ['attemptResource' => $resource])->total();
        }

        return (int) $attempts ?? 0;
    }

    public function getMaxAttempts(): int
    {
        return $this->maxAttempts ?? 0;
    }

    public function setMaxAttempts(?int $maxAttempts=null)
    {
        $this->maxAttempts = (int) is_null($maxAttempts) ? config('brute.max_attempts') : $maxAttempts;
        return $this;
    }
}