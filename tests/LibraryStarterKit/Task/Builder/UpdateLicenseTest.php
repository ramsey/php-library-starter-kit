<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Closure;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateLicense;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment as TwigEnvironment;

use const DIRECTORY_SEPARATOR;

class UpdateLicenseTest extends TestCase
{
    #[DataProvider('licenseFilenames')]
    public function testGetLicenseFilename(string $license, string $filename): void
    {
        $this->assertSame($filename, UpdateLicense::getLicenseFilename($license));
    }

    /**
     * @return iterable<string, array{license: string, filename: string}>
     */
    public static function licenseFilenames(): iterable
    {
        yield 'AGPL-3.0-or-later' => ['license' => 'AGPL-3.0-or-later', 'filename' => 'COPYING'];
        yield 'Apache-2.0' => ['license' => 'Apache-2.0', 'filename' => 'LICENSE'];
        yield 'BSD-2-Clause' => ['license' => 'BSD-2-Clause', 'filename' => 'LICENSE'];
        yield 'BSD-3-Clause' => ['license' => 'BSD-3-Clause', 'filename' => 'LICENSE'];
        yield 'CC0-1.0' => ['license' => 'CC0-1.0', 'filename' => 'LICENSE'];
        yield 'GPL-3.0-or-later' => ['license' => 'GPL-3.0-or-later', 'filename' => 'COPYING'];
        yield 'Hippocratic-2.1' => ['license' => 'Hippocratic-2.1', 'filename' => 'LICENSE'];
        yield 'LGPL-3.0-or-later' => ['license' => 'LGPL-3.0-or-later', 'filename' => 'COPYING.LESSER'];
        yield 'MIT' => ['license' => 'MIT', 'filename' => 'LICENSE'];
        yield 'MIT-0' => ['license' => 'MIT-0', 'filename' => 'LICENSE'];
        yield 'MPL-2.0' => ['license' => 'MPL-2.0', 'filename' => 'LICENSE'];
        yield 'Proprietary' => ['license' => 'Proprietary', 'filename' => 'COPYRIGHT'];
        yield 'Unlicense' => ['license' => 'Unlicense', 'filename' => 'UNLICENSE'];
    }

