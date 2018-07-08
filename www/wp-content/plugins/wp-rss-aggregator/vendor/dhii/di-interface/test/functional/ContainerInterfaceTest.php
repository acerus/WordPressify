<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\ContainerInterface;
use Xpmock\TestCase;

/**
 * Tests {@see \Dhii\Di\ContainerInterface}.
 *
 * @since 0.1
 */
class ContainerInterfaceTest extends TestCase
{
    /**
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\ContainerInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return ContainerInterface
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->get()
            ->has()
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
        $this->assertInstanceOf('Interop\Container\ContainerInterface', $subject);
    }
}
