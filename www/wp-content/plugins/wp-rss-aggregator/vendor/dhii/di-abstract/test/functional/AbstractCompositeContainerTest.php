<?php

namespace Dhii\Di\FuncTest;

/**
 * Tests {@see Dhii\Di\AbstractCompositeContainer}.
 *
 * The {@see Dhii\Di\Stub\AbstractCompositeContainerStub} class is used to make the test subject
 * implement the {@see Interop\Container\ContainerInterface} interface.
 *
 * This is required for some tests to pass since the code of the test subject relies on the
 * container being passed as the first argument to service factory closures to be a container
 * that implements the standard interop interface.
 *
 * @since 0.1
 */
class AbstractCompositeContainerTest extends \Xpmock\TestCase
{
    /**
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\Stub\\AbstractCompositeContainerStub';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return AbstractCompositeContainer
     */
    public function createInstance(array $definitions = array())
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->_createNotFoundException(function ($msg, $code = 0, \Exception $prev = null) {
                return new \Exception($msg, $code, $prev);
            })
            ->_createContainerException(function ($m, $code = 0, \Exception $prev = null) {
                return new \Exception($m, $code, $prev);
            })
            ->new();

        foreach ($definitions as $_id => $_definition) {
            $mock->this()->serviceDefinitions = array_merge(
                $mock->this()->serviceDefinitions,
                array($_id => $_definition)
            );
        }

