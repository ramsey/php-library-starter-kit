# ramsey/php-library-starter-kit Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 3.5.0 - 2023-04-27

### Added

- Add an option for version 2.1 of the Contributor Covenant

- Update GitHub workflows to support auto-merging of Dependabot pull requests

### Changed

- Update ramsey/devtools to version 2.0

- Increase minimum PHP version to 8.1

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Stop passing `starter-kit` command name to avoid confusing newer versions of symfony/console.

## 3.4.2 - 2022-01-27

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Use a new instance of the Symfony Finder for each use.

## 3.4.1 - 2022-01-02

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Bump codecov/codecov-action to v2.1.0

## 3.4.0 - 2022-01-02

### Added

- Add the `allow-plugins` property to composer.json.
- Tell Dependabot to update GitHub Actions.
- Run GitHub Actions CI builds only on pushes to `main` and PRs based on `main`.

### Changed

- Remove "deps" as a type from conventional commits configuration.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Use v2 of ramsey/composer-install.
- Fix a few package compatibility issues with PHP 8.1.

## 3.3.0 - 2021-09-26

### Added

- Allow use of an existing answers file when creating new projects.

  When using Composer's `create-project` command, users may now provide an environment variable (`STARTER_KIT_ANSWERS_FILE`) to indicate the location of an already-existing answers file to use when setting up a new library. This must be a JSON file including properties defined in `Ramsey\Dev\LibraryStarterKit\Answers`. To completely turn off the question prompts, include the property `skipPrompts: true`.

  For example:

  ```shell
  STARTER_KIT_ANSWERS_FILE=/path/to/answers.json composer create-project ramsey/php-library-starter-kit YOUR-PROJECT-NAME
  ```

- Include a builder task (`Ramsey\Dev\LibraryStarterKit\Task\Builder\FixStyle`) that fixes any style issues before instantiating the new repository. This avoids coding standards errors caused by out-of-order `use` statements, etc.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 3.2.2 - 2021-08-11

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Remove description case from Conventional Commits configuration, since the lowercase requirement causes confusion
- Remove PHPStan and Psalm checks from pre-commit hook, since these can result in false positives/negatives when analyzing only a few files at a time
- Use https in URLs
- Use the correct branch name in GitHub URLs

## 3.2.1 - 2021-08-07

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fix link to SECURITY.md

## 3.2.0 - 2021-08-06

### Added

- Provide Creative Commons Zero v1.0 Universal as a license option

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Write a Coordinated Disclosure section to the README if choosing to include a security policy
- Move the pull request template so that GitHub will use it

## 3.1.1 - 2021-08-05

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Detect author name and email using Git config

## 3.1.0 - 2021-08-05

### Added

- Use the author name and email address for git config, if necessary
- Require the author email address

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Set the branch name *after* the initial commit to avoid errors on older versions of Git

## 3.0.3 - 2021-08-04

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Support older versions of Git that do not implement the `-b` option for `git init`
- Improve exception handling to aid with debugging

## 3.0.2 - 2021-07-18

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Add missing newline to end of generated composer.json file

## 3.0.1 - 2021-07-18

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Make sure CaptainHook installation runs after the repository initialization

## 3.0.0 - 2021-07-14

### Added

- Allow users to exit the wizard and restart it later, saving their answers
- Use ramsey/devtools instead of `vnd:*` scripts in the local `composer.json`
- Use [CaptainHook](https://github.com/captainhookphp/captainhook) to manage Git hooks
  - Enforce the use of [Conventional Commits](https://www.conventionalcommits.org)
  - Validate and check normalization of `composer.json` in pre-commit hook
  - Run syntax, style, and static analysis checks in pre-commit hook
  - Run `composer install` on post-merge and post-checkout hooks
  - Run `composer test` in pre-push hook
- Add option to include a security policy (vulnerability disclosure policy) as part of the wizard
- Add [GitHub Actions](https://docs.github.com/en/actions) configuration for CI workflows
- Add [Codecov](https://about.codecov.io) configuration for viewing code coverage reports
- Use ramsey/coding-standard

### Changed

- Rename from ramsey/php-library-skeleton to ramsey/php-library-starter-kit
- Major re-working of the library to use [symfony/console](https://symfony.com/doc/current/components/console.html)

### Deprecated

- Nothing.

### Removed

- Remove dependencies on NodeJS and npm packages
- Remove all `vnd:*` scripts from `composer.json`
- Remove `bin/repl`, since ramsey/devtools uses ramsey/composer-repl
- Remove Travis CI configuration, in favor of GitHub Actions
- Remove Coveralls configuration, in favor of Codecov

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
