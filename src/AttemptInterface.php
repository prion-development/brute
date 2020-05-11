<?php

namespace Brute;

use Carbon\Carbon;

interface AttemptInterface
{
    public function add(string $key, int $maxAttempts = 15): AttemptInterface;

    public function delete(string $key): AttemptInterface;

    public function total($key): int;

    public function all($key): array;

    public function expireTimestamp(Carbon $timestamp): bool;
}
