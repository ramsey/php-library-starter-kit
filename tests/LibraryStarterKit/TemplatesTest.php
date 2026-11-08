<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Filesystem;
use Ramsey\Dev\LibraryStarterKit\Project;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateLicense;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Twig\Environment as TwigEnvironment;

use function dirname;

/**
 * Renders the real Twig templates, rather than mocking the Twig environment
 */
class TemplatesTest extends TestCase
{
    private TwigEnvironment $twig;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Event & MockInterface $event */
        $event = $this->mockery(Event::class, [
            'getIO' => $this->mockery(IOInterface::class),
        ]);

        /** @var Filesystem & MockInterface $filesystem */
        $filesystem = $this->mockery(Filesystem::class);

        /** @var Finder & MockInterface $finder */
        $finder = $this->mockery(Finder::class);
        $finder->shouldReceive('create')->andReturn(clone $finder);

        $setup = new Setup(
            new Project('a-project-name', dirname(__FILE__, 3)),
            $event,
            $filesystem,
            $finder,
            OutputInterface::VERBOSITY_NORMAL,
        );

        $this->twig = $setup->getTwigEnvironment();
    }

    public function testFundingWritesGitHubsExampleForm(): void
    {
        $this->answers->funding = [
            'github' => ['octocat', 'surftocat'],
            'patreon' => 'octocat',
            'tidelift' => 'npm/octo-package',
            'custom' => ['https://www.paypal.me/octocat', 'https://octocat.com'],
        ];

        $this->assertSame(
            "github: [octocat, surftocat]\npatreon: octocat\ntidelift: npm/octo-package\n"
            . "custom: [\"https://www.paypal.me/octocat\",\"https://octocat.com\"]\n",
            $this->twig->render('FUNDING.yml.twig', $this->answers->getArrayCopy()),
        );
    }

    public function testFundingWritesASingleGithubUsernameAsAList(): void
    {
        $this->answers->funding = ['github' => ['ramsey']];

        $this->assertSame(
            "github: [ramsey]\n",
            $this->twig->render('FUNDING.yml.twig', $this->answers->getArrayCopy()),
        );
    }

    #[DataProvider('licenseBadgeLinks')]
    public function testBadgesLinkTheLicenseBadgeToTheLicenseFile(string $license, string $filename): void
    {
        $this->answers->githubUsername = 'acme';
        $this->answers->projectName = 'awesome';
        $this->answers->packageName = 'acme/awesome';
        $this->answers->license = $license;

        $rendered = $this->twig->render('readme/badges.md.twig', $this->answers->getArrayCopy() + [
            'licenseFilename' => UpdateLicense::getLicenseFilename($license),
        ]);

        $this->assertStringContainsString(
            'href="https://github.com/acme/awesome/blob/main/' . $filename . '"',
            $rendered,
        );
    }

    /**
     * @return iterable<string, array{license: string, filename: string}>
     */
    public static function licenseBadgeLinks(): iterable
    {
        yield 'LGPL' => ['license' => 'LGPL-3.0-or-later', 'filename' => 'COPYING.LESSER'];
        yield 'GPL' => ['license' => 'GPL-3.0-or-later', 'filename' => 'COPYING'];
        yield 'MIT' => ['license' => 'MIT', 'filename' => 'LICENSE'];
        yield 'Unlicense' => ['license' => 'Unlicense', 'filename' => 'UNLICENSE'];
    }

    public function testContributingNamesTheMinimumPhpVersion(): void
    {
        $this->answers->githubUsername = 'acme';
        $this->answers->projectName = 'awesome';
        $this->answers->packageName = 'acme/awesome';

        $rendered = $this->twig->render(
            'CONTRIBUTING.md.twig',
            $this->answers->getArrayCopy() + ['phpVersion' => '8.5'],
        );

        $this->assertStringContainsString('you will need [PHP] 8.5 or greater and [Composer].', $rendered);
        $this->assertStringNotContainsString('8.2', $rendered);
    }
}
