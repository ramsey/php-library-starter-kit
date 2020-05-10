<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use ArrayObject;
use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\UpdatePackageJson;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UpdatePackageJsonTest extends SkeletonTestCase
{
    public function testBuild(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating package.json</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame('/path/to/app/package.json', $path);
                $this->assertJsonStringEqualsJsonString(
                    $this->packageContentsExpected(),
                    $contents
                );

                return true;
            });

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => $this->packageContentsOriginal()]),
                    $this->mockery(SplFileInfo::class, ['getContents' => '']),
                ]
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('package.json');

        $answers = new Answers();
        $answers->packageName = 'a-vendor/package-name';
        $answers->packageDescription = 'This is a test package.';
        $answers->authorName = 'Jane Doe';
        $answers->authorEmail = 'jdoe@example.com';
        $answers->authorUrl = 'https://example.com/jane';

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

        $builder = new UpdatePackageJson($task);

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenPackageJsonContainsInvalidJson(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating package.json</info>');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => 'null']),
                ]
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('package.json');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $builder = new UpdatePackageJson($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to decode contents of package.json');

        $builder->build();
    }

    public function testBuildThrowsExceptionWhenPackageJsonCannotBeFound(): void
    {
        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating package.json</info>');

        $finder = $this->mockery(Finder::class, [
            'getIterator' => new ArrayObject(
                [
                    $this->mockery(SplFileInfo::class, ['getContents' => null]),
                ]
            ),
        ]);
        $finder->expects()->in('/path/to/app')->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->depth('== 0')->andReturnSelf();
        $finder->expects()->name('package.json');

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAppPath' => '/path/to/app',
            'getFinder' => $finder,
            'getIO' => $io,
        ]);

        $builder = new UpdatePackageJson($task);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to get contents of package.json');

        $builder->build();
    }

    private function packageContentsOriginal(): string
    {
        return <<<'EOD'
            {
              "name": "@ramsey/php-library-skeleton",
              "version": "1.0.0",
              "description": "A tool to quickly set up the base files of a PHP library package.",
              "license": "MIT",
              "author": {
                "name": "Ben Ramsey",
                "email": "ben@benramsey.com",
                "url": "https://benramsey.com"
              },
              "scripts": {
                "commit": "git-cz"
              },
              "husky": {
                "hooks": {
                  "pre-commit": "yarn lint-staged --relative",
                  "commit-msg": "yarn commitlint -E HUSKY_GIT_PARAMS"
                }
              },
              "lint-staged": {
                "*.json": [
                  "json-beautify",
                  "addnl"
                ],
                "composer.json": [
                  "composer --quiet normalize"
                ],
                "package.json": [
                  "sort-package-json"
                ],
                "{src,tests}/**/*.php": [
                  "composer --quiet vnd:lint:fix",
                  "composer --quiet vnd:lint",
                  "composer --quiet vnd:analyze"
                ]
              },
              "config": {
                "commitizen": {
                  "path": "./node_modules/cz-conventional-changelog"
                }
              },
              "devDependencies": {
                "@commitlint/cli": "^8.3.5",
                "@commitlint/config-conventional": "^8.3.4",
                "add-newlines": "^0.2.0",
                "commitizen": "^4.1.2",
                "cz-conventional-changelog": "^3.2.0",
                "husky": "^4.2.5",
                "json-beautify": "^1.1.1",
                "lint-staged": "^10.2.2",
                "sort-package-json": "^1.42.2"
              }
            }
        EOD;
    }

    private function packageContentsExpected(): string
    {
        return <<<'EOD'
            {
              "name": "@a-vendor/package-name",
              "version": "1.0.0",
              "description": "This is a test package.",
              "license": "MIT",
              "author": {
                "name": "Jane Doe",
                "email": "jdoe@example.com",
                "url": "https://example.com/jane"
              },
              "scripts": {
                "commit": "git-cz"
              },
              "husky": {
                "hooks": {
                  "pre-commit": "yarn lint-staged --relative",
                  "commit-msg": "yarn commitlint -E HUSKY_GIT_PARAMS"
                }
              },
              "lint-staged": {
                "*.json": [
                  "json-beautify",
                  "addnl"
                ],
                "composer.json": [
                  "composer --quiet normalize"
                ],
                "package.json": [
                  "sort-package-json"
                ],
                "{src,tests}/**/*.php": [
                  "composer --quiet vnd:lint:fix",
                  "composer --quiet vnd:lint",
                  "composer --quiet vnd:analyze"
                ]
              },
              "config": {
                "commitizen": {
                  "path": "./node_modules/cz-conventional-changelog"
                }
              },
              "devDependencies": {
                "@commitlint/cli": "^8.3.5",
                "@commitlint/config-conventional": "^8.3.4",
                "add-newlines": "^0.2.0",
                "commitizen": "^4.1.2",
                "cz-conventional-changelog": "^3.2.0",
                "husky": "^4.2.5",
                "json-beautify": "^1.1.1",
                "lint-staged": "^10.2.2",
                "sort-package-json": "^1.42.2"
              }
            }
        EOD;
    }
}
