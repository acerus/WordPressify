<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\AbstractContainer;
use Exception;
use Interop\Container\ServiceProvider;
use Xpmock\TestCase;

/**
 * Tests {@see Dhii\Di\AbstractContainer}.
 *
 * @since 0.1
 */
class AbstractContainerTest extends TestCase
{
    /**
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\AbstractContainer';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @param ServiceProvider $provider Optional service provider. Default: null
     *
     * @return AbstractContainer
     */
    public function createInstance(ServiceProvider $provider = null)
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

        return $mock;
    }

    /**
     * Creates a service provider.
     *
     * @since 0.1
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
     * @since 0.1
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
     * Tests the service getter to retrieve a new service instance.
     *
     * @since 0.1
     */
    public function testGetNewService()
    {
        $serviceProvider = $this->createServiceProvider(array(
            'test' => function () {
                return new \SplObjectStorage();
            },
        ));

        $subject = $this->createInstance($serviceProvider);

        $this->assertEquals(new \SplObjectStorage(), $subject->this()->_get('test'));
    }

    /**
     * Tests the service getter to ensure that multiple retrievals of the same service return
     * the same instance.
     *
     * @since 0.1
     */
    public function testGetSameService()
    {
        $serviceProvider = $this->createServiceProvider(array(
            'test' => function () {
                return new \SplObjectStorage();
            },
        ));

        $subject = $this->createInstance($serviceProvider);

        // Get first time
        $first = $subject->this()->_get('test');

        $this->assertTrue($first === $subject->this()->_get('test'));
    }

    /**
     * Tests the factory method to ensure that a new service instance is created.
     *
     * @since 0.1
     */
    public function testMakeOnce()
    {
        $serviceProvider = $this->createServiceProvider(array(
            'test' => function () {
                return new \SplObjectStorage();
            },
        ));

        $subject = $this->createInstance($serviceProvider);

        $this->assertFalse($subject->this()->_get('test') === $subject->this()->_make('test'));
    }

    /**
     * Tests the factory method to ensure that multiple calls still result in different instances.
     *
     * @since 0.1
     */
    public function testMakeTwice()
    {
        $serviceProvider = $this->createServiceProvider(array(
            'test' => function () {
                return new \SplObjectStorage();
            },
        ));

        $subject = $this->createInstance($serviceProvider);

        $this->assertFalse($subject->this()->_make('test') === $subject->this()->_make('test'));
    }

    /**
     * Tests that the container throws correct exception when service not found.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage no service defined
     *
     * @since 0.1
     */
    public function testMakeThrowsNotFound()
    {
        $serviceProvider = $this->createServiceProvider(array(
            'test' => function () {
                return new \SplObjectStorage();
            },
        ));

        $subject = $this->createInstance($serviceProvider);
        $reflection = $this->reflect($subject);
        $reflection->_make('non_existing');
    }

    /**
     * Tests the service ID checker method.
     *
     * @since 0.1
     */
    public function testHas()
    {
        $serviceProvider = $this->createServiceProvider(array(
            'one' => $this->createDefinition(1),
            'two' => $this->createDefinition(2),
            'three' => $this->createDefinition(3),
        ));

        $subject = $this->createInstance($serviceProvider);

        $this->assertTrue($subject->this()->_has('one'));
        $this->assertTrue($subject->this()->_has('two'));
        $this->assertTrue($subject->this()->_has('three'));
        $this->assertFalse($subject->this()->_has('random'));
    }

    /**
     * Tests the method that registers a service provider to ensure that all services are
     * correctly registered from the provider to the container.
     *
     * @since 0.1
     */
    public function testRegister()
    {
        $subject = $this->createInstance();
        $provider = $this->createServiceProvider(array(
            'one' => $this->createDefinition(1),
            'two' => $this->createDefinition(2),
            'three' => $this->createDefinition(3),
        ));
        $expected = $provider->getServices();

        $subject->this()->_register($provider);

        $this->assertEquals($expected, $subject->this()->serviceDefinitions);
    }

    /**
     * Tests the definition setter method to ensure that the definition is correctly registered
     * to the container.
     *
     * @since 0.1
     */
    public function testSetDefintion()
    {
        $subject = $this->createInstance();
        $definition = $this->createDefinition('test');
        $expected = array('test' => $definition);

        $subject->this()->_setDefinition('test', $definition);

        $this->assertEquals($expected, $subject->this()->serviceDefinitions);
    }

    /**
     * Tests the main setter method with a service definition to ensure that the
     * service definition is correctly registered in the container.
     *
     * @since 0.1
     */
    public function testSetWithDefinition()
    {
        $subject = $this->createInstance();
        $definition = $this->createDefinition('test');
        $expected = array('test' => $definition);

        $subject->this()->_set('test', $definition);

        $this->assertEquals($expected, $subject->this()->serviceDefinitions);
    }

    /**
     * Tests the main setter method with a service definition to ensure that the
     * service definition is correctly registered in the container.
     *
     * @since 0.1
     */
    public function testSetWithProvider()
    {
        $subject = $this->createInstance();
        $provider = $this->createServiceProvider(array(
            'one' => $this->createDefinition(1),
            'two' => $this->createDefinition(2),
            'three' => $this->createDefinition(3),
        ));
        $expected = $provider->getServices();

        $subject->this()->_set($provider);

        $this->assertEquals($expected, $subject->this()->serviceDefinitions);
    }

    /**
     * Tests the definitions getter method to ensure that the definitions returned
     * all correct, previously registered definitions.
     *
     * @since 0.1
     */
    public function testGetDefinitions()
    {
        $provider = $this->createServiceProvider(array(
            'one' => $this->createDefinition(1),
            'two' => $this->createDefinition(2),
            'three' => $this->createDefinition(3),
        ));
        $subject = $this->createInstance($provider);
        $expected = $provider->getServices();

        $this->assertEquals($expected, $subject->this()->_getDefinitions());
    }

    /**
     * Tests the single definition getter method to ensure that it returns the
     * correct definition when given an existing ID and `null` when given a
     * non-existing ID.
     *
     * @since 0.1
     */
    public function testGetDefinition()
    {
        $subject = $this->createInstance();
        $definition = $this->createDefinition('test');

        $subject->this()->_set('one', $definition);
        $subject->this()->_set('two', $this->createDefinition('two'));

        $this->assertEquals($definition, $subject->this()->_getDefinition('one'));
        $this->assertEquals(null, $subject->this()->_getDefinition('random'));
    }

    /**
     * Tests the definition checker method to ensure that it correctly asserts
     * if the container has specific definitions, by ID.
     *
     * @since 0.1
     */
    public function testHasDefinition()
    {
        $subject = $this->createInstance();

        $subject->this()->_set('test', $this->createDefinition('test'));

        $this->assertTrue($subject->this()->_hasDefinition('test'));
        $this->assertFalse($subject->this()->_hasDefinition('random'));
    }

    /**
     * Tests the cached service instance getter method to assert whether
     * previously retrieved services are correctly cached while others
     * are not.
     *
     * @since 0.1
     */
    public function testGetCached()
    {
        $definitions = array(
            'one' => $this->createDefinition(1),
            'two' => $this->createDefinition(2),
            'three' => $this->createDefinition(3),
        );
        $provider = $this->createServiceProvider($definitions);
        $subject = $this->createInstance($provider);

        // Call `_get()` to cache the instance
        $subject->this()->_get('one');

        $this->assertEquals(1,    $subject->this()->_getCached('one'));
        $this->assertEquals(null, $subject->this()->_getCached('two'));
        $this->assertEquals(null, $subject->this()->_getCached('three'));
    }

    /**
     * Tests the cached service instance checker method to ensure that
     * it correctly asserts whether a service instance is cached or not.
     *
     * @since 0.1
     */
    public function testIsCached()
    {
        $definitions = array(
            'one' => $this->createDefinition(1),
            'two' => $this->createDefinition(2),
            'three' => $this->createDefinition(3),
        );
        $provider = $this->createServiceProvider($definitions);
        $subject = $this->createInstance($provider);

        // Call `_get()` to cache the instance
        $subject->this()->_get('two');

        $this->assertFalse($subject->this()->_isCached('one'));
        $this->assertTrue($subject->this()->_isCached('two'));
        $this->assertFalse($subject->this()->_isCached('three'));
    }

    /**
     * Tests the service caching method to ensure that the given service is
     * correctly cached.
     *
     * @since 0.1
     */
    public function testCacheService()
    {
        // Add some definitions to be sure
        $definitions = array(
            'one' => $this->createDefinition(1),
            'two' => $this->createDefinition(2),
        );
        $provider = $this->createServiceProvider($definitions);
        $subject = $this->createInstance($provider);

        $subject->this()->_cacheService('three', $this->createDefinition(3));
        $expected = array('three' => $this->createDefinition(3));

        $this->assertEquals($expected, $subject->this()->serviceCache);
    }

    /**
     * Tests the definition resolver method to ensure that a valid service instance
     * is created.
     *
     * @since 0.1
     */
    public function testResolveDefinition()
    {
        $definition = function ($container, $previous = null, $args) {
            return new \DateTimeZone($args['tz']);
        };
        $subject = $this->createInstance();
        $resolved = $subject->this()->_resolveDefinition(
            $definition,
            array('tz' => 'Europe/Malta')
        );

        $this->assertEquals(new \DateTimeZone('Europe/Malta'), $resolved);
    }

    /**
     * Tests to ensure that an exception is thrown when definition is invalid.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage must be callable
     *
     * @since 0.1
     */
    public function testResolveDefinitionThrowsInvalid()
    {
        $definition = 'invalid definition';
        $subject = $this->createInstance();
        $subject->this()->_resolveDefinition(
            $definition,
            array()
        );
    }
}
