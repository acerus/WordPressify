<?php

namespace Dhii\Di\FuncTest;

use Xpmock\TestCase;

/**
 * Tests {@see \Dhii\Di\WritableContainerInterface}.
 *
 * @since 0.1
 */
class WritableContainerInterfaceTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since 0.1
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\WritableContainerInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return \Dhii\Di\WritableContainerInterface
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->get()
            ->has()
            ->set()
            ->register()
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
        $this->assertInstanceOf('Interop\\Container\\ContainerInterface', $subject);
    }
}
