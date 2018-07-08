<?php

namespace Dhii\Collection\UnitTest;

/**
 * Tests {@see Dhii\Collection\CollectionInterface}.
 *
 * @since 0.1.0
 */
class CollectionInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1.0
     *
     * @return Dhii\Collection\CollectionInterface
     */
    public function createInstance()
    {
        $mock = $this->getMock('Dhii\\Collection\\CollectionInterface');

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

        $this->assertInstanceOf('Dhii\\Collection\\CollectionInterface', $subject, 'Could not create an implementing instance');
    }
}
