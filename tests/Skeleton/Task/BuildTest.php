<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task;

use Composer\IO\IOInterface;
use Mockery\MockInterface;
use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Builder;
use Ramsey\Test\Skeleton\SkeletonTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig\Environment as TwigEnvironment;

class BuildTest extends SkeletonTestCase
{
    private Build $build;

    public function setUp(): void
    {
        /** @var IOInterface & MockInterface $io */
        $io = $this->mockery(IOInterface::class);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);

        $this->build = new Build('/path/to/app', $io, $filesystem, $finder);
    }

    public function testSetGetAnswers(): void
    {
        $answers = new Answers();

        $this->assertSame($this->build, $this->build->setAnswers($answers));
        $this->assertSame($answers, $this->build->getAnswers());
    }

    public function testSetGetTwigEnvironment(): void
    {
        /** @var TwigEnvironment & MockInterface $twig */
        $twig = $this->mockery(TwigEnvironment::class);

        $this->assertSame($this->build, $this->build->setTwigEnvironment($twig));
        $this->assertSame($twig, $this->build->getTwigEnvironment());
    }

    public function testGetBuilders(): void
    {
        $builders = $this->build->getBuilders();

        $this->assertContainsOnlyInstancesOf(Builder::class, $builders);
        $this->assertCount(17, $builders);
    }

    public function testRun(): void
    {
        $builder1 = $this->mockery(Builder::class);
        $builder1->expects()->build();

        $builder2 = $this->mockery(Builder::class);
        $builder2->expects()->build();

        $builder3 = $this->mockery(Builder::class);
        $builder3->expects()->build();

        $build = $this->mockery(Build::class, [
            'getBuilders' => [
                $builder1,
                $builder2,
                $builder3,
            ],
        ]);

        $build->shouldReceive('run')->passthru();

        $build->run();
    }
}
