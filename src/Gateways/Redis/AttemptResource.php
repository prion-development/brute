<?php

namespace Brute\Gateways\Redis;

class AttemptResource
{
    /**
     * @var int|null
     */
    public $maxAttempts;

    /**
     * @var int|null
     */
    public $ttl;

    /**
     * The token generated from the tags
     *
     * @var string
     */
    public $token;
}