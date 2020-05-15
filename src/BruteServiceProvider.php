<?php

namespace Brute;

/**
 * This file is part of Prion Development's Brute Package,
 * an brute force monitor and blocker for Lumen.
 *
 * @license MIT
 * @company Prion Development
 * @package Brute
 */

use Illuminate\Support\ServiceProvider;

class BruteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $setup = [
        \Brute\Providers\Config::class,

        \Brute\Providers\BruteAttempt::class,
        \Brute\Providers\BruteBlock::class,
        \Brute\Providers\Commands::class,
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBrute();
        $this->registerProviders();
    }

    /**
     * Register Brute in Laravel/Lumen
     *
     */
    private function registerBrute(): void
    {
        $this->app->bind('brute', function ($app) {
            return app(Brute::class, ['app' => $app]);
        });

        $this->app->alias('Brute', 'Brute\Brute');
    }

    /**
     * Register Additional Providers, such as config setup
     * and command setup
     */
    private function registerProviders(): void
    {
        foreach($this->setup as $setup) {
            $this->app->register($setup);
        }
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        $commands = app(\Brute\Providers\Commands::class, ['app' => $this->app])->commands;
        return array_column($commands, 'command');
    }

}