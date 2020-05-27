<?php

namespace Unit\Cache;

use Brute\Exception\BruteBlockedException;
use Brute\Gateways\Cache\Attempt;
use Carbon\Carbon;

class AttemptTagTest extends \BruteBaseTest
{
    /**
     * @group Cache
     */
    public function testAddTag()
    {
        $tag = 'tagAddTag';
        $attemptInstance = app(Attempt::class)->tag($tag);
        $this->assertEquals($attemptInstance->tags(), [0 => $tag . '::']);
    }

    /**
     * @group Cache
     */
    public function testAddTags()
    {
        $tag1 = 'tagAddTag1';
        $tag2 = 'tagAddTag2';
        $attemptInstance = app(Attempt::class)->tag($tag1)->tag($tag2);
        $this->assertEquals($attemptInstance->tags(), [0 => $tag1 . '::', 1 => $tag2 . '::']);

        $tagArray = ['tagAddTag3','tagAddTag4'];
        $attemptInstance = $attemptInstance->tag($tagArray);
        $this->assertEquals($attemptInstance->tags(), [0 => $tag1 . '::', 1 => $tag2 . '::', 'tagAddTag3::', 'tagAddTag4::']);
    }

    /**
     * @group Cache
     */
    public function testAddDeleteAttempt()
    {
        $key = 'attemptTagTestDelete';
        $tag = 'atemptTagTestTag5';
        $attemptInstance = app(Attempt::class)->tag($tag);
        $this->assertEquals($attemptInstance->add($key)->total($key), 1);

        $attemptInstance = app(Attempt::class);
        $this->assertEquals($attemptInstance->add($key)->total($key), 1);
    }
}