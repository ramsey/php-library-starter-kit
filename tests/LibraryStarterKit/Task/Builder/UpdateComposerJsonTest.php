<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateComposerJson;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UpdateComposerJsonTest extends TestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating composer.json</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/composer.json', $path);
                $this->assertJsonStringEqualsJsonString(
                    $this->composerContentsExpected(),
                    $contents,
                );

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => $this->composerContentsOriginal()]),
                    $this->mockery(SplFileInfo::class, ['getContents' => '']),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $answers = new Answers();
        $answers->packageName = 'a-vendor/package-name';
        $answers->packageDescription = 'This is a test package.';
        $answers->packageKeywords = ['test', 'package'];
        $answers->authorName = 'Jane Doe';
        $answers->authorEmail = 'jdoe@example.com';
        $answers->authorUrl = 'https://example.com/jane';
        $answers->license = 'Apache-2.0';

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateComposerJson($task);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenComposerContentsContainInvalidJson(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating composer.json</info>');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => 'null']),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $builder = new UpdateComposerJson($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to decode contents of composer.json');

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenComposerJsonCannotBeFound(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating composer.json</info>');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $builder = new UpdateComposerJson($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of composer.json');

        $builder->build();
    }

    public function testBuildWithMinimalComposerJson(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating composer.json</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/composer.json', $path);
                $this->assertJsonStringEqualsJsonString(
                    $this->composerContentsExpectedMinimal(),
                    $contents,
                );

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(
                        SplFileInfo::class,
                        [
                            'getContents' => $this->composerContentsOriginalMinimal(),
                        ],
                    ),
                ],
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $answers = new Answers();
        $answers->packageName = 'a-vendor/package-name';
        $answers->packageDescription = 'This is a test package.';
        $answers->authorName = 'Jane Doe';
        $answers->license = 'MPL-2.0';

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $builder = new UpdateComposerJson($task);

        $builder->build();
    }

    private function composerContentsOriginal(): string
    {
        return <<<'EOD'
            {
              "name": "ramsey/php-library-starter-kit",
              "type": "project",
              "description": "A tool to quickly set up the base files of a PHP library package.",
              "keywords": [
                "skeleton",
                "package",
                "library"
              ],
              "license": "MIT",
              "authors": [
                {
                  "name": "Ben Ramsey",
                  "email": "ben@benramsey.com",
                  "homepage": "https://benramsey.com"
                }
              ],
              "require": {
                "php": "^7.4",
                "ext-json": "*",
                "symfony/filesystem": "^5",
                "symfony/finder": "^5",
                "symfony/process": "^5",
                "twig/twig": "^3"
              },
              "require-dev": {
                "composer/composer": "^1.10",
                "dealerdirect/phpcodesniffer-composer-installer": "^0.6.2",
                "ergebnis/composer-normalize": "^2.5",
                "hamcrest/hamcrest-php": "^2",
                "mockery/mockery": "^1.3",
                "php-parallel-lint/php-parallel-lint": "^1.2",
                "phpstan/extension-installer": "^1",
                "phpstan/phpstan": "^0.12.25",
                "phpstan/phpstan-mockery": "^0.12.5",
                "phpstan/phpstan-phpunit": "^0.12.8",
                "phpunit/phpunit": "^9.1",
                "psy/psysh": "^0.10.4",
                "slevomat/coding-standard": "^6.3",
                "squizlabs/php_codesniffer": "^3.5",
                "vimeo/psalm": "^3.11"
              },
              "config": {
                "sort-packages": true
              },
              "autoload": {
                "psr-4": {
                  "Ramsey\\Dev\\LibraryStarterKit\\": "src/LibraryStarterKit/",
                  "Vendor\\SubNamespace\\": "src/"
                }
              },
              "autoload-dev": {
                "psr-4": {
                  "Ramsey\\Test\\Dev\\LibraryStarterKit\\": "tests/LibraryStarterKit/",
                  "Vendor\\Console\\": "resources/console/",
                  "Vendor\\Test\\SubNamespace\\": "tests/"
                },
                "files": [
                  "vendor/hamcrest/hamcrest-php/hamcrest/Hamcrest.php"
                ]
              },
              "scripts": {
                "post-create-project-cmd": [
                  "Ramsey\\Dev\\LibraryStarterKit\\Setup::wizard",
                  "@vnd:test:all"
                ],
                "post-root-package-install": "git init",
                "vnd:analyze": [
                  "@vnd:analyze:phpstan",
                  "@vnd:analyze:psalm"
                ],
                "vnd:analyze:phpstan": "phpstan analyse --no-progress",
                "vnd:analyze:psalm": "psalm --diff --diff-methods --show-info=true --config=psalm.xml",
                "vnd:build:clean": "git clean -fX build/.",
                "vnd:build:clear-cache": "git clean -fX build/cache/.",
                "vnd:lint": [
                  "parallel-lint src tests",
                  "phpcs --cache=build/cache/phpcs.cache"
                ],
                "vnd:lint:fix": "./bin/lint-fix.sh",
                "vnd:repl": [
                  "echo ; echo 'Type ./bin/repl to start the REPL.'"
                ],
                "vnd:test": "phpunit",
                "vnd:test:all": [
                  "@vnd:lint",
                  "@vnd:analyze",
                  "@vnd:test"
                ],
                "vnd:test:coverage:ci": "phpunit --coverage-clover build/logs/clover.xml",
                "vnd:test:coverage:html": "phpunit --coverage-html build/coverage"
              },
              "scripts-descriptions": {
                "vnd:analyze": "Performs static analysis on the code base.",
                "vnd:analyze:phpstan": "Runs the PHPStan static analyzer.",
                "vnd:analyze:psalm": "Runs the Psalm static analyzer.",
                "vnd:build:clean": "Removes everything not under version control from the build directory.",
                "vnd:build:clear-cache": "Removes everything not under version control from build/cache/.",
                "vnd:lint": "Checks all source code for coding standards issues.",
                "vnd:lint:fix": "Checks source code for coding standards issues and fixes them, if possible.",
                "vnd:repl": "Note: Use ./bin/repl to run the REPL.",
                "vnd:test": "Runs the full unit test suite.",
                "vnd:test:all": "Runs linting, static analysis, and unit tests.",
                "vnd:test:coverage:ci": "Runs the unit test suite and generates a Clover coverage report.",
                "vnd:test:coverage:html": "Runs the unit tests suite and generates an HTML coverage report."
              }
            }
        EOD;
    }

    private function composerContentsExpected(): string
    {
        return <<<'EOD'
            {
              "name": "a-vendor/package-name",
              "type": "library",
              "description": "This is a test package.",
              "keywords": [
                "test",
                "package"
              ],
              "license": "Apache-2.0",
              "authors": [
                {
                  "name": "Jane Doe",
                  "email": "jdoe@example.com",
                  "homepage": "https://example.com/jane"
                }
              ],
              "require": {
                "php": "^7.4"
              },
              "require-dev": {
                "composer/composer": "^1.10",
                "dealerdirect/phpcodesniffer-composer-installer": "^0.6.2",
                "ergebnis/composer-normalize": "^2.5",
                "hamcrest/hamcrest-php": "^2",
                "mockery/mockery": "^1.3",
                "php-parallel-lint/php-parallel-lint": "^1.2",
                "phpstan/extension-installer": "^1",
                "phpstan/phpstan": "^0.12.25",
                "phpstan/phpstan-mockery": "^0.12.5",
                "phpstan/phpstan-phpunit": "^0.12.8",
                "phpunit/phpunit": "^9.1",
                "psy/psysh": "^0.10.4",
                "slevomat/coding-standard": "^6.3",
                "squizlabs/php_codesniffer": "^3.5",
                "vimeo/psalm": "^3.11"
              },
              "config": {
                "sort-packages": true
              },
              "autoload": {
                "psr-4": {
                  "Vendor\\SubNamespace\\": "src/"
                }
              },
              "autoload-dev": {
                "psr-4": {
                  "Vendor\\Console\\": "resources/console/",
                  "Vendor\\Test\\SubNamespace\\": "tests/"
                },
                "files": [
                  "vendor/hamcrest/hamcrest-php/hamcrest/Hamcrest.php"
                ]
              },
              "scripts": {
                "vnd:analyze": [
                  "@vnd:analyze:phpstan",
                  "@vnd:analyze:psalm"
                ],
                "vnd:analyze:phpstan": "phpstan analyse --no-progress",
                "vnd:analyze:psalm": "psalm --diff --diff-methods --show-info=true --config=psalm.xml",
                "vnd:build:clean": "git clean -fX build/.",
                "vnd:build:clear-cache": "git clean -fX build/cache/.",
                "vnd:lint": [
                  "parallel-lint src tests",
                  "phpcs --cache=build/cache/phpcs.cache"
                ],
                "vnd:lint:fix": "./bin/lint-fix.sh",
                "vnd:repl": [
                  "echo ; echo 'Type ./bin/repl to start the REPL.'"
                ],
                "vnd:test": "phpunit",
                "vnd:test:all": [
                  "@vnd:lint",
                  "@vnd:analyze",
                  "@vnd:test"
                ],
                "vnd:test:coverage:ci": "phpunit --coverage-clover build/logs/clover.xml",
                "vnd:test:coverage:html": "phpunit --coverage-html build/coverage"
              },
              "scripts-descriptions": {
                "vnd:analyze": "Performs static analysis on the code base.",
                "vnd:analyze:phpstan": "Runs the PHPStan static analyzer.",
                "vnd:analyze:psalm": "Runs the Psalm static analyzer.",
                "vnd:build:clean": "Removes everything not under version control from the build directory.",
                "vnd:build:clear-cache": "Removes everything not under version control from build/cache/.",
                "vnd:lint": "Checks all source code for coding standards issues.",
                "vnd:lint:fix": "Checks source code for coding standards issues and fixes them, if possible.",
                "vnd:repl": "Note: Use ./bin/repl to run the REPL.",
                "vnd:test": "Runs the full unit test suite.",
                "vnd:test:all": "Runs linting, static analysis, and unit tests.",
                "vnd:test:coverage:ci": "Runs the unit test suite and generates a Clover coverage report.",
                "vnd:test:coverage:html": "Runs the unit tests suite and generates an HTML coverage report."
              }
            }
        EOD;
    }

    private function composerContentsOriginalMinimal(): string
    {
        return <<<'EOD'
            {
              "name": "ramsey/php-library-starter-kit",
              "type": "project",
              "description": "A tool to quickly set up the base files of a PHP library package.",
              "keywords": [
                "skeleton",
                "package",
                "library"
              ],
              "license": "MIT",
              "authors": [
                {
                  "name": "Ben Ramsey",
                  "email": "ben@benramsey.com",
                  "homepage": "https://benramsey.com"
                }
              ]
            }
        EOD;
    }

    private function composerContentsExpectedMinimal(): string
    {
        return <<<'EOD'
            {
              "name": "a-vendor/package-name",
              "type": "library",
              "description": "This is a test package.",
              "keywords": [],
              "license": "MPL-2.0",
              "authors": [
                {
                  "name": "Jane Doe"
                }
              ]
            }
        EOD;
    }
}
