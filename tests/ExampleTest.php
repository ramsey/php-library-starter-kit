<?php

declare(strict_types=1);

namespace Vendor\Test\SubNamespace;

use Vendor\SubNamespace\Example;

class ExampleTest extends TestCase
{
    public function testGreet(): void
    {
        $example = new Example();

        $this->assertSame('Hello, Friends!', $example->greet('Friends'));
    }
}
