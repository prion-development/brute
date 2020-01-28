<?php

namespace Brute;

/**
 * This file is part of Prion Development's Membrane Package,
 * an oauth account, role & permission management solution for Lumen.
 *
 * @license MIT
 * @company Prion Development
 * @package Membrane
 */

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class BruteServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $setup = [
        \Brute\Setup\Commands::class,
        \Brute\Setup\Config::class,
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

        foreach($this->setup as $setup) {
            $this->app->register($setup);
        }
    }

    /**
     * Register Membrane in Laravel/Lumen
     *
     */
    protected function registerBrute(): void
    {
        $this->app->bind('brute', function ($app) {
            return new Brute($app);
        });

        $this->app->alias('Brute', 'Brute\Brute');
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return array_values(app(\Brute\Setup\Commands::class)->commands);
    }

}