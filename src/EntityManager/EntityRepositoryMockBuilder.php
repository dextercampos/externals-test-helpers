<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager;

use Doctrine\ORM\EntityRepository;

class EntityRepositoryMockBuilder extends AbstractMockBuilder
{
    /**
     * Create the expectation for query builder.
     *
     * @param int $count
     * @param null|mixed[] $expected
     * @param null|mixed $return
     * @param null|mixed $exception
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\EntityRepositoryMockBuilder
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
     * Get class to mock.
     *
     * @return string
     */
    protected function getClassToMock(): string
    {
        return EntityRepository::class;
    }
}
