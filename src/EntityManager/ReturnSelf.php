<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager;

use Mockery\ExpectationInterface;
use DexterCampos\Externals\Test\Helpers\EntityManager\Interfaces\ReturnSelfInterface;

class ReturnSelf implements ReturnSelfInterface
{
    /**
     * Returns self.
     *
     * @param \Mockery\ExpectationInterface $shouldReceive
     *
     * @return \Mockery\ExpectationInterface
     */
    public function returnSelf(ExpectationInterface $shouldReceive): ExpectationInterface
    {
        /**
         * @var \Mockery\Expectation $shouldReceive
         */
        return $shouldReceive->andReturnSelf();
    }
}
