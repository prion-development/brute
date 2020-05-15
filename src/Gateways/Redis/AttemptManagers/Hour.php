<?php

namespace Brute\Gateways\Redis\AttemptManagers;

use Brute\Gateways\Redis\AttemptResource;
use Carbon\Carbon;

class Hour extends AddAttemptsAbstract implements AddAttemptsInterface
{
    public function add(): AddAttemptsInterface
    {
        if (!$this->shouldRun()) {
            return $this;
        }

        $now = Carbon::now('UTC');
        $expireAt = $this->expireAt();
        $token = $this->token();
        $this->redis()->incr($token);
        $this->redis()->expire($token, $expireAt->diffInMinutes($now));
        return $this;
    }

    public function expireAt(): Carbon
    {
        return Carbon::now('UTC')->addMinutes($this->ttl());
    }

    public function firstHour(): int
    {
        $firstMinute = Carbon::now('UTC')->subMinutes($this->ttl())->format('H');
        return intval($firstMinute);
    }

    public function firstHourToken(): string
    {
        $firstHour = Carbon::now('UTC')->subHour()->format('i');
        $firstHour = intval($firstHour);
        return $this->token($firstHour);
    }

    public function firstHourTotal(): int
    {
        $token = $this->firstHourToken();
        if ($this->ttl() >= 24 * 60) {
            return $this->redis()->get($token);
        }

        $minutesGone = Carbon::now('UTC')->format('m');
        $minutesGone = intval($minutesGone);
        $percentHourGone = $minutesGone / 60;

        return $percentHourGone === 0 ? $this->redis()->get($token) : $percentHourGone * $this->redis()->get($token);
    }

    public function shouldRun(): bool
    {
        if ($this->ttl() >= 60) {
            return true;
        }

        return false;
    }

    public function token(?int $hour=null): string
    {
        $hour = is_null($hour) ? Carbon::now('UTC')->format('H') : $hour;
        return $this->attemptResource->token . ':h' . intval($hour);
    }

    public function tokens(): array
    {
        $hour = 0;
        $tokens = [];
        while ($hour <= 23) {
            $tokens[] = $this->token($hour);
            $hour++;
        }

        return $tokens;
    }

    public function total(): int
    {
        $attempts = 0;
        $firstMinute = $this->firstHourToken();
        $tokens = $this->tokens();
        $tokens = array_diff($tokens, [$firstMinute]);
        $attempts += $this->firstHourTotal();
        $attempts += array_sum($this->redis()->mget($tokens));

        return (int) $attempts ?? 0;
    }
}
