<?php

return [

    /**
     * Length of time to block key
     */
    'block_ttl' => 30, // in minutes

    /**
     * Number of minutes to store an attempt
     */
    'attempt_ttl' => 15,

    /**
     * Maximum Number of Attempts
     */
    'attempts' => 10,

    'version' => "1.0",

    'cache' => [
        'tag' => "prionbrute",
    ],

];