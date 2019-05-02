<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager;

use Closure;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Mockery\MockInterface;

/**
 * Mock builder for EntityManagerInterface.
 *
 * @see \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
 */
class EntityManagerMockBuilder extends AbstractMockBuilder
{
    /**
     * Mock should receive flush() call.
     *
     * @param int $callCount
     * @param null|mixed $exception
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\EntityManagerMockBuilder
     *
     * @see \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface::flush()
     */
    public function hasFlush(int $callCount, $exception = null): self
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
     * @param int $callCount
     * @param string $entityClass
     * @param null|mixed $repository
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\EntityManagerMockBuilder
     *
     * @see \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface::getRepository()
     */
    public function hasGetRepository(
        int $callCount,
        string $entityClass,
        $repository = null
    ): self {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $entityClass, $repository): void {
                $config = $mock->shouldReceive('getRepository')
                    ->times($callCount)
                    ->with($entityClass);

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
     * Mock should receive multiple getRepository() calls using entity classes.
     *
     * @param string[] $entityClasses
     * @param null|mixed $repository
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\EntityManagerMockBuilder
     */
    public function hasMultipleGetRepository(array $entityClasses, $repository = null): self
    {
        foreach ($entityClasses as $entityClass) {
            $this->hasGetRepository(1, $entityClass, $repository);
        }

        return $this;
    }

    /**
     * Mock should receive persist() call.
     *
     * @param int $callCount
     * @param string $class
     * @param mixed[]|\EoneoPay\Externals\ORM\Interfaces\EntityInterface|\Closure|null $expectedData
     * @param null|mixed $exception
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\EntityManagerMockBuilder
     *
     * @see \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface::persist()
     */
    public function hasPersist(
        int $callCount,
        string $class,
        $expectedData = null,
        $exception = null
    ): self {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $class, $expectedData, $exception): void {
                $config = $mock->shouldReceive('persist')
                    ->times($callCount);

                $this->expectExceptionOrReturnNull($config, $exception);

                if ($expectedData instanceof EntityInterface) {
                    $config->with($expectedData);

                    return;
                }

                if ($expectedData instanceof Closure) {
                    $config->withArgs($expectedData);

                    return;
                }

                $config->withArgs(function ($entity) use ($class, $expectedData): bool {
                    $this->assertExpectedEntity($class, $entity, $expectedData);

                    // Actual persist function generates ID.
                    $this->setEntityId($entity);

                    return true;
                });
            }
        );

        return $this;
    }

    /**
     * Mock should receive withRemove() call.
     *
     * @param int $callCount
     * @param string $class
     * @param mixed $expectedData
     * @param null|mixed $exception
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\EntityManagerMockBuilder
     *
     * @see \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface::remove()
     */
    public function hasRemove(
        int $callCount,
        string $class,
        $expectedData,
        $exception = null
    ): self {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $class, $expectedData, $exception): void {
                // Always called once since we don't want to remove the same entity twice.
                $config = $mock->shouldReceive('remove')->times($callCount);

                $this->expectExceptionOrReturnNull($config, $exception);

                if ($expectedData instanceof EntityInterface) {
                    $config->with($expectedData);

                    return;
                }
                $config->withArgs(function ($entity) use ($class, $expectedData): bool {
                    $this->assertExpectedEntity($class, $entity, $expectedData);

                    return true;
                });
            }
        );

        return $this;
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

    /**
     * Set entity id for assertion purposes.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    private function setEntityId(EntityInterface $entity): void
    {
        $getIdPropertyMethod = $this->getMethodAsPublic(\get_class($entity), 'getIdProperty');
        $idGetter = \sprintf('get%s', (string)$getIdPropertyMethod->invoke($entity));

        if ($entity->{$idGetter}() !== null) {
            return;
        }
        $setPropertyMethod = $this->getMethodAsPublic(\get_class($entity), 'set');

        $setPropertyMethod->invoke($entity, $getIdPropertyMethod->invoke($entity), $this->getFaker()->uuid);
    }
}
