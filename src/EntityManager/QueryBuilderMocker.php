<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager;

use Doctrine\ORM\QueryBuilder;

/**
 * @method self hasAddSelect(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasAndWhere(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasExpr(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasGroupBy(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasGetQuery(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasInnerJoin(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasLeftJoin(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasOrderBy(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasOrWhere(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasSelect(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasSetParameter(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasSetParameters(int $count, ?array $expectation = null, $return = null, $exception = null)
 * @method self hasWhere(int $count, ?array $expectation = null, $return = null, $exception = null)
 */
class QueryBuilderMocker extends AbstractMockBuilder
{
    /**
     * Known methods use in query builder.
     *
     * @var string[]
     */
    private static $constantMethod = [
        'addSelect',
        'andWhere',
        'expr',
        'getQuery',
        'groupBy',
        'innerJoin',
        'leftJoin',
        'orderBy',
        'orWhere',
        'select',
        'setParameter',
        'setParameters',
        'where'
    ];

    /**
     * Adding select in query builder mock.
     *
     * @param string $name
     * @param mixed[] $parameter
     *
     * @return \DexterCampos\Externals\Test\Helpers\EntityManager\QueryBuilderMocker
     */
    public function __call(string $name, array $parameter): self
    {
        $method = \lcfirst(\substr($name, 3));
        if (\in_array($method, self::$constantMethod) === false) {
            throw new \RuntimeException(\sprintf('%s not in common method', $method));
        }

        $exception = isset($parameter[3]) === false ? null : $parameter[3];
        $this->shouldReceive($parameter[0], $method, $parameter[2], $parameter[1], $exception);

        return $this;
    }

    /**
     * Get class to mock.
     *
     * @return string
     */
    protected function getClassToMock(): string
    {
        return QueryBuilder::class;
    }
}
