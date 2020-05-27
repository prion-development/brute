<?php

namespace Unit\Redis;

use Brute\Gateways\Redis\Attempt;

class AttemptTagTest extends \BruteBaseTest
{
    /**
     * @group Redis
     */
    public function testAddTag()
    {
        $tag = 'tagRedisAddTag';
        $attemptInstance = app(Attempt::class)->tag($tag);
        $this->assertEquals($attemptInstance->tags(), [0 => $tag . '::']);
    }

    /**
     * @group Redis
     */
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

    /**
     * @group Redis
     */
    public function testAddDeleteAttempt()
    {
        $key = 'attemptRedisTagTestDelete';
        $tag = 'attemptRedisTagTestTag5';
        $attemptInstance = app(Attempt::class)->tag($tag)->setKey($key);
        $attemptInstance->deleteAll();
        $this->assertEquals(0, $attemptInstance->deleteAll()->total($key));
        $this->assertEquals(1, $attemptInstance->add($key)->total($key));

        $attemptInstance = app(Attempt::class)->setKey($key);
        $this->assertEquals(0, $attemptInstance->deleteAll()->total($key));
        $this->assertEquals(1, $attemptInstance->add($key)->total($key));
    }
}
