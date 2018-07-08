<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\WritableCompositeContainerInterface;
use Xpmock\TestCase;

/**
 * Tests {@see \Dhii\Di\WritableCompositeContainerInterface}.
 *
 * @since 0.1
 */
class WritableCompositeContainerInterfaceTest extends TestCase
{
    /**
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\WritableCompositeContainerInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return WritableCompositeContainerInterface
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->get()
            ->has()
            ->getContainers()
            ->add()
            ->new();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since 0.1
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf(static::TEST_SUBJECT_CLASSNAME, $subject);
        $this->assertInstanceOf('Dhii\\Di\\ContainerInterface', $subject);
        $this->assertInstanceOf('Dhii\\Di\\ContainersAwareInterface', $subject);
        $this->assertInstanceOf('Dhii\\Di\\WritableCompositeContainerInterface', $subject);
    }
}
