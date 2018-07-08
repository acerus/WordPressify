<?php

namespace Dhii\Collection\FuncTest\AbstractCollectionTest;

/**
 * Tests {@see \Dhii\Collection\AbstractCollection}.
 *
 * @since 0.1.0
 */
class AbstractCollectionTest extends \Xpmock\TestCase
{
    /**
     * Creates a new instance of the subject.
     *
     * @since 0.1.0
     *
     * @return \Dhii\Collection\AbstractCollection The new instance of the subject.
     */
    public function createInstance()
    {
        $mock = $this->mock('Dhii\\Collection\\AbstractCollection')
                ->_validateItem()
                ->new();

        $reflection = $this->reflect($mock);
        $reflection->_construct();

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since 0.1.0
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf('Dhii\\Collection\\AbstractCollection', $subject, 'Subject is not of the right type');
    }

    /**
     * Tests whether the collection reports added items correctly.
     *
     * @since 0.1.0
     */
    public function testCanAdd()
    {
        $subject = $this->createInstance();
        $reflection = $this->reflect($subject);

        $reflection->_addItem('apple');
        $reflection->_addItem('banana');

        $items = $reflection->_getItems();
        $this->assertEquals(2, count($items), 'Number of items reported is wrong');
        $this->assertTrue(in_array('apple', $items, true), 'Required element not found among reported items');
        $this->assertTrue(in_array('banana', $items, true), 'Required element not found among reported items');

        $this->assertTrue($reflection->_hasItem('apple'), 'Collection does not contain required item');
        $this->assertTrue($reflection->_hasItem('banana'), 'Collection does not contain required item');
    }

    /**
     * Tests whether the collection reports set items correctly.
     *
     * @since 0.1.0
     */
    public function testCanSet()
    {
        $subject = $this->createInstance();
        $reflection = $this->reflect($subject);

        $reflection->_setItem('one', 'apple');
        $reflection->_setItem('two', 'banana');

        $items = $reflection->_getItems();
        $this->assertEquals(2, count($items), 'Number of items reported is wrong');
        $this->assertEquals('apple', $items['one'], 'Required element not found at specified key');
        $this->assertEquals('banana', $items['two'], 'Required element not found at specified key');

        $this->assertTrue($reflection->_hasItem('apple'), 'Collection does not report required item');
        $this->assertTrue($reflection->_hasItem('banana'), 'Collection does not report required item');

        $this->assertEquals('apple', $reflection->_getItem('one'), 'Collection does not return required item');
        $this->assertEquals('banana', $reflection->_getItem('two'), 'Collection does not return required item');
        $this->assertTrue($reflection->_hasItem('apple'), 'Collection does not contain required item');
        $this->assertTrue($reflection->_hasItem('banana'), 'Collection does not contain required item');

        $this->assertEquals('one', $reflection->_findItem('apple'), 'Collection does not find item at required index');
        $this->assertEquals('two', $reflection->_findItem('banana'), 'Collection does not find item at required index');
    }

    public function testCanRemove()
    {
        $subject = $this->createInstance();
        $reflection = $this->reflect($subject);

        $reflection->_setItem('one', 'apple');
        $reflection->_setItem('two', 'banana');

        $items = $reflection->_getItems();
        $this->assertEquals(2, count($items), 'Number of items reported before removal is wrong');

        $this->assertTrue($reflection->_removeItem('apple'), 'Collection reports inability to remove item');
        $items = $reflection->_getItems();
        $this->assertEquals(1, count($items), 'Number of items reported after removal is wrong');
        $this->assertEquals(array('two' => 'banana'), $items, 'The result reported after item removal is wrong');
    }
}
