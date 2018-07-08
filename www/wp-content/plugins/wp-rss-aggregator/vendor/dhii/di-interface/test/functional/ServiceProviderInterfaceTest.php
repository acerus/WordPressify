<?php

namespace Dhii\Di\FuncTest;

use Dhii\Di\ServiceProviderInterface;
use Xpmock\TestCase;

/**
 * Tests {@see \Dhii\Di\ServiceProviderInterface}.
 *
 * @since 0.1
 */
class ServiceProviderInterfaceTest extends TestCase
{
    /**
     * The name of the test subject.
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\\Di\\ServiceProviderInterface';

    /**
     * Creates a new instance of the test subject.
     *
     * @since 0.1
     *
     * @return ServiceProviderInterface
     */
    public function createInstance()
    {
        $mock = $this->mock(static::TEST_SUBJECT_CLASSNAME)
            ->getServices()
            ->new();

        return $mock;
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
        $this->assertInstanceOf('Interop\\Container\\ServiceProvider', $subject);
    }
}
