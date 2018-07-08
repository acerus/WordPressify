<?php

namespace Dhii\Collection\UnitTest;

/**
 * Tests {@see Dhii\Collection\SequenceIteratorIteratorInterface}.
 *
 * @since 0.1.0
 */
class SequenceIteratorIteratorInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1.0
     *
     * @return Dhii\Collection\SequenceIteratorIteratorInterface
     */
    public function createInstance()
    {
        $mock = $this->getMock('Dhii\\Collection\\SequenceIteratorIteratorInterface');

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

        $this->assertInstanceOf('Dhii\\Collection\\SequenceIteratorIteratorInterface', $subject, 'Could not create an implementing instance');
        $this->assertInstanceOf('OuterIterator', $subject, 'Subject does not implement required interface');
    }
}
