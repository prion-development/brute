<?php

namespace Brute\Providers;

interface ProviderInterface
{
    public function boot(): void;

    public function register(): void;
}