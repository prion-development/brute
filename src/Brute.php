<?php

namespace Brute;

/**
 * This class is the main entry point of Prion Brute. Usually this the interaction
 * with this class will be done through the Brute Facade
 *
 * @license MIT
 * @company Prion Development
 * @package RateLimiter
 */

class Brute
{
    /**
     * Laravel application.
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function attempt(): AttemptInterface
    {
        return $this->app->make(AttemptInterface::class);
    }

    public function block(): BlockInterface
    {
        return $this->app->make(BlockInterface::class);
    }
}