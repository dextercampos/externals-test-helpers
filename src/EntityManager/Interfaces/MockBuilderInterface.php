<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager\Interfaces;

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
