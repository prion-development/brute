<?php

namespace Unit\Redis;

use Brute\Gateways\Redis\Attempt;

/**
 * @group Redis
 */
class AttemptTagTest extends \BruteBaseTest
{
    public function testAddTag()
    {
        $tag = 'tagRedisAddTag';
        $attemptInstance = app(Attempt::class)->tag($tag);
        $this->assertEquals($attemptInstance->tags(), [0 => $tag . '::']);
    }

    public function testAddTags()
    {
        $tag1 = 'tagRedisAddTag1';
        $tag2 = 'tagRedisAddTag2';
        $attemptInstance = app(Attempt::class)->tag($tag1)->tag($tag2);
        $this->assertEquals($attemptInstance->tags(), [0 => $tag1 . '::', 1 => $tag2 . '::']);

        $tagArray = ['tagRedisAddTag3', 'tagRedisAddTag4'];
        $attemptInstance = $attemptInstance->tag($tagArray);
        $this->assertEquals($attemptInstance->tags(), [0 => $tag1 . '::', 1 => $tag2 . '::', 'tagRedisAddTag3::', 'tagRedisAddTag4::']);
    }

    public function testAddDeleteAttempt()
    {
        $key = 'attemptRedisTagTestDelete';
        $tag = 'attemptRedisTagTestTag5';
        $attemptInstance = app(Attempt::class)->tag($tag);
        $this->assertEquals($attemptInstance->add($key)->total($key), 1);

        $attemptInstance = app(Attempt::class);
        $this->assertEquals($attemptInstance->add($key)->total($key), 1);
    }
}
