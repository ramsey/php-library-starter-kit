<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console;

use Ramsey\Dev\LibraryStarterKit\Console\StyleFactory;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class StyleFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $input = new ArgvInput([]);
        $output = new NullOutput();
        $factory = new StyleFactory();

        $this->assertInstanceOf(SymfonyStyle::class, $factory->factory($input, $output));
    }
}
