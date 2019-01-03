# ramsey/php-library-skeleton

[![Source Code][badge-source]][source]
[![Latest Version][badge-release]][packagist]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Build Status][badge-build]][build]
[![Coverage Status][badge-coverage]][coverage]
[![Total Downloads][badge-downloads]][downloads]

ramsey/php-library-skeleton is a package that may be used to generate a basic
PHP library project directory structure, complete with many of the starting
files (i.e. README, LICENSE, GitHub issue templates, PHPUnit configuration,
etc.) that are commonly found in PHP libraries. The project directory that's
created may be used as a starting point for creating your own PHP libraries.

This project adheres to a [Contributor Code of Conduct][conduct]. By
participating in this project and its community, you are expected to uphold this
code.


## Installation

ramsey/php-library-skeleton is not a library that you install in the traditional
way using `composer require`. Instead, you'll use `composer create-project` to
use this package as a starting part for developing your own PHP library.

Here's how do this, using [Composer][]:

```bash
composer create-project ramsey/php-library-skeleton target-directory
```


## Why did you include *x* and not *y*?

Simply put: I created this project skeleton generator for my own uses, and these
are the common files and boilerplate language I use in my PHP libraries. If you
like what you see, feel free to use it. If you like some of it but not all, fork
it and customize it to fit your needs. I hope you find it helpful!


## Contributing

While this project is mostly for my own needs, contributions are welcome. Please
read [CONTRIBUTING][] for details.


## Copyright and License

The ramsey/php-library-skeleton library is copyright © [Ben Ramsey](https://benramsey.com)
and licensed for use under the MIT License (MIT). Please see [LICENSE][] for
more information.


[conduct]: https://github.com/ramsey/php-library-skeleton/blob/master/.github/CODE_OF_CONDUCT.md
[composer]: http://getcomposer.org/
[contributing]: https://github.com/ramsey/php-library-skeleton/blob/master/.github/CONTRIBUTING.md

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
