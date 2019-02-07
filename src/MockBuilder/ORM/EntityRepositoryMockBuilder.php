<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Mockery\MockInterface;

/**
 * @method self withException(string $exception)
 * @method self addConfiguration(\Closure $closure)
 *
 * @see \Doctrine\ORM\EntityRepository
 */
class EntityRepositoryMockBuilder extends AbstractMockBuilder
{
    /**
     * Mock should receive createQueryBuilder() call.
     *
     * @param string $alias
     * @param string|null $indexBy
     * @param null|\Doctrine\ORM\QueryBuilder $queryBuilder
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityRepositoryMockBuilder
     */
    public function hasCreateQueryBuilder(
        string $alias,
        string $indexBy = null,
        ?QueryBuilder $queryBuilder = null
    ): self {
        $this->addConfiguration(function (MockInterface $mock) use ($alias, $indexBy, $queryBuilder): void {
            $mock->shouldReceive('createQueryBuilder')->once()->with($alias, $indexBy)->andReturn($queryBuilder);
        });

        return $this;
    }

    /**
     * Mock should receive find() call.
     *
     * @param $id
     * @param null|int $lockMode
     * @param null|int $lockVersion
     * @param null|object $return
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityRepositoryMockBuilder
     */
    public function hasFind($id, ?int $lockMode = null, ?int $lockVersion = null, $return = null): self
    {
        $this->addConfiguration(function (MockInterface $mock) use ($id, $lockMode, $lockVersion, $return): void {
            $mock->shouldReceive('find')->once()->with($id, $lockMode, $lockVersion)->andReturn($return);
        });

        return $this;
    }

    /**
     * Create the expectation for query builder.
     *
     * @param int $count
     * @param null|mixed[] $expected
     * @param null|mixed $return
     * @param null|mixed $exception
     *
     * @return \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityRepositoryMockBuilder
     */
    public function hasQueryBuilder(
        int $count,
        ?array $expected = null,
        $return = null,
        $exception = null
    ): self {
        $this->shouldReceive($count, 'createQueryBuilder', $return, $expected, $exception);

        return $this;
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

    /**
     * Get class to mock.
     *
     * @return string
     */
    protected function getClassToMock(): string
    {
        return EntityRepository::class;
    }
}
