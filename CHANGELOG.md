# ramsey/php-library-skeleton Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


## [Unreleased]

### Added

### Changed

### Deprecated

### Removed

### Fixed

### Security


## [1.0.2] - 2019-01-03

### Fixed

* The `.gitattributes` file continues to be problematic when using the zip
  distribution, so this release removes the file entirely.


## [1.0.1] - 2019-01-03

### Fixed

* Fixed a problem where using `composer create-project` was not properly
  creating a project from the 1.0.0 release because the `.gitattributes` file
  was too liberal, failing to include important skeleton files in the release
  zip bundle.


## [1.0.0] - 2019-01-02

### Added

This is the initial release of ramsey/php-library-skeleton, with the ability to
quickly generate a PHP library including all the starting files that I
([@ramsey][]) prefer to have in my projects. Future versions of this project may
expand on this and allow for more generic options.

To create the starting point for a PHP library using this project, run the
following:

``` bash
composer create-project ramsey/php-library-skeleton target-directory
```

You will be walked through a series of questions, and your PHP library source
files will be located in `target-directory`, when completed. Change to that
directory, `git init`, and off you go!


[Unreleased]: https://github.com/ramsey/php-library-skeleton/compare/1.0.1...HEAD
[1.0.1]: https://github.com/ramsey/php-library-skeleton/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/ramsey/php-library-skeleton/commits/1.0.0
[@ramsey]: https://github.com/ramsey
