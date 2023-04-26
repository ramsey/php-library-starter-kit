<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use ReflectionClass;
use Spatie\Snapshots\MatchesSnapshots;

use function preg_replace;

trait SnapshotsTool
{
    use MatchesSnapshots;

    protected function getSnapshotId(): string
    {
        $snapshotId = (new ReflectionClass($this))->getShortName()
            . '__'
            . $this->nameWithDataSet()
            . '__'
            . $this->snapshotIncrementor;

        return (string) preg_replace('/[^0-9a-z]/i', '_', $snapshotId);
    }
}
