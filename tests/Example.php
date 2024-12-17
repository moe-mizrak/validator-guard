<?php

namespace MoeMizrak\ValidatorGuard\Tests;

use MoeMizrak\ValidatorGuard\Attributes\AllowedValuesGuard;
use MoeMizrak\ValidatorGuard\Attributes\ArrayKeysExistGuard;
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

    #[ArrayKeysExistGuard(
        keys: ['nonExistedKey', 'secondKey'],
        inMethodResult: true
    )]
    public function arrayKeysExistFailureMethod(string $param): array
    {
        return $this->returnArrayMethod();
    }

    private function returnArrayMethod(): array
    {
        return [
            'firstKey' => 'firstValue',
            'secondKey' => ['randomString' => 'randomValue'],
        ];
    }

    #[ArrayKeysExistGuard(
        keys: ['nonExistedKey', 'secondParamKey'],
        inParam: true,
        paramPosition: 1
    )]
    public function arrayKeysExistFailureParamMethod(int $intParam, array $arrayParam): array
    {
        return $this->returnArrayMethod();
    }

    #[ArrayKeysExistGuard(
        keys: ['firstKey', 'secondKey'],
        inParam: true,
        paramPosition: 1
    )]
    public function arrayKeysExistBothParamAndResultMethod(int $intParam, array $arrayParam): array
    {
        return $this->returnArrayMethod();
    }

    public function allowedValuesMethod(
        #[AllowedValuesGuard(values: ['allowedString', 'anotherValue'], paramPosition: 0)] string $stringParam,
        int $intParam
    ): int {
        return $intParam;
    }

    public function multipleAllowedValuesMethod(
        #[AllowedValuesGuard(values: ['firstAllowedString', 'anotherValue'], paramPosition: 0)] string $firstParam,
        #[AllowedValuesGuard(values: ['secondAllowedString'], paramPosition: 1)] string $secondParam
    ): string {
        return $firstParam . ' ' . $secondParam;
    }

    public function allowedValuesNullableMethod(
        int $intParam,
        #[AllowedValuesGuard(values: ['allowedString', 'anotherValue', null], paramPosition: 1)] ?string $stringParam = null
    ): int {
        return $intParam;
    }

    public function allowedValuesNullMethod(
        int $intParam,
        #[AllowedValuesGuard(values: ['allowedString', 'anotherValue'], paramPosition: 1)] ?string $stringParam = null
    ): int {
        return $intParam;
    }
}