<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Ramsey\Dev\LibraryStarterKit\Project;

class ProjectTest extends TestCase
{
    public function testProject(): void
    {
        $project = new Project('project-name', '/path/to/project');

        $this->assertSame('project-name', $project->getName());
        $this->assertSame('/path/to/project', $project->getPath());
    }
}
