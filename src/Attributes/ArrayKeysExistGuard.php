<?php

namespace MoeMizrak\ValidatorGuard\Attributes;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use MoeMizrak\ValidatorGuardCore\Contracts\ValidationAttributeInterface;
use MoeMizrak\ValidatorGuardCore\Data\MethodContextData;

/**
 * ArrayKeysExistGuard checks whether array key exists in the method array result or array parameter.
 *
 * Usage examples:
 * #[ArrayKeysExistGuard(
 *      keys: ['firstKey', 'secondKey'],
 *      inParam: true,
 *      paramPosition: 1
 * )]
 *
 * #[ArrayKeysExistGuard(
 *      keys: ['firstKey', 'secondKey'],
 *      inMethodResult: true
 * )]
 *
 * @attribute ArrayKeysExistGuard
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class ArrayKeysExistGuard implements ValidationAttributeInterface
{
    public function __construct(
        public array $keys,
        public ?bool $inMethodResult = false,
        public ?bool $inParam = false,
        public ?int $paramPosition = 0,
    ) {}

    /**
     * @inheritDoc
     */
    public function handle(MethodContextData $methodContextData): bool
    {
        // Handle the case for method result
        if ($this->inMethodResult && ! $this->checkArrayKeys($methodContextData->methodResult)) {
            return false;
        }

        // Handle the case for parameter
        if ($this->inParam && $this->paramPosition !== null) {
            $params = $methodContextData->params;
            // Get the obtained param from the params array by position
            $paramData = Arr::get($params, $this->paramPosition);

            if (! $this->checkArrayKeys($paramData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether keys exist in give data
     *
     * @param array|null $data
     *
     * @return bool
     */
    private function checkArrayKeys(?array $data): bool
    {
        if (! is_array($data)) {
            throw new InvalidArgumentException(
                'Expected an array, received ' . gettype($data)
            );
        }

        // Loop through the keys and check if they exist in the data
        foreach ($this->keys as $key) {
            if (! array_key_exists($key, $data)) {
                return false;
            }
        }

        return true;
    }
}