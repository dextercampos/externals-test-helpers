<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityManager\Interfaces;

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
