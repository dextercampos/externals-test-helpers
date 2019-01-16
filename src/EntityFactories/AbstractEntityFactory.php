<?php
declare(strict_types=1);

namespace DexterCampos\Externals\Test\Helpers\EntityFactories;

use EoneoPay\Externals\ORM\EntityFactory;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

/**
 * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Faker
 */
abstract class AbstractEntityFactory extends EntityFactory
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * Get faker instance.
     *
     * @return \Faker\Generator
     */
    protected function getFaker(): FakerGenerator
    {
        if ($this->faker !== null) {
            return $this->faker;
        }

        return $this->faker = FakerFactory::create();
    }
}
