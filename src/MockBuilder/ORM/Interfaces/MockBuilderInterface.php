<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\Interfaces;

use Mockery\MockInterface;

interface MockBuilderInterface
{
    /**
     * Build mock.
     *
     * @return \Mockery\MockInterface
     */
    public function build(): MockInterface;
}
