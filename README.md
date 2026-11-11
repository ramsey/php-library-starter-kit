<h1 align="center"><!-- NAME_START -->PHP Library Starter Kit<!-- NAME_END --></h1>

<!-- BADGES_START -->
<p align="center">
    <strong>A starter kit for quickly setting up a new PHP library package.</strong>
</p>

<p align="center">
    <a href="https://github.com/ramsey/php-library-starter-kit"><img src="http://img.shields.io/badge/source-ramsey/php--library--starter--kit-blue.svg" alt="Source Code"></a>
    <a href="https://packagist.org/packages/ramsey/php-library-starter-kit"><img src="https://img.shields.io/packagist/v/ramsey/php-library-starter-kit.svg?label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/php-library-starter-kit.svg?colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/php-library-starter-kit/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/php-library-starter-kit.svg?colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ramsey/php-library-starter-kit/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/ramsey/php-library-starter-kit/continuous-integration.yml?branch=main&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/ramsey/php-library-starter-kit"><img src="https://img.shields.io/codecov/c/gh/ramsey/php-library-starter-kit?label=codecov&logo=codecov" alt="Codecov Code Coverage"></a>
</p>
<!-- BADGES_END -->

<!-- DESC_START -->
## About

ramsey/php-library-starter-kit is a package that may be used to generate a basic PHP library project directory structure, complete with many of the starting files (i.e. README, LICENSE, GitHub issue templates, PHPUnit configuration, etc.) that are commonly found in PHP libraries. You may use the project directory that's created as a starting point for creating your own PHP libraries.
<!-- DESC_END -->

<!-- COC_START -->
This project adheres to a [code of conduct](CODE_OF_CONDUCT.md). By participating in this project and its community, you are expected to uphold this code.
<!-- COC_END -->

<!-- USAGE_START -->
## Usage

```bash
composer create-project ramsey/php-library-starter-kit YOUR-PROJECT-NAME
```

Running this command will create a new repository containing the same files and structure as this repository. Afterward, it will run the `Ramsey\Dev\LibraryStarterKit\Wizard::start()` method to set up the project, which will walk you through a series of questions and make changes to files based on your answers. When complete, it will remove the `./src/LibraryStarterKit` and `./tests/LibraryStarterKit` directories, leaving everything else in place with an initial commit.

### Using an existing answers file

When executing `create-project`, if you exit the program while in the middle of the question prompts, you might notice it creates a `.starter-kit-answers` file in the project directory. When you return later and run `composer starter-kit`, it will use this file to pre-fill any questions you've already answered. Once finished, the starter kit removes this file.

You may also use an existing answers file to provide all your answers to the prompts, including skipping the question prompts. To do this, set an environment variable with the path to your answers file:

```bash
STARTER_KIT_ANSWERS_FILE=/path/to/starter-kit-answers.json
```

To skip the question prompts, make sure you include the `skipPrompts` property in the answers file, and set it to `true`.

The answers file is a JSON object, consisting of all the public properties found in `Ramsey\Dev\LibraryStarterKit\Answers`.

The `funding` answer is `null` for no sponsor button, or an object whose keys are the platform keys of GitHub's `FUNDING.yml` (`github`, `patreon`, `open_collective`, `ko_fi`, `tidelift`, `community_bridge`, `liberapay`, `issuehunt`, `polar`, `buy_me_a_coffee`, `thanks_dev`, `custom`) and whose values are a list of up to four names for `github`, a list of up to four URLs for `custom`, and one string otherwise, such as `{"github": ["example"], "tidelift": "packagist/acme/awesome"}`.

For example:

```json
{
    "authorEmail": "author@example.com",
    "authorHoldsCopyright": true,
    "authorName": "Author Smith",
    "authorUrl": "https://example.com/",
    "codeOfConduct": "Contributor-2.0",
    "codeOfConductCommittee": null,
    "codeOfConductEmail": "conduct@example.com",
    "codeOfConductPoliciesUrl": null,
    "codeOfConductReportingUrl": null,
    "copyrightEmail": "author@example.com",
    "copyrightHolder": "Acme, Inc.",
    "copyrightUrl": "https://example.com/acme",
    "copyrightYear": "2021",
    "funding": null,
    "githubUsername": "example",
    "license": "MIT",
    "packageDescription": "An awesome library that does stuff.",
    "packageKeywords": [
        "awesome",
        "stuff"
    ],
    "packageName": "acme/awesome",
    "packageNamespace": "Acme\\Awesome",
    "projectName": "My Awesome Library",
    "securityPolicy": true,
    "securityPolicyContactEmail": "security@example.com",
    "securityPolicyContactFormUrl": null,
    "skipPrompts": true,
    "vendorName": "acme"
}
```
<!-- USAGE_END -->

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with [CONTRIBUTING.md](CONTRIBUTING.md).

<!-- SECURITY_START -->
## Coordinated vulnerability disclosure policy

Keeping user information safe and secure is a top priority, and we welcome the contribution of external security researchers. If you believe you've found a security issue in software that is maintained in this repository, please read [SECURITY.md](SECURITY.md) for instructions on submitting a vulnerability report.
<!-- SECURITY_END -->

<!-- FAQ_START -->
## FAQs

### Why did you include package/tool *x* and not *y*?

I created this project starter kit for my own uses, and these are the common files, packages, and tools I use in my PHP libraries. If you like what you see, feel free to use it. If you like some of it but not all, fork it and customize it to fit your needs. I hope you find it helpful!
<!-- FAQ_END -->

<!-- COPYRIGHT_START -->
## Copyright and license

ramsey/php-library-starter-kit is copyright © [Ben Ramsey](https://ben.ramsey.dev) and licensed for use under the terms of the MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
<!-- COPYRIGHT_END -->
