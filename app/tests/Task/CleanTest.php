<?php

declare(strict_types=1);

namespace Ramsey\Skeleton\Test\Task;

use Ramsey\Skeleton\Task\Clean;
use Ramsey\Skeleton\Test\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class CleanTest extends TestCase
{
    public function testRun()
    {
        $pathsToDelete = [
            '/app$/',
            '/composer\.lock$/',
            '/skeleton$/',
        ];

        $filesystem = \Mockery::mock(Filesystem::class);
        $filesystem->expects()->remove(\Mockery::on(function (array $value) use ($pathsToDelete) {
            foreach ($pathsToDelete as $key => $expectedExpression) {
                if (!preg_match($expectedExpression, $value[$key])) {
                    return false;
                }
            }
            return true;
        }));

        $task = \Mockery::mock(Clean::class, [
            'getFilesystem' => $filesystem,
        ]);

        $task->shouldReceive('run')->passthru();

        $task->run();
    }
}
