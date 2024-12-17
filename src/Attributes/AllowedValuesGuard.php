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
 *      #[AllowedValuesGuard(values: ['firstAllowedString', 'anotherValue'], paramPosition: 0)] string $firstParam,
 *      #[AllowedValuesGuard(values: ['secondAllowedString'], paramPosition: 1)] string $secondParam
 * )
 *
 * @attribute AllowedValuesGuard
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final readonly class AllowedValuesGuard implements ValidationAttributeInterface
{
    public function __construct(
        protected array $values = [],
        protected int $paramPosition
    ) {}

    /**
     * @inheritDoc
     */
    public function handle(MethodContextData $methodContextData): bool
    {
        $param = Arr::get($methodContextData->params, $this->paramPosition);

        return in_array($param, $this->values);
    }
}