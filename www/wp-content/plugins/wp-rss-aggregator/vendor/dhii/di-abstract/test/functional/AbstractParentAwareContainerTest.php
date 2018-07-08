<?php

namespace Dhii\Di\FuncTest;

use Interop\Container\ContainerInterface;
use Dhii\Di\AbstractParentAwareContainer;
use Exception;
use Interop\Container\ServiceProvider;
use Xpmock\TestCase;

/**
 * Tests {@see Dhii\Di\AbstractParentAwareContainer}.
 *
 * @since 0.1
 */
class AbstractParentAwareContainerTest extends TestCase
{
    /**
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\AbstractParentAwareContainer';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return AbstractParentAwareContainer
     */
    public function createInstance(ServiceProvider $provider = null, ContainerInterface $parent = null)
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->_createNotFoundException(function ($msg, $code = 0, Exception $prev = null) {
                return new Exception($msg, $code, $prev);
            })
            ->_createContainerException(function ($m, $code = 0, Exception $prev = null) {
                return new Exception($m, $code, $prev);
            })
            ->new();

        if ($provider !== null) {
            foreach ($provider->getServices() as $_id => $_definition) {
                $mock->this()->serviceDefinitions = array_merge(
                    $mock->this()->serviceDefinitions,
                    array($_id => $_definition)
                );
            }
        }

        $mock->this()->parentContainer = $parent;

        return $mock;
    }

    /**
     * Creates a parent container.
     *
     * This method differs from {@see createInstance} in that it creates the container mock from the
     * {@see ParentAwareContainerInterface} rather than from {@see AbstractParentAwareContainer}.
     *
     * @param array $definitions Optional array of service definitions.
     * @param ContainerInterface $parent Optional parent container.
     *
     * @return ParentAwareContainerInterface The created instance.
     */
    public function createParentContainer(array $definitions = array(), $parent = null)
    {
        $mock = $this->mock('Dhii\\Di\\ParentAwareContainerInterface')
            ->has(function ($id) use ($definitions) {
                return isset($definitions[$id]);
            })
            ->get(function ($id) use ($definitions) {
                return $definitions[$id];
            })
            ->getParentContainer(function () use ($parent) {
                return $parent;
            })
            ->new();

        return $mock;
    }

    /**
     * Creates a service provider.
     *
     * @param array $definitions The service definitions.
     *
     * @return ServiceProvider
     */
    public function createServiceProvider(array $definitions = array())
    {
        $mock = $this->mock('Interop\\Container\\ServiceProvider')
            ->getServices(function () use ($definitions) {
                return $definitions;
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
        return function ($container, $previous = null) use ($value) {
            return $value;
        };
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
    }

    /**
     * Tests the parent container checker method when the container has no parent.
     *
     * @since 0.1
     */
    public function testHasParentContainerNoParent()
    {
        $subject = $this->createInstance();

        $this->assertFalse($subject->this()->_hasParentContainer());
    }

    /**
     * Tests the parent container checker method when the container has a parent.
     *
     * @since 0.1
     */
    public function testHasParentContainerWithParent()
    {
        $parent = $this->createParentContainer();
        $subject = $this->createInstance(null, $parent);

        $this->assertTrue($subject->this()->_hasParentContainer());
    }

    /**
     * Tests the parent container getter method when the container has no parent.
     *
     * @since 0.1
     */
    public function testGetParentContainerNoParent()
    {
        $subject = $this->createInstance();

        $this->assertNull($subject->this()->_getParentContainer());
    }

    /**
     * Tests the parent container getter method when the container has a parent.
     *
     * @since 0.1
     */
    public function testGetParentContainerWithParent()
    {
        $parent = $this->createParentContainer();
        $subject = $this->createInstance(null, $parent);

        $this->assertEquals($parent, $subject->this()->_getParentContainer());
    }

    /**
     * Tests the parent container setter method with a container argument.
     *
     * @since 0.1
     */
    public function testSetParentContainer()
    {
        $subject = $this->createInstance();
        $parent = $this->createParentContainer();

        $subject->this()->_setParentContainer($parent);

        $this->assertEquals($parent, $subject->this()->parentContainer);
    }

    /**
     * Tests the parent container setter method with a container argument and then a null
     * argument to assert if the parent container previously set is correctly cleared.
     *
     * @since 0.1
     */
    public function testSetParentContainerNull()
    {
        $subject = $this->createInstance();
        $parent = $this->createParentContainer();

        $subject->this()->_setParentContainer($parent);
        $subject->this()->_setParentContainer(null);

        $this->assertNull($subject->this()->parentContainer);
    }

    /**
     * Tests the root container resolver method with a single level of parent hierarchy.
     *
     * @since 0.1
     */
    public function testGetRootContainerOneLevel()
    {
        $parent = $this->createParentContainer();
        $subject = $this->createInstance(null, $parent);

        $this->assertEquals($parent, $subject->this()->_getRootContainer());
    }

    /**
     * Tests the root container resolver method with two levels of parent hierarchy.
     *
     * @since 0.1
     */
    public function testGetRootContainerTwoLevels()
    {
        $root = $this->createParentContainer();
        $parent = $this->createParentContainer(array(), $root);
        $subject = $this->createInstance(null, $parent);

        $this->assertEquals($root, $subject->this()->_getRootContainer());
    }

    /**
     * Tests the definition resolver method to ensure that a valid service instance
     * is created.
     *
     * It also ensures that the container instance passed as argument to the service factory closure
     * is the root container of the hierarchy.
     *
     * @since 0.1
     */
    public function testResolveDefinition()
    {
        $root = $this->createParentContainer();
        $parent = $this->createParentContainer(array(), $root);
        $subject = $this->createInstance(null, $parent);

        // Service resolves to passed args with the container instance appended at index `c`
        $definition = function ($container, $previous = null, $args) {
            return array_merge(
                $args,
                array('c' => $container)
            );
        };

        $resolved = $subject->this()->_resolveDefinition($definition, array(
            'num' => 12345,
        ));

        $expected = array(
            'num' => 12345,
            'c' => $root,
        );

        $this->assertEquals($expected, $resolved);
    }
}
