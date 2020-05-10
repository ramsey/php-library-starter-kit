<?php

declare(strict_types=1);

namespace Ramsey\Test\Skeleton\Task;

use Ramsey\Skeleton\Task\Answers;
use Ramsey\Test\Skeleton\SkeletonTestCase;

class AnswersTest extends SkeletonTestCase
{
    public function testGetKeys(): void
    {
        $answers = new Answers();

        $this->assertSame(
            [
                'authorEmail',
                'authorHoldsCopyright',
                'authorName',
                'authorUrl',
                'codeOfConduct',
                'codeOfConductCommittee',
                'codeOfConductEmail',
                'codeOfConductPoliciesUrl',
                'codeOfConductReportingUrl',
                'commandPrefix',
                'copyrightEmail',
                'copyrightHolder',
                'copyrightUrl',
                'copyrightYear',
                'githubUsername',
                'license',
                'packageDescription',
                'packageKeywords',
                'packageName',
                'packageNamespace',
                'projectName',
                'vendorName',
            ],
            $answers->getTokens()
        );
    }

    public function testGetValues(): void
    {
        $answers = new Answers();

        $this->assertSame(
            [
                null,
                false,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                [],
                null,
                null,
                null,
                null,
            ],
            $answers->getValues()
        );
    }

    public function testGetArrayCopy(): void
    {
        $answers = new Answers();

        $this->assertSame(
            [
                'authorEmail' => null,
                'authorHoldsCopyright' => false,
                'authorName' => null,
                'authorUrl' => null,
                'codeOfConduct' => null,
                'codeOfConductCommittee' => null,
                'codeOfConductEmail' => null,
                'codeOfConductPoliciesUrl' => null,
                'codeOfConductReportingUrl' => null,
                'commandPrefix' => null,
                'copyrightEmail' => null,
                'copyrightHolder' => null,
                'copyrightUrl' => null,
                'copyrightYear' => null,
                'githubUsername' => null,
                'license' => null,
                'packageDescription' => null,
                'packageKeywords' => [],
                'packageName' => null,
                'packageNamespace' => null,
                'projectName' => null,
                'vendorName' => null,
            ],
            $answers->getArrayCopy()
        );
    }
}
