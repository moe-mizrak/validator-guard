<?php

namespace MoeMizrak\ValidatorGuard\Attributes;


use Carbon\Carbon;
use Illuminate\Support\Arr;
use MoeMizrak\ValidatorGuardCore\Contracts\ValidationAttributeInterface;
use MoeMizrak\ValidatorGuardCore\Data\MethodContextData;

/**
 * DateGuard checks whether the given date parameter is in the future, past, weekdays, weekends, today, tomorrow or between two dates and so on.
 *
 * Usage examples:
 * #[DateGuard(
 *      0,
 *      boundary: DateGuard::FUTURE
 * )]
 *
 * #[DateGuard(
 *      1,
 *      boundary: DateGuard::BETWEEN,
 *      range: ['lowerBound' => '2021-01-01', 'upperBound' => '2021-12-31']
 * )]
 *
 * @attribute DateGuard
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
final readonly class DateGuard implements ValidationAttributeInterface
{
    /*
     * Allowed boundaries:
     */
    public const FUTURE = 'future';
    public const FUTURE_OR_PRESENT = 'future_or_present';
    public const PAST = 'past';
    public const PAST_OR_PRESENT = 'past_or_present';
    public const BETWEEN = 'between';
    public const NOT_BETWEEN = 'not_between';
    public const WEEKDAYS = 'weekdays';
    public const WEEKENDS = 'weekends';
    public const TODAY = 'today';
    public const TOMORROW = 'tomorrow';

    public function __construct(
        private int $paramPosition,
        private ?string $boundary = null,
        private ?array $range = null,
    ) {
        $this->validateBoundary($boundary);
        $this->validateRange($range);
    }

    /**
     * @inheritDoc
     */
    public function handle(MethodContextData $methodContextData): bool
    {
        // Retrieve the date parameter from the method context data.
        $params = $methodContextData->params;
        $dateParam = Arr::get($params, $this->paramPosition);
        $date = $dateParam ? Carbon::parse($dateParam) : null;

        $lowerBound = null;
        $upperBound = null;
        $range = $this->range;
        // If the range is not null, get the lower and upper bounds.
        if ($range) {
            $lowerBound = Arr::get($range, 'lowerBound');
            $upperBound = Arr::get($range, 'upperBound');
        }

        // Get the current date and time.
        $now = Carbon::now();

        // If given date data is null then return true because if parameter is optional, it can be null which means validation should pass.
        if (! $date) {
            return true;
        }

        return match ($this->boundary) {
            self::FUTURE => $date->gt($now), // Check if $date is in the future
            self::PAST => $date->lt($now), // Check if $date is in the past
            self::FUTURE_OR_PRESENT => $date->gte($now), // Check if $date is future or now
            self::PAST_OR_PRESENT => $date->lte($now), // Check if $date is past or now
            self::BETWEEN => ! is_null($range)
                && $date->gt($lowerBound)
                && $date->lt($upperBound), // Check if $date is between, range array has to be provided !
            self::NOT_BETWEEN => ! is_null($range)
                && ! ($date->gt($lowerBound) && $date->lt($upperBound)), // Check if $date is not between, range array has to be provided !
            self::WEEKDAYS => $date->isWeekday(), // Check if $date is a weekday
            self::WEEKENDS => $date->isWeekend(), // Check if $date is a weekend
            self::TODAY => $date->isToday(), // Check if $date is today
            self::TOMORROW => $date->isTomorrow(), // Check if $date is tomorrow
            default => false,
        };
    }

    /**
     * Validate the boundary if it is within required values.
     *
     * @param string|null $boundary
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateBoundary(?string $boundary): void
    {
        if ($boundary !== null && ! in_array($boundary, [
                self::FUTURE,
                self::FUTURE_OR_PRESENT,
                self::PAST,
                self::PAST_OR_PRESENT,
                self::BETWEEN,
                self::NOT_BETWEEN,
                self::WEEKDAYS,
                self::WEEKENDS,
                self::TODAY,
                self::TOMORROW,
            ], true)) {
            throw new \InvalidArgumentException('Invalid boundary value: '. $boundary);
        }
    }

    /**
     * Validate the range array.
     *
     * @param array|null $range
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateRange(?array $range): void
    {
        // If the range is null, return.
        if ($range === null) {
            return;
        }

        // Check if the range array has the required keys.
        if (! array_key_exists('lowerBound', $range) || ! array_key_exists('upperBound', $range)) {
            throw new \InvalidArgumentException('The "range" array must have "lowerBound" and "upperBound" keys.');
        }
    }
}