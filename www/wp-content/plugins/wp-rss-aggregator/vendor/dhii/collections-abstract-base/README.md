## Collections - Abstract Base ##
[![Build Status](https://travis-ci.org/Dhii/collections-abstract-base.svg?branch=master)](https://travis-ci.org/Dhii/collections-abstract-base)
[![Code Climate](https://codeclimate.com/github/Dhii/collections-abstract-base/badges/gpa.svg)](https://codeclimate.com/github/Dhii/collections-abstract-base)
[![Test Coverage](https://codeclimate.com/github/Dhii/collections-abstract-base/badges/coverage.svg)](https://codeclimate.com/github/Dhii/collections-abstract-base/coverage)

The abstract base classes for collections.

This exists in order not to create a circular reference between generic and
more specialized collection classes, which would otherwise be inevitable
due to the unavailability of traits in the target version of PHP.
