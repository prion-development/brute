<?php

namespace Brute\Providers;

/**
 * This file is part of Prion Development's Brute Package,
 * an brute force monitor and blocker for Lumen.
 *
 * @license MIT
 * @company Prion Development
 * @package Brute
 */

use Illuminate\Support\ServiceProvider;

class Commands extends ServiceProvider implements ProviderInterface
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'AttemptsDelete' => [
            'class' => \Brute\Commands\AttemptsDelete::class,
            'command' => 'command.brute.attempts-delete',
        ],
        'BlocksDelete' => [
            'class' => \Brute\Commands\DeleteBlock::class,
            'command' => 'command.brute.blocks-delete',
        ],
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->registerCommands();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Loop Through All Commands and Register them
     */
    protected function registerCommands(): void
    {
        foreach ($this->commands as $key => $command) {
            call_user_func_array([$this, 'registerCommand'], $command);
        }
    }

    /**
     * Register a Single Command with a Class
     *
     * @param $class
     * @param $command
     */
    protected function registerCommand($class, $command): void
    {
        $this->app->singleton($command, function ($app) use ($class) {
            return new $class($app['files']);
        });
    }

    /**
     * Pull all Commands for Brute
     *
     * @return array
     */
    public function all(): array
    {
        return array_column($this->commands, 'command');
    }
}