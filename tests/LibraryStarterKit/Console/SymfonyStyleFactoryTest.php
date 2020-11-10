<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console;

use Ramsey\Dev\LibraryStarterKit\Console\SymfonyStyleFactory;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyStyleFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $input = new ArgvInput([]);
        $output = new NullOutput();
        $factory = new SymfonyStyleFactory();

        $this->assertInstanceOf(SymfonyStyle::class, $factory->factory($input, $output));
    }
}
