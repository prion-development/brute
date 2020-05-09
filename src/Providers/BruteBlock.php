<?php

namespace Brute\Providers;

use Brute\BlockInterface;
use Brute\Exception\BruteException;
use Illuminate\Support\ServiceProvider;

class BruteBlock extends ServiceProvider
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
                $this->app->bind(BlockInterface::class, \Brute\Gateways\Cache\Block::class);
                break;

            default:
                throw new BruteException("Invalid brute gateway: " . $gateway);
        }
    }
}
