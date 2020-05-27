<?php

namespace Unit\Cache;

use Brute\Exception\BruteBlockedException;
use Brute\Gateways\Cache\Attempt;
use Carbon\Carbon;
use ReflectionMethod;

class AttemptTest extends \BruteBaseTest
{
    public function testAddDeleteAttempt()
    {
        $key = 'attempt_1';
        $attemptInstance = app(Attempt::class);
        $this->assertEquals($attemptInstance->total($key), 0);

        $this->assertEquals($attemptInstance->add($key)->total($key), 1);
        $this->assertEquals($attemptInstance->add($key)->total($key), 2);
        $this->assertEquals($attemptInstance->delete($key)->total($key), 1);
        $this->assertEquals($attemptInstance->add($key)->total($key), 2);
        $this->assertEquals($attemptInstance->add($key)->total($key), 3);
    }

    public function testDeleteFirstTimestamp()
    {
        $key = 'attempt_2';
        $attemptInstance = app(Attempt::class);
        $this->assertEquals($attemptInstance->total($key), 0);

        $this->assertEquals($attemptInstance->add($key)->total($key), 1);
        sleep(1);
        $this->assertEquals($attemptInstance->add($key)->total($key), 2);

        $timestamps = $attemptInstance->all($key);
        rsort($timestamps);
        $keepValue = reset($timestamps);

        $this->assertEquals($attemptInstance->delete($key)->all($key), [0 => $keepValue]);
    }

    public function testTimestampInvalid()
    {
        $originalTtl = config('brute.attempt_ttl');
        config(['brute.attempt_ttl' => 1]);

        $validTimestamp = Carbon::now('UTC');
        $this->assertFalse(app(Attempt::class)->expireTimestamp($validTimestamp));

        $invalidTimestamp = Carbon::now('UTC')->subMinutes(2);
        $this->assertTrue(app(Attempt::class)->expireTimestamp($invalidTimestamp));

        config(['brute.attempt_ttl' => $originalTtl]);
    }

    public function testRemoveExpiredTimestamps()
    {
        $originalTtl = config('brute.attempt_ttl');
        config(['brute.attempt_ttl', 15]);

        $timestamps = [
            Carbon::now('UTC'),
            Carbon::now('UTC')->subMinute(),
            Carbon::now('UTC')->subMinutes(10),
            Carbon::now('UTC')->subMinutes(15)->addSeconds(2),
            Carbon::now('UTC')->subMinutes(15)->subSeconds(2),
            Carbon::now('UTC')->subHour(),
        ];

        $checkTimestamps = app(Attempt::class)->removeInvalidTimestamps($timestamps);
        $this->assertEquals(count($checkTimestamps), 4);

        config(['brute.attempt_ttl', $originalTtl]);
    }

    public function testBlockKey()
    {
        $key = 'attempt_3';
        $attemptInstance = app(Attempt::class);
        $this->assertEquals($attemptInstance->total($key), 0);

        $originalMaxAttempts = config('brute.max_attempts');
        config(['brute.max_attempts' => 2]);

        $this->assertEquals($attemptInstance->add($key)->total($key), 1);
        $this->expectException(BruteBlockedException::class);
        $attemptInstance->add($key);

        config(['brute.max_attempts' => $originalMaxAttempts]);
    }
}