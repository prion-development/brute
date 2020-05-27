<?php

namespace Unit\Cache;

use Brute\Exception\BruteBlockedException;
use Brute\Gateways\Cache\Block;

class BlockTest extends \BruteBaseTest
{
    public function testAddBlock()
    {
        $tag = 'BlockTestTag1';
        $key = 'blockTestKey1';

        $this->assertTrue(app(Block::class)->check($key));

        $this->expectException(BruteBlockedException::class);
        app(Block::class)->tag($tag)->add($key)->check($key);
    }

    public function testDeleteBlock()
    {
        $tag = 'BlockTestTag1';
        $key = 'blockTestKey1';
        $this->testAddBlock();

        $this->assertTrue(app(Block::class)->tag($tag)->delete($key)->check($key));
    }
}
