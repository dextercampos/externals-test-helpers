<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager\Interfaces;

use Mockery\ExpectationInterface;

interface ReturnSelfInterface
{
    /**
     * Returns self.
     *
     * @param \Mockery\ExpectationInterface $expectation
     *
     * @return \Mockery\ExpectationInterface
     */
    public function returnSelf(ExpectationInterface $expectation): ExpectationInterface;
}
