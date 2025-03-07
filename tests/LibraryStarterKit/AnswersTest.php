<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Mockery\MockInterface;
use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Filesystem;

use function json_encode;

class AnswersTest extends TestCase
{
    private Filesystem & MockInterface $filesystem;

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
                'securityPolicy',
                'securityPolicyContactEmail',
                'securityPolicyContactFormUrl',
                'skipPrompts',
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
                true,
                null,
                null,
                false,
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
                'securityPolicy' => true,
                'securityPolicyContactEmail' => null,
                'securityPolicyContactFormUrl' => null,
                'skipPrompts' => false,
                'vendorName' => null,
            ],
            $answers->getArrayCopy(),
        );
    }

    public function testLoadsExistingFileUponInstantiation(): void
    {
        $filesystem = new Filesystem();
        $answers = new Answers(__DIR__ . '/answers-test.json', $filesystem);

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
                'securityPolicy' => true,
                'securityPolicyContactEmail' => 'security@example.com',
                'securityPolicyContactFormUrl' => 'https://example.com/security',
                'skipPrompts' => true,
                'vendorName' => 'fellowship',
            ],
            $answers->getArrayCopy(),
        );
    }

    public function testSaveToFile(): void
    {
        $this->filesystem->expects()->exists('/path/to/file.json')->andReturnFalse();

        $this->filesystem->shouldReceive('dumpFile')->withArgs(
            function (string $filename, string $content) {
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
                        'securityPolicy' => true,
                        'securityPolicyContactEmail' => 'security@example.com',
                        'securityPolicyContactFormUrl' => 'https://example.com/security',
                        'skipPrompts' => false,
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
        $answers->securityPolicy = true;
        $answers->securityPolicyContactEmail = 'security@example.com';
        $answers->securityPolicyContactFormUrl = 'https://example.com/security';
        $answers->vendorName = 'fellowship';

        $answers->saveToFile();
    }
}
