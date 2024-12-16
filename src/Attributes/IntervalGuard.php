<?php

namespace MoeMizrak\ValidatorGuard\Attributes;

use MoeMizrak\ValidatorGuardCore\Contracts\ValidationAttributeInterface;
use MoeMizrak\ValidatorGuardCore\Data\MethodContextData;
use InvalidArgumentException;

/**
 * IntervalGuard attribute for validating method results against interval constraints.
 *
 * Usage examples:
 * #[IntervalGuard(10, '<', 30)] - Ensures method result is between 10 and 30 (exclusive)
 * #[IntervalGuard(10, '<=', 30)] - Ensures method result is between 10 and 30 (inclusive)
 * #[IntervalGuard(10, '>')] - Ensures method result is strictly greater than 10
 * It is repeatable and can be used for cases like #[IntervalGuard(10, '<=')] #[IntervalGuard(30, '>')] means 10<=methodResult<30
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class IntervalGuard implements ValidationAttributeInterface
{
    /**
     * Valid comparison operators
     */
    private const VALID_OPERATORS = ['<', '<=', '==', '>', '>=', '!='];

    /**
     * Constructor validates the provided comparison operator
     */
    public function __construct(
        public float $lowerBound,
        public string $operator = '<=',
        public ?float $upperBound = null,
    ) {
        $this->validateOperator($this->operator);
    }

    /**
     * Validate the comparison operator
     *
     * @throws InvalidArgumentException If an invalid operator is provided
     */
    private function validateOperator(string $operator): void
    {
        if (! in_array($operator, self::VALID_OPERATORS, true)) {
            throw new InvalidArgumentException(
                "Invalid comparison operator '{$operator}'. " .
                'Allowed operators: ' . implode(', ', self::VALID_OPERATORS)
            );
        }
    }

    /**
     * @inheritDoc
     */
    final public function handle(MethodContextData $methodContextData): bool
    {
        $result = $methodContextData->methodResponse;

        // Single bound evaluation
        if ($this->upperBound === null) {
            return $this->evaluateSingleBound($result);
        }

        // Double bound evaluation
        return $this->evaluateDoubleBound($result);
    }

    /**
     * Evaluate method result against a single bound
     */
    private function evaluateSingleBound(float $result): bool
    {
        return match ($this->operator) {
            '<'   => $this->lowerBound < $result,
            '<='  => $this->lowerBound <= $result,
            '=='  => $this->lowerBound == $result,
            '>'   => $this->lowerBound > $result,
            '>='  => $this->lowerBound >= $result,
            '!='  => $this->lowerBound != $result,
            default => false,
        };
    }

    /**
     * Evaluate method result against two bounds
     */
    private function evaluateDoubleBound(float $result): bool
    {
        return match ($this->operator) {
            '<'   => $this->lowerBound < $result && $result < $this->upperBound,
            '<='  => $this->lowerBound <= $result && $result <= $this->upperBound,
            '>'   => $this->lowerBound > $result && $result > $this->upperBound,
            '>='  => $this->lowerBound >= $result && $result >= $this->upperBound,
            '!='  => $this->lowerBound != $result || $result != $this->upperBound,
            default => false,
        };
    }
}