<?php

namespace Brute;

interface BlockInterface
{
    public function add($key): BlockInterface;

    public function check($key): bool;

    public function checkAndExtend($key): bool;

    public function delete($key): BlockInterface;

    public function extend($key): BlockInterface;
}
