# ramsey/php-library-starter-kit Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 3.0.0 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.1.4 - 2020-05-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Remove package name from license file, since it caused conflicts with GitHub's automatic license-detection software

## 2.1.3 - 2020-05-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fix case of incorrect license used in generated package.json

## 2.1.2 - 2020-05-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fix typo in `export-ignore` directives in `.gitattributes`

## 2.1.1 - 2020-05-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fix links to code of conduct and contributing guide

## 2.1.0 - 2020-05-29

### Added

- Nothing.

### Changed

- Rename phpstan.neon to phpstan.neon.dist

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 2.0.1 - 2020-05-29

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fix typo in CONTRIBUTING.md.

## 2.0.0 - 2020-05-29

### Added

- Nothing.

### Changed

- Pretty much a full re-write

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.1.1 - 2019-10-23

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed PSR-12 coding standards violations.

## 1.1.0 - 2019-05-27

### Added

- Nothing.

### Changed

- Moved the `README.md` file to the project root to support packages that will not be placed on GitHub.
- Upgraded to the latest versions of dev tools:
  - PHPStan `^0.11`
  - PHPUnit `^8`
- Now using `--no-suggest` when installing packages after finishing the wizard.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.2 - 2019-01-03

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- The `.gitattributes` file continues to be problematic when using the zip distribution, so this release removes the file entirely.

## 1.0.1 - 2019-01-03

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed a problem where using `composer create-project` was not properly creating a project from the 1.0.0 release because the `.gitattributes` file was too liberal, failing to include important skeleton files in the release zip bundle.

## 1.0.0 - 2019-01-02

This is the initial release of ramsey/php-library-starter-kit, with the ability to
quickly generate a PHP library including all the starting files that I
([@ramsey][]) prefer to have in my projects. Future versions of this project may
expand on this and allow for more generic options.

To create the starting point for a PHP library using this project, run the
following:

``` bash
composer create-project --remove-vcs ramsey/php-library-starter-kit target-directory
```

You will be walked through a series of questions, and your PHP library source
files will be located in `target-directory`, when completed. Change to that
directory, `git init`, and off you go!

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

[@ramsey]: https://github.com/ramsey
