<?php

namespace MoeMizrak\ValidatorGuard\Tests;

use MoeMizrak\ValidatorGuard\ValidatorGuardServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            ValidatorGuardServiceProvider::class,
        ];
    }
}