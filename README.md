<h1 align="center"><!-- NAME_START -->PHP Library Starter Kit<!-- NAME_END --></h1>

<!-- BADGES_START -->
<p align="center">
    <strong>A starter kit for quickly setting up a new PHP library package.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/php-library-starter-kit"><img src="http://img.shields.io/badge/source-ramsey/php--library--starter--kit-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/php-library-starter-kit"><img src="https://img.shields.io/packagist/v/ramsey/php-library-starter-kit.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/php-library-starter-kit.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/php-library-starter-kit/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/php-library-starter-kit.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ramsey/php-library-starter-kit/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/workflow/status/ramsey/php-library-starter-kit/build/main?style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/php-library-starter-kit"><img src="https://img.shields.io/codecov/c/gh/ramsey/php-library-starter-kit?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/php-library-starter-kit"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fphp-library-starter-kit%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>
<!-- BADGES_END -->

<!-- DESC_START -->
## About

ramsey/php-library-starter-kit is a package that may be used to generate a basic
PHP library project directory structure, complete with many of the starting
files (i.e. README, LICENSE, GitHub issue templates, PHPUnit configuration,
etc.) that are commonly found in PHP libraries. You may use the project
directory that's created as a starting point for creating your own PHP libraries.
<!-- DESC_END -->

<!-- COC_START -->
This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.
<!-- COC_END -->

<!-- USAGE_START -->
## Usage

``` bash
composer create-project ramsey/php-library-starter-kit YOUR-PROJECT-NAME
```

Running this command will create a new repository containing the same files
and structure as this repository. Afterward, it will run the
`Ramsey\Dev\LibraryStarterKit\Wizard::start()` method to set up the project, which will
walk you through a series of questions and make changes to files based on your
answers. When complete, it will remove the `./src/LibraryStarterKit` and `./tests/LibraryStarterKit`
directories, leaving everything else in place with an initial commit.
<!-- USAGE_END -->

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

<!-- SECURITY_START -->
## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue in software that is maintained in this repository, please read
[SECURITY.md][] for instructions on submitting a vulnerability report.
<!-- SECURITY_END -->

<!-- FAQ_START -->
## FAQs

### Why did you include package/tool *x* and not *y*?

I created this project starter kit for my own uses, and these are the
common files, packages, and tools I use in my PHP libraries. If you like what
you see, feel free to use it. If you like some of it but not all, fork it and
customize it to fit your needs. I hope you find it helpful!
<!-- FAQ_END -->

<!-- COPYRIGHT_START -->
## Copyright and License

The ramsey/php-library-starter-kit library is copyright © [Ben Ramsey](https://benramsey.com)
and licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE)
for more information.
<!-- COPYRIGHT_END -->
