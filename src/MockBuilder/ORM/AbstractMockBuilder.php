<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM;

use Closure;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Mockery;
use Mockery\ExpectationInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\Assert;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\Interfaces\AssertInterface;
use StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\Interfaces\MockBuilderInterface;
use StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\Interfaces\ReturnSelfInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Suppress due to dependency
 * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Faker
 */
abstract class AbstractMockBuilder implements MockBuilderInterface
{
    /**
     * @var \Closure[]
     */
    private $configuration = [];

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * Add closure config.
     *
     * @param \Closure $closure
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\AbstractMockBuilder
     */
    public function addConfiguration(Closure $closure): self
    {
        $this->configuration[] = $closure;

        return $this;
    }

    /**
     * Assert parameters passed to function.
     *
     * @param null|mixed[] $expectations
     *
     * @return \Closure
     */
    public function assertWithExpectation(?array $expectations): Closure
    {
        return function () use ($expectations): ?bool {
            if ($expectations === null) {
                return null;
            }

            return \func_get_args() === $expectations;
        };
    }

    /**
     * Customize method for calling shouldReceive.
     *
     * @param int $count
     * @param string $methodName
     * @param null|mixed $return
     * @param null|mixed[] $expected
     * @param null|mixed $exception
     *
     * @return self
     */
    public function shouldReceive(
        int $count,
        string $methodName,
        $return,
        ?array $expected = null,
        $exception = null
    ): self {
        $this->addConfiguration(
            function (MockInterface $mock) use ($count, $methodName, $expected, $return, $exception): void {
                $shouldReceived = $mock->shouldReceive($methodName)
                    ->times($count);

                if ($expected !== null) {
                    $shouldReceived->withArgs($this->assertWithExpectation($expected));
                }

                if ($expected === null) {
                    $shouldReceived->withNoArgs();
                }

                if ($exception !== null) {
                    $code = 0;
                    $message = '';

                    // Check first if variable exception is array
                    if (\is_array($exception) === true) {
                        $handler = $exception;

                        // Get the exception name
                        $keys = \array_keys($exception);
                        $exception = \reset($keys);

                        $code = $handler[$exception]['code'];
                        $message = $handler[$exception]['message'];
                    }

                    if ($this->validateKey($exception ?? '') === false) {
                        throw new RuntimeException(
                            \sprintf('Something went wrong while mocking throws %s', $exception)
                        );
                    }

                    $shouldReceived->andThrows($exception, $message, $code);
                }

                if ($exception === null) {
                    if ($return instanceof ReturnSelfInterface) {
                        $return->returnSelf($shouldReceived);

                        return; // Return to exit the mocking
                    }

                    $shouldReceived->andReturn($return);
                }
            }
        );

        return $this;
    }

    /**
     * Validate if key is a string value and a valid class.
     *
     * @param string $key
     *
     * @return bool
     */
    public function validateKey(string $key): bool
    {
        return \is_string($key) === true || \class_exists($key) === true;
    }

    /**
     * @param int $count
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\AbstractMockBuilder
     */
    public function withCount(int $count): self
    {
        $this->getConfiguration()->times($count);

        return $this;
    }

    /**
     * Mock should throw exception from latest mock method called.
     *
     * @param string $exception
     *
     * @return self
     */
    public function withException(string $exception): self
    {
        $this->getConfiguration()->andThrow($exception);

        return $this;
    }

    /**
     * Get available methods that are allowed to be mocked.
     *
     * @return string[]
     */
    abstract protected function getAvailableMethods(): array;

    /**
     * Get class to mock.
     *
     * @return string
     */
    abstract protected function getClassToMock(): string;

    /**
     * Assert expected data in entity.
     *
     * @param string $className
     * @param mixed $object
     * @param mixed[]|null $data
     *
     * @return void
     */
    protected function assertExpectedEntity(string $className, $object, ?array $data = null): void
    {
        Assert::assertInstanceOf($className, $object);

        if ($data === null) {
            return;
        }

        foreach ($data as $key => $expected) {
            $actual = $this->getValue($key, $object);

            if ($expected instanceof AssertInterface) {
                $expected->assert($actual);

                continue;
            }

            Assert::assertSame($expected, $actual);
        }
    }

    /**
     * Throw exception or return null.
     *
     * @param \Mockery\ExpectationInterface $expectation
     * @param null|string $exception
     *
     * @return void
     */
    protected function expectExceptionOrReturnNull(ExpectationInterface $expectation, ?string $exception = null): void
    {
        if ($exception !== null) {
            /** @noinspection PhpUndefinedMethodInspection */
            $expectation->andThrow($exception);

            return;
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $expectation->andReturnNull();
    }

    /**
     * Assert array of values in closure.
     *
     * @param mixed[] $values
     *
     * @return \Closure
     */
    protected function getAssertArrayClosure(array $values): Closure
    {
        return function (array $args) use ($values): bool {
            if (\count($args) !== \count($values)) {
                return false;
            }

            foreach ($values as $key => $value) {
                if (\array_key_exists($key, $args) === false) {
                    return false;
                }

                if ($value instanceof AssertInterface) {
                    $value->assert($args[$key]);

                    continue;
                }

                Assert::assertSame($value, $args[$key]);
            }

            return true;
        };
    }

    /**
     * Get faker.
     *
     * @return \Faker\Generator
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    protected function getFaker(): Generator
    {
        if ($this->faker !== null) {
            return $this->faker;
        }

        return $this->faker = FakerFactory::create();
    }

    /**
     * Convert protected/private method to public.
     *
     * @param string $className
     * @param string $methodName
     *
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException
     */
    protected function getMethodAsPublic(string $className, string $methodName): ReflectionMethod
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * Convert protected/private property to public.
     *
     * @param string $className
     * @param string $propertyName
     *
     * @return \ReflectionProperty
     *
     * @throws \ReflectionException
     */
    protected function getPropertyAsPublic(string $className, string $propertyName): ReflectionProperty
    {
        $class = new ReflectionClass($className);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Get configuration by index. Returns last config if null passed.
     *
     * @param null|int $index
     *
     * @return \Mockery\Expectation
     */
    private function getConfiguration(?int $index = null): Mockery\Expectation
    {
        return $this->configuration[$index ?? (\count($this->configuration) - 1)];
    }

    /**
     * Get value from object using getter or property access.
     *
     * @param string $key
     * @param mixed $object
     *
     * @return mixed
     */
    private function getValue(string $key, $object)
    {
        $getter = \sprintf('get%s', $key);
        if (\method_exists($object, $getter) === true) {
            return $object->$getter();
        }

        if (\property_exists($object, $key) === true) {
            return $object->{$key};
        }
        throw new RuntimeException('Property or getter does not exist in object.');
    }

    /**
     * Call closure configurations and return mock.
     *
     * @return \Mockery\MockInterface
     */
    final public function build(): MockInterface
    {
        $mock = Mockery::mock($this->getClassToMock());

        foreach ($this->configuration as $configuration) {
            $configuration($mock);
        }

        return $mock;
    }
}
