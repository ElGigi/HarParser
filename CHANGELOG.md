# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning] (http://semver.org/).
For change log format, use [Keep a Changelog] (http://keepachangelog.com/).

## [1.0.0-beta3] - In progress

### Added

- New method `getArrayCopy()` on entities
- New interface `EntityInterface` for entities

### Changed

- Improve missing argument exception message

### Removed

- Unnecessary class PhpDoc

### Fixed

- Creation of `Cookie` with expires parameter whose type is `DateTimeInterface`
- `Parser::parse()` does not throw exception if filename does not exist

## [1.0.0-beta2] - 2021-09-08

### Added

- New method `Log::getEntry(int $entry, Page|string|null $page = null): ?Entry`

## [1.0.0-beta1] - 2021-08-27

Initial development
