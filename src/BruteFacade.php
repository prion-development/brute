<?php

namespace Brute;

/**
 * This file is part of Prion Development's Brute,
 * a tag based request limiter.
 *
 * @license MIT
 * @company Prion Development
 * @package Brute
 */

use Illuminate\Support\Facades\Facade;

class BruteFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'brute';
    }
}