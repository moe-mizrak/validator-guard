<?php

namespace MoeMizrak\ValidatorGuard\Tests;

use MoeMizrak\ValidatorGuard\Attributes\AllowedValuesGuard;
use MoeMizrak\ValidatorGuard\Attributes\ArrayKeysExistGuard;
use MoeMizrak\ValidatorGuard\Attributes\DateGuard;
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
        #[AllowedValuesGuard(paramPosition: 0, values: ['allowedString', 'anotherValue'])] string $stringParam,
        int $intParam
    ): int {
        return $intParam;
    }

    public function multipleAllowedValuesMethod(
        #[AllowedValuesGuard(paramPosition: 0, values: ['firstAllowedString', 'anotherValue'])] string $firstParam,
        #[AllowedValuesGuard(paramPosition: 1, values: ['secondAllowedString'])] string $secondParam
    ): string {
        return $firstParam . ' ' . $secondParam;
    }

    public function allowedValuesNullableMethod(
        int $intParam,
        #[AllowedValuesGuard(paramPosition: 1, values: ['allowedString', 'anotherValue', null])] ?string $stringParam = null
    ): int {
        return $intParam;
    }

    public function allowedValuesNullMethod(
        int $intParam,
        #[AllowedValuesGuard(paramPosition: 1, values: ['allowedString', 'anotherValue'])] ?string $stringParam = null
    ): int {
        return $intParam;
    }

    public function dateBoundaryPastDateMethod(
        int $intParam,
        #[DateGuard(paramPosition: 1, boundary: DateGuard::PAST)] ?string $dateParam
    ): int {
        return $intParam;
    }

    public function dateBoundaryFutureDateMethod(
        int $intParam,
        #[DateGuard(paramPosition: 1, boundary: DateGuard::FUTURE)] ?string $dateParam
    ): int {
        return $intParam;
    }

    public function dateBoundaryBetweenDateRangeMissingMethod(
        int $intParam,
        #[DateGuard(paramPosition: 1, boundary: DateGuard::BETWEEN)] ?string $dateParam
    ): int {
        return $intParam;
    }

    public function dateBoundaryBetweenBoundaryLowerRangeMissingMethod(
        int $intParam,
        #[DateGuard(
            paramPosition: 1,
            boundary: DateGuard::BETWEEN,
            range: ['upperBound' => '2054-12-12'])
        ] ?string $dateParam
    ): int {
        return $intParam;
    }

    public function dateBoundaryBetweenBoundaryMethod(
        int $intParam,
        #[DateGuard(
            paramPosition: 1,
            boundary: DateGuard::BETWEEN,
            range: [
                'upperBound' => '2030-12-12',
                'lowerBound' => '2020-12-12'
            ])] ?string $dateParam
    ): int {
        return $intParam;
    }
}