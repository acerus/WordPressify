<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\ParentAwareContainerInterface;
use Xpmock\TestCase;

/**
 * Tests {@see \Dhii\Di\ParentAwareContainerInterface}.
 *
 * @since 0.1
 */
class ParentAwareContainerInterfaceTest extends TestCase
{
    /**
     * @since 0.1
     *
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\ParentAwareContainerInterface';

    /**
     * Name of the test subject's ancestor.
     *
     * @since 0.1
     */
    const TEST_SUBJECT_ANCESTOR = 'Dhii\\Di\\ContainerInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return ParentAwareContainerInterface
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->getParentContainer()
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
        $this->assertInstanceOf(static::TEST_SUBJECT_ANCESTOR, $subject);
    }
}
