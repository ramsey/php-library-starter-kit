# <!-- NAME_START -->ramsey/php-library-skeleton<!-- NAME_END -->

<!-- BADGES_START -->
[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

[badge-source]: http://img.shields.io/badge/source-ramsey/php--library--skeleton-blue.svg?style=flat-square
[badge-release]: https://img.shields.io/packagist/v/ramsey/php-library-skeleton.svg?style=flat-square&label=release
[badge-license]: https://img.shields.io/packagist/l/ramsey/php-library-skeleton.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/ramsey/php-library-skeleton.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/ramsey/php-library-skeleton/master.svg?style=flat-square
[badge-coverage]: https://img.shields.io/coveralls/github/ramsey/php-library-skeleton/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/ramsey/php-library-skeleton.svg?style=flat-square&colorB=mediumvioletred

[source]: https://github.com/ramsey/php-library-skeleton
[packagist]: https://packagist.org/packages/ramsey/php-library-skeleton
[license]: https://github.com/ramsey/php-library-skeleton/blob/master/LICENSE
[php]: https://php.net
[build]: https://travis-ci.org/ramsey/php-library-skeleton
[coverage]: https://coveralls.io/r/ramsey/php-library-skeleton?branch=master
[downloads]: https://packagist.org/packages/ramsey/php-library-skeleton
<!-- BADGES_END -->

<!-- DESC_START -->
ramsey/php-library-skeleton is a package that may be used to generate a basic
PHP library project directory structure, complete with many of the starting
files (i.e. README, LICENSE, GitHub issue templates, PHPUnit configuration,
etc.) that are commonly found in PHP libraries. You may use the project
directory that's created as a starting point for creating your own PHP libraries.
<!-- DESC_END -->

<!-- COC_START -->
This project adheres to a [Contributor Code of Conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to uphold
this code.
<!-- COC_END -->

<!-- USAGE_START -->
## Usage

Running the command below will create a new repository containing the same files
and structure as this skeleton repository. Afterward, it will run the
`Ramsey\Skeleton\Setup::wizard()` callable to set up the project, which will
walk you through a series of questions and make changes to files based on your
answers. When complete, it will remove the `./src/Skeleton` and `./tests/Skeleton`
directories, leaving everything else in place with an initial commit.

``` bash
composer create-project ramsey/php-library-skeleton YOUR-PROJECT-NAME
```
<!-- USAGE_END -->

## Contributing

Contributions are welcome! Before contributing to this project, familiarize
yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

To develop this project, you will need [PHP](https://www.php.net) 7.4 or greater,
[Composer](https://getcomposer.org), [Node.js](https://nodejs.org/), and
[Yarn](https://yarnpkg.com).

After cloning this repository locally, execute the following commands:

``` bash
cd /path/to/repository
composer install
yarn install
```

Now, you are ready to develop!

### Tooling

This project uses [Husky](https://github.com/typicode/husky) and
[lint-staged](https://github.com/okonet/lint-staged) to validate all staged
changes prior to commit.

#### Composer Commands

To see all the commands available in the project `vnd` namespace for
Composer, type:

``` bash
composer list vnd
```

##### Composer Command Autocompletion

If you'd like to have Composer command auto-completion, you may use
[bamarni/symfony-console-autocomplete](https://github.com/bamarni/symfony-console-autocomplete).
Install it globally with Composer:

``` bash
composer global require bamarni/symfony-console-autocomplete
```

Then, in your shell configuration file — usually `~/.bash_profile` or `~/.zshrc`,
but it could be different depending on your settings — ensure that your global
Composer `bin` directory is in your `PATH`, and evaluate the
`symfony-autocomplete` command. This will look like this:

``` bash
export PATH="$(composer config home)/vendor/bin:$PATH"
eval "$(symfony-autocomplete)"
```

Now, you can use the `tab` key to auto-complete Composer commands:

``` bash
composer vnd:[TAB][TAB]
```

#### Coding Standards

This project follows a superset of [PSR-12](https://www.php-fig.org/psr/psr-12/)
coding standards, enforced by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).
The project PHP_CodeSniffer configuration may be found in `phpcs.xml.dist`.

lint-staged will run PHP_CodeSniffer before committing. It will attempt to fix
any errors it can, and it will reject the commit if there are any un-fixable
issues. Many issues can be fixed automatically and will be done so pre-commit.

You may lint the entire codebase using PHP_CodeSniffer with the following
commands:

``` bash
# Lint
composer vnd:lint

# Lint and autofix
composer vnd:lint:fix
```

#### Static Analysis

This project uses a combination of [PHPStan](https://github.com/phpstan/phpstan)
and [Psalm](https://github.com/vimeo/psalm) to provide static analysis of PHP
code. Configurations for these are in `phpstan.neon` and `psalm.xml`,
respectively.

lint-staged will run PHPStan and Psalm before committing. The pre-commit hook
does not attempt to fix any static analysis errors. Instead, the commit will
fail, and you must fix the errors manually.

You may run static analysis manually across the whole codebase with the
following command:

``` bash
# Static analysis
composer vnd:analyze
```

### Project Structure

This project uses [pds/skeleton](https://github.com/php-pds/skeleton) as its
base folder structure and layout.

| Name              | Description                                    |
| ------------------| ---------------------------------------------- |
| **bin/**          | Commands and scripts for this project          |
| **build/**        | Cache, logs, reports, etc. for project builds  |
| **docs/**         | Project-specific documentation                 |
| **resources/**    | Additional resources for this project          |
| **src/**          | Project library and application source code    |
| **tests/**        | Tests for this project                         |

<!-- FAQ_START -->
## FAQs

### Why did you include package/tool *x* and not *y*?

I created this project skeleton generator for my own uses, and these are the
common files, packages, and tools I use in my PHP libraries. If you like what
you see, feel free to use it. If you like some of it but not all, fork it and
customize it to fit your needs. I hope you find it helpful!
<!-- FAQ_END -->

<!-- COPYRIGHT_START -->
## Copyright and License

The ramsey/php-library-skeleton library is copyright © [Ben Ramsey](https://benramsey.com)
and licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE)
for more information.
<!-- COPYRIGHT_END -->
