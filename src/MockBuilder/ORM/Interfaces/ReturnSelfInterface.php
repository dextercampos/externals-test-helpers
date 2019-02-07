<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\Interfaces;

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
