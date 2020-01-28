<?php

namespace Brute;

/**
 * This class is the main entry point of Prion Brute. Usually this the interaction
 * with this class will be done through the Brute Facade
 *
 * @license MIT
 * @package Brute
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

    public function attempt()
    {

    }

    public function block()
    {

    }
}