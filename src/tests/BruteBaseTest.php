<?php

class BruteBaseTest extends BruteTestCase
{
    /**
     * Make sure we use a tagged cache
     */
    public function testBruteSetCache()
    {
        $method = new ReflectionMethod('\Brute\Gateways\Cache\Attempt', 'cache');
        $method->setAccessible(true);

        $object = new \Brute\Gateways\Cache\Attempt;
        $this->assertTrue($method->invoke($object) instanceof \Illuminate\Cache\TaggedCache);
    }

    /**
     * Make sure the filter works the way we expect
     *
     * @throws ReflectionException
     */
    public function testBruteBaseFilter()
    {
        $method = new ReflectionMethod('\Brute\Gateways\Cache\Attempt', 'key');
        $method->setAccessible(true);

        $object = new \Brute\Gateways\Cache\Attempt;
        $this->assertTrue($method->invoke($object, 'TestString::::TestString') == $object->type . 'TestString::TestString');
        $this->assertTrue($method->invoke($object, 'TestString::TestString') === $object->type . 'TestString::TestString');
        $this->assertTrue($method->invoke($object, 'TestString') === $object->type . 'TestString');

        $this->assertTrue($method->invoke($object, 'brute_block:TestString') === $object->type . 'TestString');
        $this->assertTrue($method->invoke($object, 'brute_attempt:TestString') === $object->type . 'TestString');
    }

    /**
     * Test the Key Filtering
     */
    public function testBruteBaseItemFilter()
    {
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)->tag('TestString::TestString')->tags() === [0 => 'TestString::TestString::']);
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)->tag('TestString::::TestString')->tags() === [0 => 'TestString::TestString::']);
    }

    /**
     * Test Prefix Filtering
     */
    public function testBruteBasePrefixFilter()
    {
        $testString1 = 'TestString::TestString';
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)->tag($testString1)->tags() === [0 => $testString1 . '::']);

        $testString1 = 'TestString::::TestString';
        $testString2 = 'TestString2::::TestString2';
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)
                ->tag($testString1)
                ->tag($testString2)
                ->tags() === [0 => 'TestString::TestString::', 1 => 'TestString2::TestString2::']);
    }
}