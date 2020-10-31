<h1 align="center"><!-- NAME_START -->ramsey/php-library-skeleton<!-- NAME_END --></h1>

<!-- BADGES_START -->
<p align="center">
    <strong>A tool to quickly set up the base files of a PHP library package.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/php-library-skeleton"><img src="http://img.shields.io/badge/source-ramsey/php--library--skeleton-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/php-library-skeleton"><img src="https://img.shields.io/packagist/v/ramsey/php-library-skeleton.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/php-library-skeleton.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/php-library-skeleton/actions?query=workflow%3ACI"><img src="https://img.shields.io/github/workflow/status/ramsey/php-library-skeleton/CI?label=CI&logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/php-library-skeleton"><img src="https://img.shields.io/codecov/c/gh/ramsey/php-library-skeleton?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/php-library-skeleton"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fphp-library-skeleton%2Fcoverage" alt="Psalm Type Coverage"></a>
    <a href="https://github.com/ramsey/php-library-skeleton/blob/master/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/php-library-skeleton.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://packagist.org/packages/ramsey/php-library-skeleton/stats"><img src="https://img.shields.io/packagist/dt/ramsey/php-library-skeleton.svg?style=flat-square&colorB=darkmagenta" alt="Package downloads on Packagist"></a>
    <a href="https://phpc.chat/channel/ramsey"><img src="https://img.shields.io/badge/phpc.chat-%23ramsey-darkslateblue?style=flat-square" alt="Chat with the maintainers"></a>
</p>
<!-- BADGES_END -->

<!-- DESC_START -->
## About

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

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

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
