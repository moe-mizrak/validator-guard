<?php

namespace MoeMizrak\ValidatorGuard\Tests;

use MoeMizrak\ValidatorGuard\Attributes\IntervalGuard;

class Example
{
    #[IntervalGuard(10, '>')]
    public function smallerThanMethod(int $param): int
    {
        return $param;
    }

    #[IntervalGuard(10, '<', 30)]
    public function inBetweenMethod(int $param): int
    {
        return $param;
    }

    #[IntervalGuard(10, '<')]
    #[IntervalGuard(30, '>=')]
    public function repeatableMethod(int $param): int
    {
        return $param;
    }
}