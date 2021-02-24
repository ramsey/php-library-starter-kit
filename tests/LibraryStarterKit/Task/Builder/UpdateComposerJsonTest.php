<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use ArrayObject;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateComposerJson;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use RuntimeException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UpdateComposerJsonTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

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

        $this->answers->packageName = 'a-vendor/package-name';
        $this->answers->packageDescription = 'This is a test package.';
        $this->answers->packageKeywords = ['test', 'package'];
        $this->answers->authorName = 'Jane Doe';
        $this->answers->authorEmail = 'jdoe@example.com';
        $this->answers->authorUrl = 'https://example.com/jane';
        $this->answers->license = 'Apache-2.0';

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateComposerJson($build);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenComposerContentsContainInvalidJson(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

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

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
        ]);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getSetup' => $environment,
            'getConsole' => $console,
        ]);

        $builder = new UpdateComposerJson($build);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to decode contents of composer.json');

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenComposerJsonCannotBeFound(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
        ]);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateComposerJson($build);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of composer.json');

        $builder->build();
    }

    public function testBuildWithMinimalComposerJson(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->note('Updating composer.json');

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
            'getIterator' => new ArrayObject([
                $this->mockery(SplFileInfo::class, [
                    'getContents' => $this->composerContentsOriginalMinimal(),
                ]),
            ]),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('composer.json');

        $this->answers->packageName = 'a-vendor/package-name';
        $this->answers->packageDescription = 'This is a test package.';
        $this->answers->authorName = 'Jane Doe';
        $this->answers->license = 'MPL-2.0';

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getFinder' => $finder,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        /** @var Build & MockInterface $build */
        $build = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $builder = new UpdateComposerJson($build);

        $builder->build();
    }

    private function composerContentsOriginal(): string
    {
        return <<<'EOD'
            {
                "name": "ramsey/php-library-starter-kit",
                "type": "project",
                "description": "A starter kit for quickly setting up a new PHP library package.",
                "keywords": [
                    "builder",
                    "library",
                    "package",
                    "skeleton",
                    "template"
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
                    "php": "^7.4 || ^8",
                    "ext-json": "*",
                    "symfony/console": "^5.1",
                    "symfony/filesystem": "^5.1",
                    "symfony/finder": "^5.1",
                    "symfony/process": "^5.1",
                    "twig/twig": "^3.1"
                },
                "require-dev": {
                    "ramsey/devtools": "^1.5"
                },
                "suggest": {
                    "ext-foobar": "Foo bar"
                },
                "config": {
                    "sort-packages": true
                },
                "extra": {
                    "ramsey/conventional-commits": {
                        "configFile": "conventional-commits.json"
                    },
                    "ramsey/devtools": {
                        "command-prefix": "dev"
                    }
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
                        "Vendor\\Test\\SubNamespace\\": "tests/"
                    }
                },
                "minimum-stability": "dev",
                "prefer-stable": true,
                "scripts": {
                    "post-root-package-install": "git init",
                    "post-create-project-cmd": "Ramsey\\Dev\\LibraryStarterKit\\Wizard::start",
                    "starter-kit": "Ramsey\\Dev\\LibraryStarterKit\\Wizard::start"
                },
                "scripts-descriptions": {
                    "starter-kit": "Runs the PHP Library Starter Kit wizard."
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
                "php": "^7.4 || ^8"
              },
              "require-dev": {
                "ramsey/devtools": "^1.5"
              },
              "config": {
                "sort-packages": true
              },
              "extra": {
                "ramsey/conventional-commits": {
                  "configFile": "conventional-commits.json"
                },
                "ramsey/devtools": {
                  "command-prefix": "dev"
                }
              },
              "autoload": {
                "psr-4": {
                  "Vendor\\SubNamespace\\": "src/"
                }
              },
              "autoload-dev": {
                "psr-4": {
                  "Vendor\\Test\\SubNamespace\\": "tests/"
                }
              },
              "minimum-stability": "dev",
              "prefer-stable": true
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
