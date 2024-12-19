<?php

namespace MoeMizrak\ValidatorGuard\Attributes;

use Illuminate\Support\Arr;
use MoeMizrak\ValidatorGuardCore\Contracts\ValidationAttributeInterface;
use MoeMizrak\ValidatorGuardCore\Data\MethodContextData;

/**
 * AllowedValuesGuard checks if method parameter is in allowed values list.
 *
 * Usage examples:
 * multipleAllowedValuesMethod(
 *      #[AllowedValuesGuard(paramPosition: 0, values: ['firstAllowedString', 'anotherValue'])] string $firstParam,
 *      #[AllowedValuesGuard(paramPosition: 1, values: ['secondAllowedString'])] string $secondParam
 * )
 *
 * @attribute AllowedValuesGuard
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final readonly class AllowedValuesGuard implements ValidationAttributeInterface
{
    public function __construct(
        private int $paramPosition,
        private array $values = []
    ) {}

    /**
     * @inheritDoc
     */
    public function handle(MethodContextData $methodContextData): bool
    {
        $param = Arr::get($methodContextData->params, $this->paramPosition);

        return in_array($param, $this->values, true);
    }
}