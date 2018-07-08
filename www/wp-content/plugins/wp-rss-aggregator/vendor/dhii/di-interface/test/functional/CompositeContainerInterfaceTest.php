<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\CompositeContainerInterface;
use Xpmock\TestCase;

/**
 * Tests {@see \Dhii\Di\CompositeContainerInterface}.
 *
 * @since 0.1
 */
class CompositeContainerInterfaceTest extends TestCase
{
    /**
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\CompositeContainerInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return CompositeContainerInterface
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->get()
            ->has()
            ->getContainers()
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
    }
}
