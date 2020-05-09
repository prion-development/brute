<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The Storage Gateway for Brute
    |--------------------------------------------------------------------------
    |
    | Supported: cache, database, redis
    |
    */

    'gateway' => env('BRUTE_GATEWAY', 'cache'),


    /*
    |--------------------------------------------------------------------------
    | Time Length to Block Keys
    |--------------------------------------------------------------------------
    |
    | @minutes
    |
    */

    'block_ttl' => env('BRUTE_BLOCK_TTL', 30),

    /*
    |--------------------------------------------------------------------------
    | Length of Time to store an attempt
    |--------------------------------------------------------------------------
    |
    |  Number of minutes we will store each failed attempt
    |
    | @minutes
    |
    */

    'attempt_ttl' => env('BRUTE_ATTEMPT_TTL', 15),

    /*
    |--------------------------------------------------------------------------
    | Maximum Number of Attempts
    |--------------------------------------------------------------------------
    |
    | integer
    |
    */

    'attempts' => env('BRUTE_ATTEMPTS_MAX', 10),

    /*
    |--------------------------------------------------------------------------
    | Brute Version
    |--------------------------------------------------------------------------
    |
    | @string
    |
    */

    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Brute Cache Tag (So our cached attempts are unique
    |--------------------------------------------------------------------------
    |
    | @string
    |
    */

    'cache' => [
        'tag' => env('BRUTE_TAG', "prionbrute"),
    ],

];