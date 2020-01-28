<?php

namespace Brute\Setup;

interface SetupInterface
{
    public function boot(): void;

    public function register(): void;
}