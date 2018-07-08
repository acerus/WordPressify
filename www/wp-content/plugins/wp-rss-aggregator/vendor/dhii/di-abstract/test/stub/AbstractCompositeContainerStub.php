<?php

namespace Dhii\Di\Stub;

use Dhii\Di\AbstractCompositeContainer;
use Interop\Container\ContainerInterface;

/**
 * Stub class - used for testing the {@see AbstractCompositeContainer}.
 *
 * @since 0.1
 */
abstract class AbstractCompositeContainerStub extends AbstractCompositeContainer
    implements ContainerInterface
{
    public function get($id)
    {
        return $this->_get($id);
    }

    public function has($id)
    {
        return $this->_has($id);
    }
}
