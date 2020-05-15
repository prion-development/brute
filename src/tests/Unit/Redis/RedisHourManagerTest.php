<?php

namespace Unit\Redis;

use Brute\Gateways\Redis\AttemptManagers\Hour;
use Brute\Gateways\Redis\AttemptResource;
use Carbon\Carbon;

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

}