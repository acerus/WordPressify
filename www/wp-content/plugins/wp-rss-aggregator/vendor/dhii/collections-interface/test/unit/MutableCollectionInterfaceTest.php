<?php

namespace Dhii\Collection\UnitTest;

/**
 * Tests {@see Dhii\Collection\MutableCollectionInterface}.
 *
 * @since 0.1.0
 */
class MutableCollectionInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1.0
     *
     * @return Dhii\Collection\MutableCollectionInterface
     */
    public function createInstance()
    {
        $mock = $this->getMock('Dhii\\Collection\\MutableCollectionInterface');

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

        $this->assertInstanceOf('Dhii\\Collection\\MutableCollectionInterface', $subject, 'Could not create an implementing instance');
    }
}