        return $mock;
    }

    /**
     * Creates a child container.
     *
     * This method differs from {@see createInstance} in that it creates the container mock from the
     * {@see Interop\Container\ContainerInterface} interface rather than from
     * {@see AbstractCompositeContainer} class.
     *
     * @param array $definitions Optional array of service definitions.
     *
     * @return ParentAwareContainerInterface The created instance.
     */
    public function createChildContainer(array $definitions = array())
    {
        $mock = $this->mock('Interop\\Container\\ContainerInterface')
            ->has(function ($id) use ($definitions) {
                return isset($definitions[$id]);
            })
            ->get(function ($id) use ($definitions) {
                return call_user_func_array($definitions[$id], array(null, null));
            })
            ->new();

        return $mock;
    }

    /**
     * Create a service definition that returns a simple value.
     *
     * @param mixed $value The value that the service definition will return.
     *
     * @return callable A service definition that will return the given value.
     */
    public function createDefinition($value)
    {
        return function () use ($value) {
            return $value;
        };
    }

    /**
     * Tests the container ID generator method to ensure that different containers result in
     * different keys.
     *
     * @since 0.1
     */
    public function testCreateContainerId()
    {
        $subject = $this->createInstance();
        $child1 = $this->createChildContainer();
        $child2 = $this->createChildContainer();

        $id1 = $subject->this()->_createContainerId($child1);
        $id2 = $subject->this()->_createContainerId($child2);

        $this->assertNotEquals($id1, $id2);
    }

    /**
     * Tests the method that adds containers to ensure that the container is correctly registered
     * inside a definition and that the definition correctly returns the same registered instance.
     *
     * @since 0.1
     */
    public function testAdd()
    {
        $subject = $this->createInstance();
        $child = $this->createChildContainer();

        $subject->this()->_add($child);

        // Assert that container service definition has been added
        $this->assertEquals(1, count($subject->this()->serviceDefinitions));

        // Get generated container ID
        $ids = array_keys($subject->this()->serviceDefinitions);
        $childId = $ids[0];

        // Assert that the container returned is the same instance that was added
        $this->assertTrue($child === $subject->this()->_get($childId));
    }

    /**
     * Tests the container getter method to ensure that the containers, and only containers,
     * are correctly retrieved in an array - as instances and not definitions.
     *
     * @since 0.1
     */
    public function testGetContainers()
    {
        $subject = $this->createInstance();
        $child1 = $this->createChildContainer();
        $child2 = $this->createChildContainer();

        // Add a non-container service in the mix.
        // This should not be returned by `_getContainers()`
        $subject->this()->serviceDefinitions = array(
            $subject->this()->_createContainerId($child1) => $this->createDefinition($child1),
            $subject->this()->_createContainerId($child2) => $this->createDefinition($child2),
            'test' => $this->createDefinition('random'),
        );

        $containers = array_values($subject->this()->_getContainers());

        $this->assertEquals(array($child1, $child2), $containers);
    }

    /**
     * Tests the container getter reverse method to ensure that the containers, and only containers,
     * are correctly retrieved in reverse order as an array - as instances and not definitions.
     *
     * @since 0.1
     */
    public function testGetContainersReversed()
    {
        $subject = $this->createInstance();
        $child1 = $this->createChildContainer();
        $child2 = $this->createChildContainer();

        // Add a non-container service in the mix.
        // This should not be returned by `_getContainers()`
        $subject->this()->serviceDefinitions = array(
            $subject->this()->_createContainerId($child1) => $this->createDefinition($child1),
            $subject->this()->_createContainerId($child2) => $this->createDefinition($child2),
            'test' => $this->createDefinition('random'),
        );

        $containers = array_values($subject->this()->_getContainers());

        $this->assertEquals(array($child2, $child1), $containers);
    }

    /**
     * Tests the method that checks if a service is delegated to a child container.
     *
     * The method is also expected to return the container instance that has the delegated service.
     *
     * In the event of service keys being registered across multiple child containers, the service
     * will be retrieved from the last child container registered.
     *
     * @since 0.1
     */
    public function testHasDelegated()
    {
        $subject = $this->createInstance(array(
            'mine' => $this->createDefinition('service in self'),
        ));
        $child1 = $this->createChildContainer(array(
            'dupe' => $this->createDefinition('duplicate from 1'),
        ));
        $child2 = $this->createChildContainer(array(
            'test' => $this->createDefinition(123456),
            'dupe' => $this->createDefinition('duplicate from 2'),
        ));

        $subject->this()->serviceDefinitions = array(
            $subject->this()->_createContainerId($child1) => $this->createDefinition($child1),
            $subject->this()->_createContainerId($child2) => $this->createDefinition($child2),
        );

        // Nothing has "foobar"
        $this->assertFalse($subject->this()->_hasDelegated('foobar'));

        // Services in self container are not acknowledged
        $this->assertFalse($subject->this()->_hasDelegated('mine'));

        // Child2 has "test"
        $this->assertEquals($child2, $subject->this()->_hasDelegated('test'));

        // Both children have "dupe" but Child2 is the expected return since it was registered last
        $this->assertEquals($child2, $subject->this()->_hasDelegated('dupe'));
    }

    /**
     * Tests the method that checks if a service is delegated to a child container, when the containers
     * are provided through an iterator.
     *
     * The method is also expected to return the container instance that has the delegated service.
     *
     * In the event of service keys being registered across multiple child containers, the service
     * will be retrieved from the last child container registered.
     *
     * @since 0.1
     */
    public function testHasDelegatedIterator()
    {
        $child1 = $this->createChildContainer(array(
            'dupe' => $this->createDefinition('duplicate from 1'),
        ));
        $child2 = $this->createChildContainer(array(
            'test' => $this->createDefinition(123456),
            'dupe' => $this->createDefinition('duplicate from 2'),
        ));

        $subject = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->_createNotFoundException(function ($msg, $code = 0, Exception $prev = null) {
                return new Exception($msg, $code, $prev);
            })
            ->_createContainerException(function ($m, $code = 0, Exception $prev = null) {
                return new Exception($m, $code, $prev);
            })
            ->_getContainersReversed(function () use ($child1, $child2) {
                // in reverse order
                $containers = array(
                    uniqid('child2') => $child2,
                    uniqid('child1') => $child1
                );
                $iterator = new \AppendIterator();
                $iterator->append(new \ArrayIterator($containers));
                return $iterator;
            })
            ->new();

        // Nothing has "foobar"
        $this->assertFalse($subject->this()->_hasDelegated('foobar'));

        // Services in self container are not acknowledged
        $this->assertFalse($subject->this()->_hasDelegated('mine'));

        // Child2 has "test"
        $this->assertEquals($child2, $subject->this()->_hasDelegated('test'));

        // Both children have "dupe" but Child2 is the expected return since it was registered last
        $this->assertEquals($child2, $subject->this()->_hasDelegated('dupe'));
    }

    /**
     * Tests the delegated service getter method to ensure that any services that are registered
     * to child containers are retrievable.
     *
     * This test also ensures that in the event of service keys being registered across multiple
     * child containers, the service will be retrieved from the last child container registered.
     *
     * @since 0.1
     */
    public function testGetDelegated()
    {
        $subject = $this->createInstance();

        $child1 = $this->createChildContainer(array(
            'dupe' => $this->createDefinition('duplicate from 1'),
        ));
        $child2 = $this->createChildContainer(array(
            'test' => $this->createDefinition(123456),
            'dupe' => $this->createDefinition('duplicate from 2'),
        ));

        $subject->this()->serviceDefinitions = array(
            $subject->this()->_createContainerId($child1) => $this->createDefinition($child1),
            $subject->this()->_createContainerId($child2) => $this->createDefinition($child2),
        );

        $this->assertEquals(123456, $subject->this()->_getDelegated('test'));

        $this->assertEquals('duplicate from 2', $subject->this()->_getDelegated('dupe'));
    }

    /**
     * Tests that the composite container will throw an exception if none of the
     * child containers have a matching service.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage no service defined
     *
     * @since 0.1
     */
    public function testGetDelegatedThrowsNotFound()
    {
        $subject = $this->createInstance();
        $reflection = $this->reflect($subject);

        $child1 = $this->createChildContainer(array(
            'test1' => $this->createDefinition(321654),
        ));
        $child2 = $this->createChildContainer(array(
            'test2' => $this->createDefinition(123456),
            'test3' => $this->createDefinition('asdasdasd'),
        ));

        $reflection->serviceDefinitions = array(
            $reflection->_createContainerId($child1) => $this->createDefinition($child1),
            $reflection->_createContainerId($child2) => $this->createDefinition($child2),
        );

        $reflection->_getDelegated('non existing service');
    }
}
