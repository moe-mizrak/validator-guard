<?php

namespace MoeMizrak\ValidatorGuard\Attributes;

use MoeMizrak\ValidatorGuardCore\Contracts\ValidationAttributeInterface;
use MoeMizrak\ValidatorGuardCore\Data\MethodContextData;
use ReflectionClass;

/**
 * CallbackGuard attribute for making a call to given class method including parameters and comparing result with expected result.
 *
 * Usage examples:
 * #[CallbackGuard(
 *      className: Example::class,
 *      methodName: 'objectResultMethod',
 *      expectedResult: new MethodContextData([
 *          'params' => [],
 *          'methodName' => 'random',
 * '        'className' => Example::class,
 *      ])
 * )]
 *
 * #[CallbackGuard(
 *      className: Example::class,
 *      methodName: 'boolResultMethod',
 *      expectedResult: false
 * )]
 *
 * #[CallbackGuard(
 *      className: Example::class,
 *      methodName: 'voidResultMethod',
 *      params: [100],
 * )]
 *
 * @attribute CallbackGuard
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class CallbackGuard implements ValidationAttributeInterface
{
    public function __construct(
        private string $className,
        private string $methodName,
        private ?array $params = [],
        private mixed $expectedResult = null
    ) {}

    /**
     * @inheritDoc
     */
    public function handle(MethodContextData $methodContextData): bool
    {
        // Get a reflection class instance
        $class = new ReflectionClass($this->className);
        // Instantiate an object of the class
        $instance = $class->newInstance();
        // Get a reflection method instance
        $method = $class->getMethod($this->methodName);
        // Call the method with the provided parameters
        $result =  $method->invoke($instance, ...$this->params);

        if (is_object($this->expectedResult)) {
            return $this->validateObject($result, $this->expectedResult);
        }

        return $result === $this->expectedResult;
    }

    /**
     * Validate two objects by serializing them.
     *
     * @param mixed $firstObject
     * @param mixed $secondObject
     *
     * @return bool
     */
    private function validateObject(mixed $firstObject, mixed $secondObject): bool
    {
        return serialize($firstObject) === serialize($secondObject);
    }
}