<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task\Builder;

use Composer\IO\ConsoleIO;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder\UpdateLicense;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment as TwigEnvironment;

use const DIRECTORY_SEPARATOR;

class UpdateLicenseTest extends SkeletonTestCase
{
    /**
     * @dataProvider provideLicensesForTesting
     */
    public function testBuild(
        string $license,
        string $filename,
        string $contents,
        callable $additionalChecks
    ): void {
        $answers = new Answers();
        $answers->license = $license;

        $io = $this->mockery(ConsoleIO::class);
        $io->expects()->write('<info>Updating license and copyright information</info>');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->remove('LICENSE');
        $filesystem->expects()->dumpFile('/path/to/app/' . $filename, $contents);

        $twigEnvironment = $this->mockery(TwigEnvironment::class);
        $twigEnvironment
            ->expects()
            ->render(
                'license' . DIRECTORY_SEPARATOR . $license . '.twig',
                $answers->getArrayCopy(),
            )
            ->andReturn($contents);

        /** @var Build & MockInterface $task */
        $task = $this->mockery(Build::class, [
            'getAnswers' => $answers,
            'getFilesystem' => $filesystem,
            'getIO' => $io,
            'getTwigEnvironment' => $twigEnvironment,
        ]);

        $task
            ->shouldReceive('path')
            ->andReturnUsing(fn (string $path): string => '/path/to/app/' . $path);

        $additionalChecks($twigEnvironment, $filesystem, $answers);

        $builder = new UpdateLicense($task);

        $builder->build();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function provideLicensesForTesting(): array
    {
        return [
            [
                'license' => 'AGPL-3.0-or-later',
                'filename' => 'COPYING',
                'contents' => 'AGPL-3.0-or-later license contents',
                'additionalChecks' => function ($twig, $filesystem, $answers) {
                    $twig
                        ->expects()
                        ->render(
                            'license' . DIRECTORY_SEPARATOR . 'AGPL-3.0-or-later-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturn('AGPL-3.0-or-later notice contents');

                    $filesystem->expects()->dumpFile(
                        '/path/to/app/NOTICE',
                        'AGPL-3.0-or-later notice contents',
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
                'additionalChecks' => function ($twig, $filesystem, $answers) {
                    $twig
                        ->expects()
                        ->render(
                            'license' . DIRECTORY_SEPARATOR . 'GPL-3.0-or-later-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturn('GPL-3.0-or-later notice contents');

                    $filesystem->expects()->dumpFile(
                        '/path/to/app/NOTICE',
                        'GPL-3.0-or-later notice contents',
                    );
                },
            ],
            [
                'license' => 'LGPL-3.0-or-later',
                'filename' => 'COPYING.LESSER',
                'contents' => 'LGPL-3.0-or-later license contents',
                'additionalChecks' => function ($twig, $filesystem, $answers) {
                    $twig
                        ->expects()
                        ->render(
                            'license' . DIRECTORY_SEPARATOR . 'LGPL-3.0-or-later-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturn('LGPL-3.0-or-later notice contents');

                    $filesystem->expects()->dumpFile(
                        '/path/to/app/NOTICE',
                        'LGPL-3.0-or-later notice contents',
                    );

                    $twig
                        ->expects()
                        ->render(
                            'license' . DIRECTORY_SEPARATOR . 'GPL-3.0-or-later.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturn('GPL-3.0-or-later license contents');

                    $filesystem->expects()->dumpFile(
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
                'additionalChecks' => function ($twig, $filesystem, $answers) {
                    $twig
                        ->expects()
                        ->render(
                            'license' . DIRECTORY_SEPARATOR . 'MPL-2.0-NOTICE.twig',
                            $answers->getArrayCopy(),
                        )
                        ->andReturn('MPL-2.0 notice contents');

                    $filesystem->expects()->dumpFile(
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
