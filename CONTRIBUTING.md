# Contributing

Contributions are welcome. This project accepts pull requests on [GitHub].

This project adheres to a [code of conduct]. By participating in this project and its community, you are expected to uphold this code.

## Communication channels

You can find help and discussion in the following places:

* GitHub Issues: <https://github.com/ramsey/php-library-starter-kit/issues>

## Reporting bugs

Report bugs using the project's [issue tracker][issues].

⚠️ _**ATTENTION!!!** DO NOT include passwords or other sensitive information in your bug report._

When submitting a bug report, please include enough information to reproduce the bug. A good bug report includes the following sections:

* **Description**

  Provide a short and clear description of the bug.

* **Steps to reproduce**

  Provide steps to reproduce the behavior you are experiencing. Please try to keep this as short as possible. If able, create a reproducible script outside of any framework you are using. This will help us to quickly debug the issue.

* **Expected behavior**

  Provide a short and clear description of what you expect to happen.

* **Screenshots or output**

  If applicable, add screenshots or program output to help explain your problem.

* **Environment details**

  Provide details about the system where you're using this package, such as PHP version and operating system.

* **Additional context**

  Provide any additional context that may help us debug the problem.

## Fixing bugs

This project welcomes pull requests to fix bugs!

If you see a bug report that you'd like to fix, please feel free to do so. Following the directions and guidelines described in the "Adding New Features" section below, you may create bugfix branches and send pull requests.

## Adding new features

If you have an idea for a new feature, it's a good idea to check out the [issues] or active [pull requests] first to see if anyone is already working on the feature. If not, feel free to submit an issue first, asking whether the feature is beneficial to the project. This will save you from doing a lot of development work only to have your feature rejected. We don't enjoy rejecting your hard work, but some features don't fit with the goals of the project.

When you do begin working on your feature, here are some guidelines to consider:

* Your pull request description should clearly detail the changes you have made. We will use this description to update the CHANGELOG. If there is no description, or it does not adequately describe your feature, we may ask you to update the description.
* ramsey/php-library-starter-kit follows a superset of the **[PER-CS coding standard][per-cs]**. Please ensure your code does, too. _Hint: run `composer lint` to check._
* Please **write tests** for any new features you add.
* Please **ensure that tests pass** before submitting your pull request. ramsey/php-library-starter-kit automatically runs tests for pull requests. However, running the tests locally will help save time. _Hint: run `composer test`._
* **Use topic/feature branches.** Please do not ask to pull from your main branch.
  * For more information, see "[Understanding the GitHub flow][gh-flow]."
* **Submit one feature per pull request.** If you have multiple features you wish to submit, please break them into separate pull requests.
* **Write good commit messages.** This project follows the [Conventional Commits] specification and uses Git hooks to ensure all commits follow this standard. Running `composer install` will set up the Git hooks, so when you run `git commit`, you'll be prompted to create a commit using the Conventional Commits rules.

## Developing

To develop this project, you will need [PHP] 8.2 or greater and [Composer].

After cloning this repository locally, execute the following commands:

```bash
cd /path/to/repository
composer install
```

Now, you are ready to develop!

### Tooling

This project uses [CaptainHook] to validate all staged changes prior to commit.

### Coding standards

This project follows a superset of [PER-CS] coding standards, enforced by [PHP_CodeSniffer].

CaptainHook will run coding standards checks as part of the Git pre-commit hook.

You may lint the codebase manually using the following commands:

```bash
# Lint
composer lint

# Attempt to auto-fix coding standards issues
composer fix
```

### Static analysis

This project uses [PHPStan] to perform static analysis of PHP code.

CaptainHook will run static analysis checks as part of the Git pre-push hook.

You may run static analysis manually with the following command:

```bash
composer analyze
```

### Running tests

The following must pass before we will accept a pull request. If this does not pass, it will result in a complete build failure. Before you can run this, be sure to `composer install`.

To run all the tests and coding standards checks, execute the following from the command line, while in the project root directory:

```bash
composer test
```

CaptainHook will automatically run all tests before pushing to the remote repository.

### Mutation testing

This project uses [Infection] to perform mutation testing, which checks that the tests detect small changes made to the code. GitHub Actions runs mutation tests for pull requests.

You may run mutation tests manually with the following command:

```bash
composer mutation
```

### Running `create-project` locally

You can test Composer's `create-project` command locally to make sure the starter kit wizard runs properly with your changes. To do so, use a command similar to the following:

```bash
composer create-project ramsey/php-library-starter-kit YOUR-PROJECT-NAME \
    --repository='{ "type": "path", "url": "/path/to/local/php-library-starter-kit", "options": { "symlink": false } }' \
    --remove-vcs \
    --stability=dev
```

[captainhook]: https://github.com/CaptainHookPhp/captainhook
[code of conduct]: CODE_OF_CONDUCT.md
[composer]: https://getcomposer.org
[conventional commits]: https://www.conventionalcommits.org/
[gh-flow]: https://guides.github.com/introduction/flow/
[github]: https://github.com/ramsey/php-library-starter-kit
[infection]: https://infection.github.io
[issues]: https://github.com/ramsey/php-library-starter-kit/issues
[per-cs]: https://www.php-fig.org/per/coding-style/
[php]: https://www.php.net
[php_codesniffer]: https://github.com/PHPCSStandards/PHP_CodeSniffer
[phpstan]: https://github.com/phpstan/phpstan
[pull requests]: https://github.com/ramsey/php-library-starter-kit/pulls
