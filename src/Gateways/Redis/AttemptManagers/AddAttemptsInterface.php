<?php

namespace Brute\Gateways\Redis\AttemptManagers;

use Carbon\Carbon;

interface AddAttemptsInterface
{
    public function add(): AddAttemptsInterface;
    public function expireAt(): Carbon;
    public function setTtl(int $ttl): AddAttemptsInterface;
    public function shouldRun(): bool;
    public function token(?int $minute=null): string;
    public function tokens(): array;
    public function total(): int;

    /**
     * TLL in Minutes
     *
     * @return int
     */
    public function ttl(): int;
}