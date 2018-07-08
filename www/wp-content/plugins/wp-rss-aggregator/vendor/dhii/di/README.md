## Dhii - DI ##

[![Build Status](https://travis-ci.org/Dhii/di.svg?branch=master)](https://travis-ci.org/Dhii/di)
[![Code Climate](https://codeclimate.com/github/Dhii/di/badges/gpa.svg)](https://codeclimate.com/github/Dhii/di)
[![Test Coverage](https://codeclimate.com/github/Dhii/di/badges/coverage.svg)](https://codeclimate.com/github/Dhii/di/coverage)
[![Join the chat at https://gitter.im/Dhii/di](https://badges.gitter.im/Dhii/di.svg)](https://gitter.im/Dhii/di?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

A simple, granular, standards-compliant dependency injection container and factory implementation.

### Features
- Complies with [container-interop v1.1](https://github.com/container-interop/container-interop/tree/1.1.0) specification.
- Mostly supports the proposed [service-provider v0.3](https://github.com/container-interop/service-provider/tree/v0.3.0) standard.
- Includes the [delegate lookup](https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup-meta.md) feature, a.k.a. composite containers, with intuitive override order.
- Uses some other [standards](https://github.com/Dhii/di-interface) published separately.
- Granular approach, with an [implementation](https://github.com/Dhii/di-abstract) agnostic of concrete behaviour published separately: rely on this re-usable, tested, standards-compliant functionality to make your own container implementation.
- [`ContainerInterface#get()`](https://github.com/container-interop/container-interop/blob/master/src/Interop/Container/ContainerInterface.php#L26) guaranteed to return same instance every time.
- [`FactoryInterface#make()`](https://github.com/Dhii/di-interface/blob/master/src/FactoryInterface.php#L26) guaranteed to return new instance every time.

### Disadvantages
- Does not support the factories' 2nd parameter, i.e. [`$getPrevious`](https://github.com/container-interop/service-provider/blob/v0.3.0/src/ServiceProvider.php#L22).
- Container and factory unified into one class (to be separated soon).
