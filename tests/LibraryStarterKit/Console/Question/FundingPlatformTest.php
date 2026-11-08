<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatform;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

use function array_filter;
use function array_values;

class FundingPlatformTest extends TestCase
{
    public function testKeysAreInGitHubsOrder(): void
    {
        $this->assertSame(
            [
                'community_bridge',
                'github',
                'issuehunt',
                'ko_fi',
                'liberapay',
                'open_collective',
                'patreon',
                'tidelift',
                'polar',
                'buy_me_a_coffee',
                'thanks_dev',
                'custom',
            ],
            FundingPlatform::keys(),
        );
    }

    public function testOnlyGithubAndCustomAcceptLists(): void
    {
        $listPlatforms = array_values(array_filter(
            FundingPlatform::cases(),
            fn (FundingPlatform $p): bool => $p->acceptsList(),
        ));

        $this->assertSame([FundingPlatform::Github, FundingPlatform::Custom], $listPlatforms);
    }

    public function testEveryPlatformHasALabelAndAPrompt(): void
    {
        foreach (FundingPlatform::cases() as $platform) {
            $this->assertNotSame('', $platform->label());
            $this->assertNotSame('', $platform->prompt());
        }
    }

    public function testPromptsForListAndPatternedPlatformsNameWhatToEnter(): void
    {
        $this->assertSame(
            'Which GitHub Sponsors usernames should the button link to? Enter up to four, comma-separated',
            FundingPlatform::Github->prompt(),
        );
        $this->assertSame(
            'What is your Tidelift package, as PLATFORM-NAME/PACKAGE-NAME (for example, packagist/acme/awesome)?',
            FundingPlatform::Tidelift->prompt(),
        );
        $this->assertSame(
            'Which custom funding URLs should the button link to? Enter up to four, comma-separated',
            FundingPlatform::Custom->prompt(),
        );
    }
}
