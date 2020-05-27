<?php

namespace Brute\Providers;

use Brute\AttemptInterface;
use Brute\Exception\BruteException;
use Illuminate\Support\ServiceProvider;

class BruteAttempt extends ServiceProvider
{
    public function register(): void
    {
        $this->registerGateway();
    }

    private function registerGateway()
    {
        $gateway = config('brute.gateway');
        $gateway = strtolower($gateway);

        switch($gateway) {
            case 'cache':
                $this->app->bind(AttemptInterface::class, \Brute\Gateways\Cache\Attempt::class);
                break;

            default:
                throw new BruteException("Invalid brute gateway: " . $gateway);
        }
    }
}
