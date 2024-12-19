<?php

namespace MoeMizrak\ValidatorGuard\Tests;

use MoeMizrak\ValidatorGuard\Attributes\AllowedValuesGuard;
use MoeMizrak\ValidatorGuard\Attributes\ArrayKeysExistGuard;
use MoeMizrak\ValidatorGuard\Attributes\CallbackGuard;
use MoeMizrak\ValidatorGuard\Attributes\DateGuard;
use MoeMizrak\ValidatorGuard\Attributes\IntervalGuard;
use MoeMizrak\ValidatorGuardCore\Data\MethodContextData;

class Example
{
    #[IntervalGuard(lowerBound:10, operator: '>')]
    public function smallerThanMethod(int $param): int
    {
        return $param;
    }

    #[IntervalGuard(lowerBound: 10, operator: '<', upperBound: 30)]
    public function inBetweenMethod(int $param): int
    {
        return $param;
    }

    #[IntervalGuard(lowerBound: 10, operator: '<')]
    #[IntervalGuard(lowerBound: 30, operator: '>=')]
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

    #[CallbackGuard(
        className: Example::class,
        methodName: 'voidResultMethod',
        params: [100],
    )]
    public function callbackGuardExpectedResultNullMethod(): string
    {
        return 'callbackGuardExpectedResultNullMethod response';
    }

    public function voidResultMethod(int $intParam): void
    {}

    #[CallbackGuard(
        className: Example::class,
        methodName: 'boolResultMethod',
        expectedResult: true
    )]
    public function callbackGuardExpectedBoolMethod(): string
    {
        return 'callbackGuardExpectedBoolMethod response';
    }

    #[CallbackGuard(
        className: Example::class,
        methodName: 'boolResultMethod',
        expectedResult: false
    )]
    public function callbackGuardExpectedBoolFailureMethod(): string
    {
        return 'callbackGuardExpectedBoolFailureMethod response';
    }

    public function boolResultMethod(): bool
    {
        return true;
    }

    #[CallbackGuard(
        className: Example::class,
        methodName: 'boolResultMethod',
        expectedResult: new MethodContextData([
            'params' => [],
            'methodName' => 'random',
            'className' => Example::class,
        ])
    )]
    public function callbackGuardExpectedObjectFailureMethod(): string
    {
        return 'callbackGuardExpectedBoolFailureMethod response';
    }

    #[CallbackGuard(
        className: Example::class,
        methodName: 'objectResultMethod',
        expectedResult: new MethodContextData([
            'params' => [],
            'methodName' => 'random',
            'className' => Example::class,
        ])
    )]
    public function callbackGuardExpectedObjectSucceedMethod(): string
    {
        return 'callbackGuardExpectedObjectSucceedMethod response';
    }

    public function objectResultMethod(): object
    {
        // Create an anonymous object with properties
        return new MethodContextData([
            'params' => [],
            'methodName' => 'random',
            'className' => Example::class,
        ]);
    }
}