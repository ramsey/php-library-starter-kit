<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Throwable;
use Vendor\Test\SubNamespace\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    protected Answers $answers;

    protected function setUp(): void
    {
        parent::setUp();

        $filesystem = $this->mockery(Filesystem::class);
        $filesystem->expects()->exists('/path/to/answers.json')->andReturnFalse();

        $this->answers = new Answers('/path/to/answers.json', $filesystem);
    }

    /**
     * Configures and returns a mock object with proper type-hinting
     * for static analysis tools
     *
     * @param class-string<T> $class
     *
     * @return T & MockInterface
     *
     * @throws Throwable
     *
     * @template T
     */
    public function mockery(string $class, mixed ...$arguments): MockInterface
    {
        /** @var T & MockInterface */
        return Mockery::mock($class, ...$arguments);
    }

    /**
     * Configures and returns a mock object for spying with proper type-hinting
     * for static analysis tools
     *
     * @param class-string<T> $class
     *
     * @return T & MockInterface
     *
     * @throws Throwable
     *
     * @template T
     */
    public function mockerySpy(string $class, mixed ...$arguments): MockInterface
    {
        /** @var T & MockInterface */
        return Mockery::spy($class, ...$arguments);
    }
}
