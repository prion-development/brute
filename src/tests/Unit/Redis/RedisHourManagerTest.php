<?php

namespace Unit\Redis;

use Brute\Gateways\Redis\AttemptManagers\Hour;
use Brute\Gateways\Redis\AttemptResource;
use Carbon\Carbon;
use Mockery\Mock;

/**
 * @group Redis
 */
class RedisHourManagerTest extends \BruteBaseTest
{
    public function testRedisShouldRun()
    {
        $original = config('brute.attempt_ttl');
        config(['brute.attempt_ttl' => 61]);
        $this->assertTrue(app(Hour::class)->setTtl(61)->shouldRun());

        config(['brute.attempt_ttl' => 59]);
        $this->assertFalse(app(Hour::class)->setTtl(59)->shouldRun());

        config(['brute.attempt_ttl' => $original]);
    }

    public function testHourTokenGenerator()
    {
        $resource = app(AttemptResource::class);
        $resource->token = 'brute:test:redis:hour-token';
        $hour = intval(Carbon::now('UTC')->format('H'));

        $this->assertEquals(
            $resource->token . ':h' . $hour,
            app(Hour::class, ['attemptResource' => $resource])->token()
        );

        $this->assertEquals(
            $resource->token . ':h0',
            app(Hour::class, ['attemptResource' => $resource])->token(0)
        );

        $this->assertEquals(
            $resource->token . ':h1',
            app(Hour::class, ['attemptResource' => $resource])->token(1)
        );

        $this->assertEquals(
            $resource->token . ':h11',
            app(Hour::class, ['attemptResource' => $resource])->token(11)
        );

    }

    public function testRedisHourAllTokens()
    {
        $resource = app(AttemptResource::class);
        $resource->token = 'brute:test:redis:hour-token';
        $tokens = app(Hour::class, ['attemptResource' => $resource])->tokens();

        $hour = 0;
        while ($hour <= 23) {
            $this->assertTrue(in_array($resource->token . ':h' . $hour, $tokens));
            $hour++;
        }
    }

    public function testRedisHourAddAttempt()
    {
        $original = config('brute.attempt_ttl');
        config(['brute.attempt_ttl' => 100]);
        $resource = app(AttemptResource::class);
        $resource->token = 'brute:test:redis:hour-token-attempt-1';
        $hourManager = app(Hour::class, ['attemptResource' => $resource])->deleteAll();

        $this->assertEquals(1, $hourManager->add()->total());
        config(['brute.attempt_ttl' => $original]);
    }

    /**
     * Test Current and Middle Hour
     *
     * @group Redis
     */
    public function testRedisHourTotal()
    {
        $original = config('brute.attempt_ttl');
        config(['brute.attempt_ttl' => 140]);

        $resource = app(AttemptResource::class);
        $resource->token = 'brute:test:redis:hour-token-attempt-3';
        app(Hour::class, ['attemptResource' => $resource])->deleteAll();

        $previousHour = intval(Carbon::now('UTC')->subHour()->format('H'));
        $previousHourToken = $resource->token . ':h' . $previousHour;

        $n=0;
        while ($n<4) {
            app('redis')->connection()->incr($previousHourToken);
            $n++;
        }

        $hourManager = app(Hour::class, ['attemptResource' => $resource]);
        $this->assertEquals(4, $hourManager->total());
        $this->assertEquals(5, $hourManager->add()->total());
        config(['brute.attempt_ttl' => $original]);
    }

    /**
     * Test First Hour
     *
     * @group Redis
     */
    public function testRedisHourGonePercent()
    {
        $original = config('brute.attempt_ttl');
        config(['brute.attempt_ttl' => 100]);

        $resource = app(AttemptResource::class);
        $resource->token = 'brute:test:redis:hour-token-attempt-3';
        app(Hour::class, ['attemptResource' => $resource])->deleteAll();

        $previousHour = intval(Carbon::now('UTC')->subMinutes(100)->format('H'));
        $previousHourToken = $resource->token . ':h' . $previousHour;

        $n=0;
        while ($n<4) {
            app('redis')->connection()->incr($previousHourToken);
            $n++;
        }

        $hourManager = $this->partialMock(Hour::class, function ($mock) {
            $mock->shouldReceive('hourGonePercent')->times(2)->andReturn(.5);
        })->setResource($resource);

        $this->assertEquals(2, $hourManager->total());
        $this->assertEquals(3, $hourManager->add()->total());
        config(['brute.attempt_ttl' => $original]);
    }

    public function testRedisHourLowTtlAttempt()
    {
        $original = config('brute.attempt_ttl');
        config(['brute.attempt_ttl' => 15]);
        $resource = app(AttemptResource::class);
        $resource->token = 'brute:test:redis:hour-token-attempt-2';
        $hourManager = app(Hour::class, ['attemptResource' => $resource])->deleteAll();

        $this->assertEquals(0, $hourManager->add()->total());
        config(['brute.attempt_ttl' => $original]);
    }
}