    #[DataProvider('provideLicensesForTesting')]
    public function testBuild(
        string $license,
        string $filename,
        string $contents,
        callable $additionalChecks,
    ): void {
        $this->answers->license = $license;

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating license and copyright information');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove('LICENSE');
        $filesystem->expects()->dumpFile('/path/to/app/' . $filename, $contents);

        $twigEnvironment = $this->mockery(TwigEnvironment::class);
        $twigEnvironment
            ->expects('render')
            ->with(
                'license' . DIRECTORY_SEPARATOR . $license . '.twig',
                $this->answers->getArrayCopy(),
            )
            ->andReturns($contents);

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getTwigEnvironment' => $twigEnvironment,
        ]);

        $environment
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $this->answers,
            'getConsole' => $console,
            'getSetup' => $environment,
        ]);

        $additionalChecks($twigEnvironment, $filesystem, $this->answers);

        $builder = new UpdateLicense($task);

        $builder->build();
    }

    /**
     * @return list<array{
     *     license: string,
     *     filename: string,
     *     contents: string,
     *     additionalChecks: Closure(TwigEnvironment & MockInterface, Filesystem & MockInterface, Answers): void,
     * }>
     */
    public static function provideLicensesForTesting(): array
    {
        return [
            [
                'license' => 'AGPL-3.0-or-later',
                'filename' => 'COPYING',
                'contents' => 'AGPL-3.0-or-later license contents',
                'additionalChecks' => function (
                    TwigEnvironment & MockInterface $twig,
                    Filesystem & MockInterface $filesystem,
                    Answers $answers,
                ): void {
                    $twig
                        ->expects('render')
                        ->with(
                            'license' . DIRECTORY_SEPARATOR . 'AGPL-3.0-or-later-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturns('AGPL-3.0-or-later notice contents');

                    $filesystem->expects('dumpFile')->with(
                        '/path/to/app/NOTICE',
                        'AGPL-3.0-or-later notice contents',
                    );
                },
            ],
            [
                'license' => 'Apache-2.0',
                'filename' => 'LICENSE',
                'contents' => 'Apache-2.0 license contents',
                'additionalChecks' => function (
                    TwigEnvironment & MockInterface $twig,
                    Filesystem & MockInterface $filesystem,
                    Answers $answers,
                ): void {
                    $twig
                        ->expects('render')
                        ->with(
                            'license' . DIRECTORY_SEPARATOR . 'Apache-2.0-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturns('Apache-2.0 notice contents');

                    $filesystem->expects('dumpFile')->with(
                        '/path/to/app/NOTICE',
                        'Apache-2.0 notice contents',
                    );
                },
            ],
            [
                'license' => 'BSD-2',
                'filename' => 'LICENSE',
                'contents' => 'BSD-2 license contents',
                'additionalChecks' => fn () => null,
            ],
            [
                'license' => 'BSD-3',
                'filename' => 'LICENSE',
                'contents' => 'BSD-3 license contents',
                'additionalChecks' => fn () => null,
            ],
            [
                'license' => 'GPL-3.0-or-later',
                'filename' => 'COPYING',
                'contents' => 'GPL-3.0-or-later license contents',
                'additionalChecks' => function (
                    TwigEnvironment & MockInterface $twig,
                    Filesystem & MockInterface $filesystem,
                    Answers $answers,
                ): void {
                    $twig
                        ->expects('render')
                        ->with(
                            'license' . DIRECTORY_SEPARATOR . 'GPL-3.0-or-later-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturns('GPL-3.0-or-later notice contents');

                    $filesystem->expects('dumpFile')->with(
                        '/path/to/app/NOTICE',
                        'GPL-3.0-or-later notice contents',
                    );
                },
            ],
            [
                'license' => 'LGPL-3.0-or-later',
                'filename' => 'COPYING.LESSER',
                'contents' => 'LGPL-3.0-or-later license contents',
                'additionalChecks' => function (
                    TwigEnvironment & MockInterface $twig,
                    Filesystem & MockInterface $filesystem,
                    Answers $answers,
                ): void {
                    $twig
                        ->expects('render')
                        ->with(
                            'license' . DIRECTORY_SEPARATOR . 'LGPL-3.0-or-later-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturns('LGPL-3.0-or-later notice contents');

                    $filesystem->expects('dumpFile')->with(
                        '/path/to/app/NOTICE',
                        'LGPL-3.0-or-later notice contents',
                    );

                    $twig
                        ->expects('render')
                        ->with(
                            'license' . DIRECTORY_SEPARATOR . 'GPL-3.0-or-later.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturns('GPL-3.0-or-later license contents');

                    $filesystem->expects('dumpFile')->with(
                        '/path/to/app/COPYING',
                        'GPL-3.0-or-later license contents',
                    );
                },
            ],
            [
                'license' => 'MIT',
                'filename' => 'LICENSE',
                'contents' => 'MIT license contents',
                'additionalChecks' => fn () => null,
            ],
            [
                'license' => 'MIT-0',
                'filename' => 'LICENSE',
                'contents' => 'MIT-0 license contents',
                'additionalChecks' => fn () => null,
            ],
            [
                'license' => 'MPL-2.0',
                'filename' => 'LICENSE',
                'contents' => 'MPL-2.0 license contents',
                'additionalChecks' => function (
                    TwigEnvironment & MockInterface $twig,
                    Filesystem & MockInterface $filesystem,
                    Answers $answers,
                ): void {
                    $twig
                        ->expects('render')
                        ->with(
                            'license' . DIRECTORY_SEPARATOR . 'MPL-2.0-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturns('MPL-2.0 notice contents');

                    $filesystem->expects('dumpFile')->with(
                        '/path/to/app/NOTICE',
                        'MPL-2.0 notice contents',
                    );
                },
            ],
            [
                'license' => 'Proprietary',
                'filename' => 'COPYRIGHT',
                'contents' => 'proprietary license contents',
                'additionalChecks' => fn () => null,
            ],
            [
                'license' => 'Unlicense',
                'filename' => 'UNLICENSE',
                'contents' => 'unlicense contents',
                'additionalChecks' => fn () => null,
            ],
        ];
    }
}
