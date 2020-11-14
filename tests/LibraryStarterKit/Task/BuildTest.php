<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Task;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Build;
use Ramsey\Dev\LibraryStarterKit\Task\Builder;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Style\SymfonyStyle;

class BuildTest extends TestCase
{
    public function testGetAnswers(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $answers = new Answers();

        $build = new Build($setup, $console, $answers);

        $this->assertSame($answers, $build->getAnswers());
    }

    public function testGetSetup(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $build = new Build($setup, $console, new Answers());

        $this->assertSame($setup, $build->getSetup());
    }

    public function testGetConsole(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $build = new Build($setup, $console, new Answers());

        $this->assertSame($console, $build->getConsole());
    }

    public function testGetBuilders(): void
    {
        /** @var Setup & MockInterface $setup */
        $setup = $this->mockery(Setup::class);

        /** @var SymfonyStyle & MockInterface $console */
        $console = $this->mockery(SymfonyStyle::class);

        $build = new Build($setup, $console, new Answers());

        $builders = $build->getBuilders();

        $this->assertContainsOnlyInstancesOf(Builder::class, $builders);
        $this->assertCount(15, $builders);
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
