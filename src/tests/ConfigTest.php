<?php

class ConfigTest extends BruteBaseTest
{
    /**
     * Make sure the config is loading
     */
    public function testConfigExists()
    {
        $config = config('brute');
        $this->assertNotEmpty($config, 'Configuratio is not setup properly');
    }

    /**
     * Make sure the cache tag is not blank
     */
    public function testConfigCacheTag()
    {
        $this->assertNotNull(config('brute.cache.tag'), 'Default cache tag is not set');
    }
}