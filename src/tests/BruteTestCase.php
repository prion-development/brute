<?php

abstract class BruteTestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Brute\BruteServiceProvider'];
    }
}