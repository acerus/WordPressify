## Dhii - DI Interface ##
[![Build Status](https://travis-ci.org/Dhii/di-interface.svg?branch=master)](https://travis-ci.org/Dhii/di-interface)
[![Code Climate](https://codeclimate.com/github/Dhii/di-interface/badges/gpa.svg)](https://codeclimate.com/github/Dhii/di-interface)
[![Test Coverage](https://codeclimate.com/github/Dhii/di-interface/badges/coverage.svg)](https://codeclimate.com/github/Dhii/di-interface/coverage)

Interfaces for DI container implementations.
In addition to existing [container-interop][] and
[service-provider][] proxies, provides the following:

- [`CompositeContainerInterface`][] - enables [lookup delegation][].
- [`WritableCompositeContainerInterface`][] - a composite container that can have containers added.
- [`WritableContainerInterface`][] - a container that can have service definitions added.
- [`FactoryInterface`][] - enables standard [factory][] implementation.
- [`ExceptionInterface`][] - any DI exception.

The packages adheres to the [SemVer][] specification, and there will be full backward compatibility between minor versions.
Additionally, it follows the rule of the [caret operator][], i.e. there will be full backward compatibility between patch pre-release versions.

[container-interop]:                        https://github.com/container-interop/container-interop
[service-provider]:                         https://github.com/container-interop/service-provider
[`CompositeContainerInterface`]:            https://github.com/Dhii/di-interface/blob/master/src/CompositeContainerInterface.php
[`WritableCompositeContainerInterface`]:    https://github.com/Dhii/di-interface/blob/master/src/WritableCompositeContainerInterface.php
[`WritableContainerInterface`]:             https://github.com/Dhii/di-interface/blob/master/src/WritableContainerInterface.php
[`FactoryInterface`]:                       https://github.com/Dhii/di-interface/blob/master/src/FactoryInterface.php
[`ExceptionInterface`]:                     https://github.com/Dhii/di-interface/blob/master/src/ExceptionInterface.php
[lookup delegation]:                        https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup.md
[factory]:                                  https://github.com/container-interop/container-interop/issues/44
[SemVer]:                                   http://semver.org/
[caret operator]:                           https://getcomposer.org/doc/articles/versions.md#caret
