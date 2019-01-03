<?php
declare(strict_types=1);

namespace Ramsey\Skeleton\Test\Task;

use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Test\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig_Environment;

class BuildTest extends TestCase
{
    public function testSetGetVariables()
    {
        $variables = [
            'foo' => 'bar',
        ];

        $task = \Mockery::mock(Build::class);
        $task->shouldReceive('setVariables')->passthru();
        $task->shouldReceive('getVariables')->passthru();

        $this->assertSame($task, $task->setVariables($variables));
        $this->assertSame($variables, $task->getVariables());
    }

    public function testSetGetTwigEnvironment()
    {
        $twig = \Mockery::mock(Twig_Environment::class);

        $task = \Mockery::mock(Build::class);
        $task->shouldReceive('setTwigEnvironment')->passthru();
        $task->shouldReceive('getTwigEnvironment')->passthru();

        $this->assertSame($task, $task->setTwigEnvironment($twig));
        $this->assertSame($twig, $task->getTwigEnvironment());
    }

    public function testRun()
    {
        $variables = [
            'fooToken' => 'tokenValue',
        ];

        $templates = [
            \Mockery::mock(\SplFileInfo::class, [
                'getRelativePathname' => 'foo/bar.md',
                'getContents' => "# Hello\n\nThis is Markdown.",
            ]),
            \Mockery::mock(\SplFileInfo::class, [
                'getRelativePathname' => 'fooToken/bar/baz.php.twig',
            ]),
        ];

        $twig = \Mockery::mock(Twig_Environment::class);
        $twig->expects()->render('fooToken/bar/baz.php.twig', $variables)->andReturn('foobar');

        $finder = \Mockery::mock(Finder::class);
        $finder->expects()->ignoreDotFiles(false)->andReturnSelf();
        $finder->expects()->files()->andReturnSelf();
        $finder->expects()->in(matchesPattern('/\/skeleton$/'))->andReturn($templates);

        $filesystem = \Mockery::mock(Filesystem::class);
        $filesystem->expects()->dumpFile('tokenValue/bar/baz.php', 'foobar');
        $filesystem->expects()->dumpFile('foo/bar.md', "# Hello\n\nThis is Markdown.");

        $task = \Mockery::mock(Build::class, [
            'getVariables' => $variables,
            'getTwigEnvironment' => $twig,
            'getFinder' => $finder,
            'getFilesystem' => $filesystem,
        ]);

        $task->shouldReceive('run')->passthru();

        $task->run();
    }
}
