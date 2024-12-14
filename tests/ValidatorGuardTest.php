<?php

namespace MoeMizrak\ValidatorGuard\Tests;

use MoeMizrak\ValidatorGuardCore\Exceptions\ValidatorGuardCoreException;
use PHPUnit\Framework\Attributes\Test;

class ValidatorGuardTest extends TestCase
{
    private Example $example;

    public function setUp(): void
    {
        parent::setUp();

        $this->example = new Example();
    }

    #[Test]
    public function it_tests_if_validator_guard_core_works_as_container_resolved_when_validation_failed(){
        /* SETUP */
        $intValue = 20;
        $example = app(Example::class);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $example->smallerThanMethod($intValue);
    }

    #[Test]
    public function it_tests_if_validator_guard_core_works_as_container_resolved()
    {
        /* SETUP */
        $intValue = 2;
        $example = app(Example::class);

        /* EXECUTE */
        $result = $example->smallerThanMethod($intValue);

        /* ASSERT */
        $this->assertEquals($result, $intValue);
    }

    #[Test]
    public function it_tests_if_validator_guard_core_works_with_helper_valguard_when_validation_failed(){
        /* SETUP */
        $intValue = 20;
        $service = valguard($this->example);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $service->smallerThanMethod($intValue);
    }

    #[Test]
    public function it_tests_if_validator_guard_core_works_with_helper_valguard()
    {
        /* SETUP */
        $intValue = 2;
        $service = valguard($this->example);

        /* EXECUTE */
        $result = $service->smallerThanMethod($intValue);

        /* ASSERT */
        $this->assertEquals($result, $intValue);
    }

    #[Test]
    public function it_tests_interval_guard_repeatable_case()
    {
        /* SETUP */
        $intValue = 20;
        $service = valguard($this->example);

        /* EXECUTE */
        $result = $service->repeatableMethod($intValue);

        /* ASSERT */
        $this->assertEquals($result, $intValue);
    }

    #[Test]
    public function it_tests_interval_guard_repeatable_case_validation_fails()
    {
        /* SETUP */
        $intValue = 2;
        $service = valguard($this->example);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $service->repeatableMethod($intValue);
    }

    #[Test]
    public function it_tests_interval_guard_in_between_case()
    {
        /* SETUP */
        $intValue = 20;
        $service = valguard($this->example);

        /* EXECUTE */
        $result = $service->inBetweenMethod($intValue);

        /* ASSERT */
        $this->assertEquals($result, $intValue);
    }

    #[Test]
    public function it_tests_interval_guard_in_between_case_validation_fails()
    {
        /* SETUP */
        $intValue = 40;
        $service = valguard($this->example);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $service->inBetweenMethod($intValue);
    }
}