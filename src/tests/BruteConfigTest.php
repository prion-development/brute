<?php

class BruteConfigTest extends BruteBaseTest
{
    /**
     * Make sure the config is loading
     */
    public function testConfigExists(): void
    {
        $config = config('brute');
        $this->assertNotEmpty($config, 'Configuration is not setup properly');
    }

    /**
     * Make sure the cache tag is not blank
     */
    public function testConfigCacheTag(): void
    {
        $this->assertNotNull(config('brute.cache.tag'), 'Default cache tag is not set');
    }

    /**
     * Make sure we can pull all commands
     */
    public function testAllCommands(): void
    {
        $commands = app(\Brute\Providers\Commands::class, ['app' => $this->app])->all();
        $this->assertIsArray($commands);
        $this->assertNotEmpty($commands, 'Commands are not registering correctly');
    }
}