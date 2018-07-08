<?php

namespace Dhii\Collection\UnitTest;

/**
 * Tests {@see Dhii\Collection\CallbackIterableInterface}.
 *
 * @since 0.1.0
 */
class CallbackIterableInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1.0
     *
     * @return Dhii\Collection\CallbackIterableInterface
     */
    public function createInstance()
    {
        $mock = $this->getMock('Dhii\\Collection\\CallbackIterableInterface');

        return $mock;
    }

    /**
     * Tests whether a correct instance of a descendant can be created.
     *
     * @since 0.1.0
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf('Dhii\\Collection\\CallbackIterableInterface', $subject, 'Could not create an implementing instance');
    }
}
