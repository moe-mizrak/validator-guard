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

    #[Test]
    public function it_tests_when_array_key_does_NOT_exist_in_response()
    {
        /* SETUP */
        $service = valguard($this->example);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $service->arrayKeysExistFailureMethod('random');
    }

    #[Test]
    public function it_tests_when_array_key_does_NOT_exist_in_param()
    {
        /* SETUP */
        $arrayParam = [
            'firstParamKey' => 22,
            'secondParamKey' => true,
            'thirdParamKey' => 'randomString',
        ];
        $service = valguard($this->example);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $service->arrayKeysExistFailureParamMethod(35, $arrayParam);
    }

    #[Test]
    public function it_tests_when_array_keys_exist_both_in_param_and_in_method_result()
    {
        /* SETUP */
        $arrayParam = [
            'firstKey' => 22,
            'secondKey' => true,
            'thirdKey' => 'randomString',
        ];
        $methodResult = [
            'firstKey' => 'firstValue',
            'secondKey' => ['randomString' => 'randomValue'],
        ];
        $service = valguard($this->example);

        /* EXECUTE */
        $result = $service->arrayKeysExistBothParamAndResultMethod(35, $arrayParam);

        /* ASSERT */
        $this->assertEquals($result, $methodResult);
    }

    #[Test]
    public function it_tests_allowed_values_attribute_in_case_of_failure()
    {
        /* SETUP */
        $service = valguard($this->example);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $service->allowedValuesMethod('notAllowedString', 55);
    }

    #[Test]
    public function it_tests_allowed_values_attribute_in_case_validation_passes()
    {
        /* SETUP */
        $intValue = 55;
        $stringValue = 'allowedString';
        $service = valguard($this->example);

        /* EXECUTE */
        $result = $service->allowedValuesMethod($stringValue, $intValue);

        /* ASSERT */
        $this->assertEquals($result, $intValue);
    }

    #[Test]
    public function it_tests_multiple_allowed_values_attribute_in_case_validation_passes()
    {
        /* SETUP */
        $firstStringValue = 'firstAllowedString';
        $secondStringValue = 'secondAllowedString';
        $service = valguard($this->example);

        /* EXECUTE */
        $result = $service->multipleAllowedValuesMethod($firstStringValue, $secondStringValue);

        /* ASSERT */
        $this->assertEquals($result, $firstStringValue . ' ' . $secondStringValue);
    }

    #[Test]
    public function it_tests_multiple_allowed_values_attribute_in_case_of_failure()
    {
        /* SETUP */
        $firstStringValue = 'firstAllowedString';
        $secondStringValue = 'invalidString';
        $service = valguard($this->example);
        $this->expectException(ValidatorGuardCoreException::class);

        /* EXECUTE */
        $service->multipleAllowedValuesMethod($firstStringValue, $secondStringValue);
    }
}