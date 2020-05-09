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
        $method = new ReflectionMethod('\Brute\Gateways\Cache\Attempt', 'filter');
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
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)->item('TestString::TestString')->item === 'TestString::TestString::');
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)->item('TestString::::TestString')->item === 'TestString::TestString::');
    }

    /**
     * Test Prefix Filtering
     */
    public function testBruteBasePrefixFitler()
    {
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)->prefix('TestString::TestString')->prefix === 'TestString::TestString::');
        $this->assertTrue(app(\Brute\Gateways\Cache\Attempt::class)->prefix('TestString::::TestString')->prefix === 'TestString::TestString::');
    }
}