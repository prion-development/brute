<?php

namespace Brute\Setup;

use Illuminate\Support\ServiceProvider;

class Config extends ServiceProvider implements SetupInterface
{
    private $config = [
        'brute'
    ];

    /**
     * Publish the Configuration File
     */
    public function boot(): void
    {
        foreach ($this->config as $config) {
            $app_path = app()->basePath('config/'. $config .'.php');
            $this->publishes([
                __DIR__ . '/../config/'. $config .'.php' => $app_path,
            ], $config);
        }
    }

    /**
     * Merge Configuration Settings at run time. If the API has not run
     * the configuration setup command, the default settings are merged in
     */
    public function register(): void
    {
        foreach ($this->config as $config) {
            $this->mergeConfigFrom(
                __DIR__ . '/../config/' . $config . '.php',
                $config
            );
        }
    }
}