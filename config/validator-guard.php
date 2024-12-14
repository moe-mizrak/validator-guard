<?php

use MoeMizrak\ValidatorGuard\Attributes\IntervalGuard;
use MoeMizrak\ValidatorGuard\Tests\Example;

return [
    /**
     * Here add the attributes that are used for Validation Guard
     */
    'attributes' => [
        // Attributes that will be handled before method execution
        'before' => [

        ],
        // Attributes that will be handled after method execution
        'after' => [
            IntervalGuard::class,
        ]
    ],
    /**
     * Here we add all classes that we use attributes validation in order to bind them to ValidatorGuardCore in Service Provider.
     * Basically whenever these classes are resolved by container, we initiate ValidatorGuardCore to mimic them as a wrapper and handle validation.
     */
    'class_list' => [
        Example::class, // This is for testing purpose, can be commented out
    ]
];