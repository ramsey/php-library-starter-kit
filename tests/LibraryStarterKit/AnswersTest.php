<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

use function json_encode;

class AnswersTest extends TestCase
{
    /** @var Filesystem|MockInterface */
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = $this->mockery(Filesystem::class);
    }

    public function testGetTokens(): void
    {
        $this->filesystem->expects()->exists('/path/to/file.json')->andReturnFalse();

        $answers = new Answers('/path/to/file.json', $this->filesystem);

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
            $answers->getTokens(),
        );
    }

    public function testGetValues(): void
    {
        $this->filesystem->expects()->exists('/path/to/file.json')->andReturnFalse();

        $answers = new Answers('/path/to/file.json', $this->filesystem);

        $this->assertSame(
            [
                null,
                true,
                null,
                null,
                'None',
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                'Proprietary',
                null,
                [],
                null,
                null,
                null,
                null,
            ],
            $answers->getValues(),
        );
    }

    public function testGetArrayCopy(): void
    {
        $this->filesystem->expects()->exists('/path/to/file.json')->andReturnFalse();

        $answers = new Answers('/path/to/file.json', $this->filesystem);

        $this->assertSame(
            [
                'authorEmail' => null,
                'authorHoldsCopyright' => true,
                'authorName' => null,
                'authorUrl' => null,
                'codeOfConduct' => 'None',
                'codeOfConductCommittee' => null,
                'codeOfConductEmail' => null,
                'codeOfConductPoliciesUrl' => null,
                'codeOfConductReportingUrl' => null,
                'copyrightEmail' => null,
                'copyrightHolder' => null,
                'copyrightUrl' => null,
                'copyrightYear' => null,
                'githubUsername' => null,
                'license' => 'Proprietary',
                'packageDescription' => null,
                'packageKeywords' => [],
                'packageName' => null,
                'packageNamespace' => null,
                'projectName' => null,
                'vendorName' => null,
            ],
            $answers->getArrayCopy(),
        );
    }

    public function testLoadsExistingFileUponInstantiation(): void
    {
        $file = $this->mockery(SplFileInfo::class);
        $file->expects()->getContents()->andReturn(json_encode([
            'authorHoldsCopyright' => true,
            'authorEmail' => 'frodo@example.com',
            'unknownProperty' => 'foobar',
            'authorUrl' => 'https://example.com/the-fellowship/frodo',
            'packageDescription' => 'A package to help you on your journey.',
            'codeOfConduct' => 'Citizen-2.3',
            'codeOfConductEmail' => 'council@example.com',
            'projectName' => 'The Fellowship of the Ring',
            'codeOfConductPoliciesUrl' => 'https://example.com/the-fellowship/conduct-policies',
            'codeOfConductReportingUrl' => 'https://example.com/the-fellowship/conduct-reporting',
            'authorName' => 'Frodo Baggins',
            'copyrightEmail' => 'fellowship@example.com',
            'copyrightHolder' => 'The Fellowship',
            'copyrightUrl' => 'https://example.com/the-fellowship',
            'packageName' => 'fellowship/ring',
            'anotherUnknownProperty' => 'baz',
            'copyrightYear' => '2021',
            'codeOfConductCommittee' => 'Council of the Wise',
            'githubUsername' => 'frodo',
            'vendorName' => 'fellowship',
            'license' => 'BSD-2-Clause',
            'packageKeywords' => ['foo', 'bar'],
            'packageNamespace' => 'Fellowship\\Ring',
        ]));

        $this->filesystem->expects()->exists('/path/to/file.json')->andReturnTrue();
        $this->filesystem->expects()->getFile('/path/to/file.json')->andReturn($file);

        $answers = new Answers('/path/to/file.json', $this->filesystem);

        $this->assertSame(
            [
                'authorEmail' => 'frodo@example.com',
                'authorHoldsCopyright' => true,
                'authorName' => 'Frodo Baggins',
                'authorUrl' => 'https://example.com/the-fellowship/frodo',
                'codeOfConduct' => 'Citizen-2.3',
                'codeOfConductCommittee' => 'Council of the Wise',
                'codeOfConductEmail' => 'council@example.com',
                'codeOfConductPoliciesUrl' => 'https://example.com/the-fellowship/conduct-policies',
                'codeOfConductReportingUrl' => 'https://example.com/the-fellowship/conduct-reporting',
                'copyrightEmail' => 'fellowship@example.com',
                'copyrightHolder' => 'The Fellowship',
                'copyrightUrl' => 'https://example.com/the-fellowship',
                'copyrightYear' => '2021',
                'githubUsername' => 'frodo',
                'license' => 'BSD-2-Clause',
                'packageDescription' => 'A package to help you on your journey.',
                'packageKeywords' => ['foo', 'bar'],
                'packageName' => 'fellowship/ring',
                'packageNamespace' => 'Fellowship\\Ring',
                'projectName' => 'The Fellowship of the Ring',
                'vendorName' => 'fellowship',
            ],
            $answers->getArrayCopy(),
        );
    }

    public function testSaveToFile(): void
    {
        $this->filesystem->expects()->exists('/path/to/file.json')->andReturnFalse();

        $this->filesystem->shouldReceive('dumpFile')->withArgs(
            function ($filename, $content) {
                $this->assertSame('/path/to/file.json', $filename);
                $this->assertJsonStringEqualsJsonString(
                    (string) json_encode([
                        'authorEmail' => 'frodo@example.com',
                        'authorHoldsCopyright' => true,
                        'authorName' => 'Frodo Baggins',
                        'authorUrl' => 'https://example.com/the-fellowship/frodo',
                        'codeOfConduct' => 'Citizen-2.3',
                        'codeOfConductCommittee' => 'Council of the Wise',
                        'codeOfConductEmail' => 'council@example.com',
                        'codeOfConductPoliciesUrl' => 'https://example.com/the-fellowship/conduct-policies',
                        'codeOfConductReportingUrl' => 'https://example.com/the-fellowship/conduct-reporting',
                        'copyrightEmail' => 'fellowship@example.com',
                        'copyrightHolder' => 'The Fellowship',
                        'copyrightUrl' => 'https://example.com/the-fellowship',
                        'copyrightYear' => '2021',
                        'githubUsername' => 'frodo',
                        'license' => 'BSD-2-Clause',
                        'packageDescription' => 'A package to help you on your journey.',
                        'packageKeywords' => ['foo', 'bar'],
                        'packageName' => 'fellowship/ring',
                        'packageNamespace' => 'Fellowship\\Ring',
                        'projectName' => 'The Fellowship of the Ring',
                        'vendorName' => 'fellowship',
                    ]),
                    $content,
                );

                return true;
            },
        );

        $answers = new Answers('/path/to/file.json', $this->filesystem);
        $answers->authorEmail = 'frodo@example.com';
        $answers->authorHoldsCopyright = true;
        $answers->authorName = 'Frodo Baggins';
        $answers->authorUrl = 'https://example.com/the-fellowship/frodo';
        $answers->codeOfConduct = 'Citizen-2.3';
        $answers->codeOfConductCommittee = 'Council of the Wise';
        $answers->codeOfConductEmail = 'council@example.com';
        $answers->codeOfConductPoliciesUrl = 'https://example.com/the-fellowship/conduct-policies';
        $answers->codeOfConductReportingUrl = 'https://example.com/the-fellowship/conduct-reporting';
        $answers->copyrightEmail = 'fellowship@example.com';
        $answers->copyrightHolder = 'The Fellowship';
        $answers->copyrightUrl = 'https://example.com/the-fellowship';
        $answers->copyrightYear = '2021';
        $answers->githubUsername = 'frodo';
        $answers->license = 'BSD-2-Clause';
        $answers->packageDescription = 'A package to help you on your journey.';
        $answers->packageKeywords = ['foo', 'bar'];
        $answers->packageName = 'fellowship/ring';
        $answers->packageNamespace = 'Fellowship\\Ring';
        $answers->projectName = 'The Fellowship of the Ring';
        $answers->vendorName = 'fellowship';

        $answers->saveToFile();
    }
}
