# Change Log

All notable changes to this project will be documented in this file. This project adheres
to [Semantic Versioning] (http://semver.org/). For change log format,
use [Keep a Changelog] (http://keepachangelog.com/).

## [1.0.0-beta4] - In progress

### Added

- `Anonymizer` class to anonymize har file

## [1.0.0-beta3] - 2022-03-18

### Fixed

- NULL browser not allowed in builders

## [1.0.0-beta2] - 2022-03-18

### Added

- New method `Log::getEntry(int $entry, Page|string|null $page = null): ?Entry`
- New method `getArrayCopy()` on entities
- New interface `EntityInterface` for entities
- New builders classes `Builder` and `BuilderStream`

### Changed

- Improve missing argument exception message

### Removed

- Methods `Log::addPage()` and `Log::addEntry()` removed, use builders instead
- Unnecessary class PhpDoc

### Fixed

- Creation of `Cookie` with expires parameter whose type is `DateTimeInterface`
- `Parser::parse()` does not throw exception if filename does not exist

## [1.0.0-beta1] - 2021-08-27

Initial development
