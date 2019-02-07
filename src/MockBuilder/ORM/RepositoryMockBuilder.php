<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM;

use Closure;
use Mockery\MockInterface;

/**
 * Mock builder for RepositoryInterface.
 *
 * @see \EoneoPay\Externals\ORM\Interfaces\RepositoryInterface
 */
class RepositoryMockBuilder extends AbstractMockBuilder
{
    /**
     * Mock should receive custom method call.
     *
     * @param string $method
     * @param int $callCount
     * @param mixed $return
     * @param mixed ...$params
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\RepositoryMockBuilder
     */
    public function has(string $method, int $callCount, $return, ...$params): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($callCount, $method, $return, $params): void {
            if (\count($params) === 1 && $params[0] instanceof Closure) {
                $params = $params[0];
            }

            $mock->shouldReceive($method)->times($callCount)->withArgs($params)->andReturn($return);
        });

        return $this;
    }

    /**
     * Mock should receive count() call.
     *
     * @param int $callCount
     * @param int $return
     * @param mixed[]|null $criteria
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\RepositoryMockBuilder
     */
    public function hasCount(int $callCount, int $return, ?array $criteria = null): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($callCount, $criteria, $return): void {
            $config = $mock->shouldReceive('count')->times($callCount)->andReturn($return);

            if ($criteria === null) {
                $config->withNoArgs();

                return;
            }

            $config->with($criteria);
        });

        return $this;
    }

    /**
     * Mock should receive find() call.
     *
     * @param int $callCount
     * @param string $entityId
     * @param mixed $return
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\RepositoryMockBuilder
     */
    public function hasFind(int $callCount, string $entityId, $return): self
    {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $entityId, $return): void {
                $mock->shouldReceive('find')
                    ->times($callCount)
                    ->with($entityId)
                    ->andReturn($return);
            }
        );

        return $this;
    }

    /**
     * Mock should receive findALl() call.
     *
     * @param int $callCount
     * @param mixed[] $return
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\RepositoryMockBuilder
     */
    public function hasFindAll(int $callCount, array $return): self
    {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $return): void {
                $mock->shouldReceive('findAll')
                    ->times($callCount)
                    ->withNoArgs()
                    ->andReturn($return);
            }
        );

        return $this;
    }

    /**
     * Mock should receive findBy() call.
     *
     * @param int $callCount
     * @param mixed[] $criteria
     * @param mixed[] $return
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\RepositoryMockBuilder
     */
    public function hasFindBy(int $callCount, array $criteria, array $return): self
    {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $criteria, $return): void {
                $mock->shouldReceive('findBy')
                    ->times($callCount)
                    ->with($criteria)
                    ->andReturn($return);
            }
        );

        return $this;
    }

    /**
     * Mock should receive findOneBy() call.
     *
     * @param int $callCount
     * @param mixed[] $criteria
     * @param mixed $return
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\RepositoryMockBuilder
     */
    public function hasFindOneBy(int $callCount, array $criteria, $return): self
    {
        $this->addConfiguration(
            function (MockInterface $mock) use ($callCount, $criteria, $return): void {
                $config = $mock->shouldReceive('findOneBy')
                    ->times($callCount)
                    ->andReturn($return);
                $config->withArgs($this->getAssertArrayClosure($criteria));
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
        return RepositoryInterface::class;
    }

    /**
     * Get available methods that are allowed to be mocked.
     *
     * @return string[]
     */
    protected function getAvailableMethods(): array
    {
        // TODO: Implement getAvailableMethods() method.
    }
}
