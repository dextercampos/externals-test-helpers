<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM;

use Closure;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\MockInterface;

/**
 * @method self withException(string $exception)
 * @method self addConfiguration(\Closure $closure)
 *
 * @see \Doctrine\Common\Persistence\ObjectManager
 * @see \Doctrine\ORM\EntityManagerInterface
 */
class EntityManagerMockBuilder extends AbstractMockBuilder
{

    /**
     * Mock should receive clear call.
     *
     * @param null|string $objectName
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityManagerMockBuilder
     */
    public function hasClear(?string $objectName = null): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($objectName): void {
            $mock->shouldReceive('clear')->once()->with($objectName)->andReturnNull();
        });

        return $this;
    }

    /**
     * Mock should receive clear call.
     *
     * @param object $object
     * @param bool $return
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityManagerMockBuilder
     */
    public function hasContains($object, bool $return): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($object, $return): void {
            $mock->shouldReceive('contains')->once()->with($object)->andReturn($return);
        });

        return $this;
    }

    /**
     * Mock should receive clear call.
     *
     * @param object $object
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityManagerMockBuilder
     */
    public function hasDetach($object): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($object): void {
            $mock->shouldReceive('contains')->once()->with($object)->andReturnNull();
        });

        return $this;
    }

    /**
     * Mock should receive clear call.
     *
     * @param string $className
     * @param mixed $id
     * @param mixed $return
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityManagerMockBuilder
     */
    public function hasFind(string $className, $id, $return): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($className, $id, $return): void {
            $mock->shouldReceive('find')->once()->with($className, $id)->andReturn($return);
        });

        return $this;
    }

    /**
     * Mock should receive flush() call.
     *
     * @param int $callCount
     * @param null|string $exception
     *
     * @return self
     *
     * @see \Doctrine\ORM\EntityManagerInterface::flush()
     */
    public function hasFlush(int $callCount, ?string $exception = null): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($callCount, $exception): void {
            $config = $mock->shouldReceive('flush')
                ->times($callCount)
                ->withNoArgs();

            $this->expectExceptionOrReturnNull($config, $exception);
        });

        return $this;
    }

    /**
     * Mock should receive getRepository() call.
     *
     * @param string $className
     * @param null|\Doctrine\Common\Persistence\ObjectRepository $repository
     *
     * @return self
     *
     * @see \Doctrine\ORM\EntityManagerInterface::getRepository()
     */
    public function hasGetRepository(
        string $className,
        ?ObjectRepository $repository
    ): self {
        $this->addConfiguration(
            function (MockInterface $mock) use ($className, $repository): void {
                $config = $mock->shouldReceive('getRepository')
                    ->once()
                    ->with($className);

                if ($repository !== null) {
                    $config->andReturn($repository);

                    return;
                }

                $config->andReturnNull();
            }
        );

        return $this;
    }

    /**
     * Mock should receive persist() call.
     *
     * @param \Closure|object $objectOrClosure
     *
     * @return self
     *
     * @see \Doctrine\ORM\EntityManagerInterface::persist()
     */
    public function hasPersist($objectOrClosure): self
    {
        $this->addConfiguration(
            function (MockInterface $mock) use ($objectOrClosure): void {
                $expectation = $mock->shouldReceive('persist')
                    ->once()
                    ->andReturnNull();
                if ($objectOrClosure instanceof Closure) {
                    $expectation->withArgs($objectOrClosure);

                    return;
                }
                $expectation->with($objectOrClosure);
            }
        );

        return $this;
    }

    /**
     * Mock should receive remove() call.
     *
     * @param \Closure|object $objectOrClosure
     *
     * @return self
     *
     * @see \Doctrine\ORM\EntityManagerInterface::persist()
     */
    public function hasRemove($objectOrClosure): self
    {
        $this->addConfiguration(
            function (MockInterface $mock) use ($objectOrClosure): void {
                $expectation = $mock->shouldReceive('remove')
                    ->once()
                    ->andReturnNull();
                if ($objectOrClosure instanceof Closure) {
                    $expectation->withArgs($objectOrClosure);

                    return;
                }
                $expectation->with($objectOrClosure);
            }
        );

        return $this;
    }

    protected function getAvailableMethods(): array
    {
        return [
            'contains',
            'detach',
            'find',
            'flush',
            'getClassMetadata',
            'getMetadataFactory',
            'getRepository',
            'initializeObject',
            'merge',
            'persist',
            'refresh',
            'remove',
        ];
    }

    /**
     * Get class to mock.
     *
     * @return string
     */
    protected function getClassToMock(): string
    {
        return EntityManagerInterface::class;
    }
}
