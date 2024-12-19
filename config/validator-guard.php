<?php

use MoeMizrak\ValidatorGuard\Attributes\AllowedValuesGuard;
use MoeMizrak\ValidatorGuard\Attributes\ArrayKeysExistGuard;
use MoeMizrak\ValidatorGuard\Attributes\CallbackGuard;
use MoeMizrak\ValidatorGuard\Attributes\DateGuard;
use MoeMizrak\ValidatorGuard\Attributes\IntervalGuard;
use MoeMizrak\ValidatorGuard\Tests\Example;

return [
    /**
     * Here add the attributes that are used for Validation Guard
     */
    'attributes' => [
        // Attributes that will be handled before method execution
        'before' => [
            AllowedValuesGuard::class,
            DateGuard::class,
            CallbackGuard::class,
        ],
        // Attributes that will be handled after method execution
        'after' => [
            IntervalGuard::class,
            ArrayKeysExistGuard::class,
        ]
    ],

    /**
     * Here we add all classes that we use attribute validation in order to bind them to ValidatorGuardCore in Service Provider.
     * Basically whenever these classes are resolved by container, we initiate ValidatorGuardCore to mimic them as a wrapper and handle validation.
     */
    'class_list' => [
        Example::class, // This is for testing purpose, can be removed !
    ],

    /**
     * Enable throwing exceptions in case of validation failure
     */
    'throw_exceptions' => env('VALIDATOR_GUARD_THROW_EXCEPTIONS', true),

    /**
     * Enable logging exceptions in case of validation failure
     */
    'log_exceptions' => env('VALIDATOR_GUARD_LOG_EXCEPTIONS', false),

    /**
     * Set an option for default channel for logging so that it can be configured when needed.
     */
    'log_channel' => env('VALIDATOR_GUARD_LOG_CHANNEL', 'stack'),
];