<?php
declare(strict_types=1);

namespace StepTheFkUp\DoctrineTestHelpers\Tests\MockBuilder\ORM;

use Doctrine\Common\Persistence\ObjectRepository;
use stdClass;
use StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityManagerMockBuilder;
use StepTheFkUp\DoctrineTestHelpers\Tests\TestCase;

/**
 * @covers \StepTheFkUp\DoctrineTestHelpers\MockBuilder\ORM\EntityManagerMockBuilder
 */
class EntityManagerMockBuilderTest extends TestCase
{
    /**
     * Test hasClear adds expectation to call clear method in entity manager.
     *
     * @return void
     */
    public function testHasClear(): void
    {
        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasClear('objectNameToAssert');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($entityManager->clear('objectNameToAssert'));
    }

    /**
     * Test hasContains adds expectation to call contains method in entity manager.
     *
     * @return void
     */
    public function testHasContains(): void
    {
        $expectedObject = new stdClass();
        $expectedResult = true;
        $mockBuilder = (new EntityManagerMockBuilder())
            ->hasContains($expectedObject, $expectedResult);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        $this->assertEquals($expectedResult, $entityManager->contains($expectedObject));
    }

    /**
     * Test hasDetach adds expectation to call detach method in entity manager.
     *
     * @return void
     */
    public function testHasDetach(): void
    {
        $expectedObject = new stdClass();
        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasDetach($expectedObject);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        $this->assertNull($entityManager->contains($expectedObject));
    }

    /**
     * Test hasFind adds expectation to call find method in entity manager.
     *
     * @return void
     */
    public function testHasFind(): void
    {
        $expectedObject = new stdClass();
        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasFind('ClassName', 'object-id', $expectedObject);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        $this->assertEquals($expectedObject, $entityManager->find('ClassName', 'object-id'));
    }

    /**
     * Test hasFlush adds expectation to call flush method in entity manager.
     *
     * @return void
     */
    public function testHasFlush(): void
    {
        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasFlush(1);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($entityManager->flush());
    }

    /**
     * Test hasGetRepository adds expectation to call getRepository method in entity manager.
     *
     * @return void
     */
    public function testHasGetRepository(): void
    {
        /** @var \Doctrine\Common\Persistence\ObjectRepository $repository */
        $repository = $this->mock(ObjectRepository::class);

        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasGetRepository('ClassName', $repository);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertInstanceOf(ObjectRepository::class, $entityManager->getRepository('ClassName'));
    }

    /**
     * Test hasGetRepository adds expectation to call getRepository method in entity manager but returns null.
     *
     * @return void
     */
    public function testHasGetRepositoryExpectsNullRepository(): void
    {
        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasGetRepository('ClassName', null);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($entityManager->getRepository('ClassName'));
    }

    /**
     * Test hasPersist adds expectation to call persist method in entity manager using a closure to assert data
     * if the object is not accessible from the test.
     *
     * @return void
     */
    public function testHasPersistUsingClosure(): void
    {
        $mockBuilder = (new EntityManagerMockBuilder())->hasPersist(function (stdClass $actual): bool {
            return $actual instanceof stdClass
                && $actual->propertyOne === 'property one'
                && $actual->propertyTwo === 'property two';
        });

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        $expected = new stdClass();
        $expected->propertyOne = 'property one';
        $expected->propertyTwo = 'property two';

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($entityManager->persist($expected));
    }

    /**
     * Test hasPersist adds expectation to call persist method in entity manager.
     *
     * @return void
     */
    public function testHasPersistUsingObject(): void
    {
        $object = new stdClass();

        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasPersist($object);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($entityManager->persist($object));
    }

    /**
     * Test hasRemove adds expectation to call remove method in entity manager using a closure to assert data
     * if the object is not accessible from the test.
     *
     * @return void
     */
    public function testHasRemoveUsingClosure(): void
    {
        $mockBuilder = (new EntityManagerMockBuilder())->hasRemove(function (stdClass $actual): bool {
            return $actual instanceof stdClass
                && $actual->propertyOne === 'property one'
                && $actual->propertyTwo === 'property two';
        });

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        $expected = new stdClass();
        $expected->propertyOne = 'property one';
        $expected->propertyTwo = 'property two';

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($entityManager->remove($expected));
    }

    /**
     * Test hasRemove adds expectation to call remove method in entity manager.
     *
     * @return void
     */
    public function testHasRemoveUsingObject(): void
    {
        $object = new stdClass();

        $mockBuilder = new EntityManagerMockBuilder();
        $mockBuilder->hasRemove($object);

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $mockBuilder->build();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($entityManager->remove($object));
    }
}
