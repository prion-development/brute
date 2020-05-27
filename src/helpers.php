<?php

if (!function_exists('bruteKeyFilter')) {
    function bruteKeyFilter(string $string): string
    {
        $string = str_replace("::::", "::", $string);
        return $string;
    }
}
