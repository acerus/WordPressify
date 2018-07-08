<?php

namespace Dhii\Collection\UnitTest;

/**
 * Tests {@see Dhii\Collection\SearchableCollectionInterface}.
 *
 * @since 0.1.0
 */
class SearchableCollectionInterfaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1.0
     *
     * @return Dhii\Collection\SearchableCollectionInterface
     */
    public function createInstance()
    {
        $mock = $this->getMock('Dhii\\Collection\\SearchableCollectionInterface');

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

        $this->assertInstanceOf('Dhii\\Collection\\SearchableCollectionInterface', $subject, 'Could not create an implementing instance');
    }
}
