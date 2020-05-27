<?php

namespace Brute;

use Carbon\Carbon;

interface AttemptInterface
{
    public function add(string $key): AttemptInterface;

    public function delete(string $key): AttemptInterface;

    public function total(string $key): int;
}
