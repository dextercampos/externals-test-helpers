<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\Interfaces;

interface AssertInterface
{
    /**
     * Assert expected against actual.
     *
     * @param mixed $actual
     *
     * @return void
     */
    public function assert($actual): void;
}
