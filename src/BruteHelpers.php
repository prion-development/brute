<?php

namespace Brute;

trait BruteHelpers
{
    public function clean(string $string): string
    {
        $string = str_replace("::::", "::", $string);
        return $string;
    }
}