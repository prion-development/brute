<?php

namespace Brute\Gateways\Redis\AttemptManagers;

use Brute\Gateways\Redis\AttemptResource;
use Carbon\Carbon;

class Minute extends AddAttemptsAbstract implements AddAttemptsInterface
{
    public function add(): AddAttemptsInterface
    {
        if (!$this->shouldRun()) {
            return $this;
        }

        $now = Carbon::now('UTC');
        $expireAt = $this->expireAt();
        $token = $this->attemptResource->token . ':m' . intval(Carbon::now('UTC')->format('i'));
        $this->redis()->incr($token);
        $this->redis()->expire($token, $expireAt->diffInMinutes($now));
        return $this;
    }

    public function expireAt(): Carbon
    {
        if ($this->ttl() >= 60) {
            return Carbon::now('UTC')->addMinutes($this->ttl())->startOfHour();
        }

        return Carbon::now('UTC')->addMinutes($this->ttl());
    }

    public function token(?int $minute=null): string
    {
        $minute = is_null($minute) ? Carbon::now('UTC')->format('i') : 0;
        $minute = intval($minute);
        return $this->attemptResource->token . ':m' . $minute;
    }

    public function tokens(): array
    {
        $minute = 0;
        $keys = [];

        while ($minute <= 60) {
            $keys[] = $this->token($minute);
            $minute++;
        }

        return $keys;
    }

    public function total(): int
    {
        $attempts = 0;
        $firstMinute = $this->firstMinuteToken();
        $tokens = $this->tokens();
        $tokens = array_diff($tokens, [$firstMinute]);
        $attempts += $this->firstMinuteTotal();
        $attempts += array_sum($this->redis()->mget($tokens));

        return (int) $attempts ?? 0;
    }

    public function firstMinute(): int
    {
        $firstMinute = Carbon::now('UTC')->subMinutes($this->ttl())->format('i');
        return intval($firstMinute);
    }

    public function firstMinuteToken(): string
    {
        $firstMinute = Carbon::now('UTC')->subMinute()->format('i');
        $firstMinute = intval($firstMinute);
        return $this->token($firstMinute);
    }

    public function firstMinuteTotal(): int
    {
        $token = $this->firstMinuteToken();
        if ($this->ttl() >= 60) {
            return $this->redis()->get($token);
        }

        $secondsGone = Carbon::now('UTC')->format('s');
        $secondsGone = intval($secondsGone);
        $percentMinuteGone = $secondsGone / 60;

        return $percentMinuteGone === 0 ? $this->redis()->get($token) : $percentMinuteGone * $this->redis()->get($token);
    }

    public function shouldRun(): bool
    {
        return true;
    }
}