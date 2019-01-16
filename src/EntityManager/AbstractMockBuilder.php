<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager;

use Closure;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Mockery;
use Mockery\ExpectationInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\Assert;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use DexterCampos\Externals\Test\Helpers\EntityManager\Interfaces\AssertInterface;
use DexterCampos\Externals\Test\Helpers\EntityManager\Interfaces\MockBuilderInterface;
use DexterCampos\Externals\Test\Helpers\EntityManager\Interfaces\ReturnSelfInterface;

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
     * @var \Mockery\MockInterface
     */
    private $mock;

    /**
     * AbstractMockBuilder constructor.
     *
     * @param \Mockery\MockInterface|null $mock
     */
    public function __construct(?MockInterface $mock = null)
    {
        $this->mock = $mock ?? Mockery::mock($this->getClassToMock());
    }

    /**
     * Add closure config.
     *
     * @param \Closure $closure
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\AbstractMockBuilder
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
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\AbstractMockBuilder
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
                        throw new \RuntimeException(
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
     * Mock should receive method call and throw exception.
     *
     * @param int $callCount
     * @param string $exception
     * @param string $method
     * @param mixed[]|\Closure $argsOrClosure
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\AbstractMockBuilder
     */
    public function withException(int $callCount, string $exception, string $method, $argsOrClosure): self
    {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $exception, $method, $argsOrClosure): void {
                $mock->shouldReceive($method)->times($callCount)
                    ->with($argsOrClosure)
                    ->andThrow($exception);
            }
        );

        return $this;
    }

    /**
     * Get class to mock.
     *
     * @return string
     */
    abstract protected function getClassToMock(): string;

    /**
     * Assert expected data in entity.
     *
     * @param string $class
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     * @param mixed[]|null $data
     *
     * @return void
     */
    protected function assertExpectedEntity(string $class, EntityInterface $entity, ?array $data = null): void
    {
        Assert::assertInstanceOf($class, $entity);

        if ($data === null) {
            return;
        }

        foreach ($data as $key => $value) {
            $getter = \sprintf('get%s', $key);
            if ($value instanceof AssertInterface) {
                $value->assert($entity->$getter());

                continue;
            }

            Assert::assertSame($value, $entity->$getter());
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
     * Call closure configurations and return mock.
     *
     * @return \Mockery\MockInterface
     */
    final public function build(): MockInterface
    {
        foreach ($this->configuration as $configuration) {
            $configuration($this->mock);
        }

        return $this->mock;
    }
}
