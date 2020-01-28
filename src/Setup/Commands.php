<?php

namespace Brute\Setup;

use Illuminate\Support\ServiceProvider;

class Commands extends ServiceProvider implements SetupInterface
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Migration' => [
            'class' => \Membrane\Commands\Migration::class,
            'command' => 'command.membrane.migration',
        ],
        'Config' => [
            'class' => \Membrane\Commands\Config::class,
            'command' => 'command.membrane.config',
        ],
        'Seeder' => [
            'class' => \Membrane\Commands\Seeder::class,
            'command' => 'command.membrane.seeder',
        ],
        'Setup' => [
            'class' => \Membrane\Commands\Setup::class,
            'command' => 'command.membrane.setup',
        ],
        'TokenDeleteExpired' => [
            'class' => \Membrane\Commands\Token\DeleteExpired::class,
            'command' => 'command.membrane.token-delete-expired',
        ],
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->registerCommands();
        $this->commands(array_column($this->commands, 'command'));
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
}