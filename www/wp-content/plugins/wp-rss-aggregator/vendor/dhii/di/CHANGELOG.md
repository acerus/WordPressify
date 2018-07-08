# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [0.1.1] - 2017-02-03
Non-BC-breaking bugfixes.
Reduced size of dist archive.
Added Gitter notifications of Travis events, and Gitter badge.

### Fixed
- `CompositeContainer#__construct()` not accepting interop containers.
- `CompositeContainer#add()` not implementing interface method.
- `CompositeContainer` not throwing exceptions correctly.

## [0.1] - 2017-02-02
Initial release, containing concrete implementations.

### Added
- Implementations of regular and compound containers, with service provider support.
- Tests.
