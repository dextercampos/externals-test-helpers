<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\Tests\MockBuilder\ORM;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use stdClass;
use StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityRepositoryMockBuilder;
use StepTheFkUp\DoctrineTestHelpers\Tests\TestCase;

/**
 * @covers \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityRepositoryMockBuilder
 */
class EntityRepositoryMockBuilderTest extends TestCase
{
    /**
     * Test hasCreateQueryBuilder adds expectation to call createQueryBuilder method in entity repository.
     *
     * @return void
     */
    public function testHasCreateQueryBuilder(): void
    {
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $this->mock(QueryBuilder::class);

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = (new EntityRepositoryMockBuilder())
            ->hasCreateQueryBuilder('alias', 'index-by', $queryBuilder)
            ->build();

        $this->assertEquals($queryBuilder, $repository->createQueryBuilder('alias', 'index-by'));
    }

    /**
     * Test hasFind adds expectation to call find method in entity repository.
     *\
     * @return void
     */
    public function testHasFind(): void
    {
        $expectedReturn = new stdClass();

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = (new EntityRepositoryMockBuilder())
            ->hasFind('id', LockMode::NONE, 1, $expectedReturn)
            ->build();

        $this->assertEquals($expectedReturn, $repository->find('id', LockMode::NONE, 1));
    }
}
