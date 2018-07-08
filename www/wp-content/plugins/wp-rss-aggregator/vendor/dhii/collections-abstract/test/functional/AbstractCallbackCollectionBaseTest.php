<?php

namespace Dhii\Collection\FuncTest;

/**
 * Tests {@see \Dhii\Collection\AbstractCallbackCollection}.
 *
 * @since 0.1.0
 */
class AbstractCallbackCollectionBaseTest extends \Xpmock\TestCase
{
    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1.0
     *
     * @return \Dhii\Collection\AbstractCallbackCollection The new instance of the test subject.
     */
    public function createInstance()
    {
        $instance = $this->mock('Dhii\\Collection\\AbstractCallbackCollectionBase')
                ->_validateItem()
                ->new();

        $reflection = $this->reflect($instance);
        $reflection->_construct();

        return $instance;
    }

    /**
     * Tests whether a valid instance of the test subejct can be created.
     *
     * @since 0.1.0
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInstanceOf('Dhii\\Collection\\AbstractCallbackCollectionBase', $subject, 'Subject is not of the required type');
    }

    /**
     * Tests whether the each() method runs as required.
     *
     * @since 0.1.0
     */
    public function testEach()
    {
        $me = $this;
        $subject = $this->createInstance();
        $reflection = $this->reflect($subject);

        $reflection->_addItem('apple');
        $reflection->_addItem('banana');
        $reflection->_addItem('orange');
        $items = $reflection->_getItems();

        $iterations = 0;
        $iterator = $reflection->_each(
            /* In the `use` statement, the `$iterations` variable is byref to bypass early binding:
             * http://stackoverflow.com/q/8403908#comment37564320_8403958
             */
            function($key, $item, &$isContinue) use ($me, &$iterations, $items) {
                $iterations++;
                if ($iterations === 2) {
                    // This tests whether breaking out of the loop works from the callback
                    $isContinue = false;
                }

                $me->assertContains($item, $items, 'The current item is not in the collection');
            }
        );

        foreach ($iterator as $_item) {
            // Callback only runs on each iteration.
        }

        $this->assertEquals(2, $iterations, 'The callback has not been invoked the required number of times');
    }
}
