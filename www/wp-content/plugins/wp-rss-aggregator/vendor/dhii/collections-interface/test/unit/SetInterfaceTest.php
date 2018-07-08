<?php

namespace Dhii\Collection\UnitTest;

/**
 * Tests {@see Dhii\Collection\SetInterface}.
 *
 * @since 0.1.1
 */
class SetInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1.1
     *
     * @return \Dhii\Collection\SetInterface
     */
    public function createInstance()
    {
        $mock = $this->getMockBuilder('Dhii\\Collection\\SetInterface')
                ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since 0.1.1
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf('Dhii\\Collection\\SetInterface', $subject, 'A valid instance of the test subject could not be created');
        $this->assertInstanceOf('Countable', $subject, 'A valid instance of the test subject could not be created');
    }
}
