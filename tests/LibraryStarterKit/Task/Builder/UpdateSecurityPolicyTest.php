<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task\Builder;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateSecurityPolicy;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment as TwigEnvironment;

use const DIRECTORY_SEPARATOR;

class UpdateSecurityPolicyTest extends TestCase
{
    public function testBuild(): void
    {
        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Updating SECURITY.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem
            ->shouldReceive('dumpFile')
            ->once()
            ->withArgs(function (string $path, string $contents) {
                $this->assertSame(
                    '/path/to/app/SECURITY.md',
                    $path,
                );
                $this->assertSame('securityPolicyContents', $contents);

                return true;
            });

        $twig = $this->mockery(TwigEnvironment::class);

        $twig
            ->expects()
            ->render(
                'security-policy' . DIRECTORY_SEPARATOR . 'HackerOne.md.twig',
                $this->answers->getArrayCopy(),
            )
            ->andReturn('securityPolicyContents');

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getTwigEnvironment' => $twig,
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

        $builder = new UpdateSecurityPolicy($build);

        $builder->build();
    }

    public function testBuildRemovesSecurityPolicyFile(): void
    {
        $this->answers->securityPolicy = false;

        $console = $this->mockery(SymfonyStyle::class);
        $console->expects()->section('Removing SECURITY.md');

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->shouldReceive('dumpFile')->never();
        $filesystem->expects()->remove('/path/to/app/SECURITY.md');

        $twig = $this->mockery(TwigEnvironment::class);

        $twig->shouldReceive('render')->never();

        $environment = $this->mockery(Setup::class, [
            'getAppPath' => '/path/to/app',
            'getFilesystem' => $filesystem,
            'getTwigEnvironment' => $twig,
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

        $builder = new UpdateSecurityPolicy($build);

        $builder->build();
    }
}
