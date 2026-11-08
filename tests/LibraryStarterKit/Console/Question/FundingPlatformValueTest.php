<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatform;
use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatformValue;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Ramsey\Test\Dev\LibraryStarterKit\TestCase;

class FundingPlatformValueTest extends TestCase
{
    public function testNameAndPromptAndAnswers(): void
    {
        $question = new FundingPlatformValue($this->answers, FundingPlatform::Patreon);

        $this->assertSame('funding', $question->getName());
        $this->assertSame('What is your Patreon username?', $question->getQuestion());
        $this->assertSame($this->answers, $question->getAnswers());
        $this->assertNull($question->getDefault());
    }

    public function testDefaultIsTheExistingValue(): void
    {
        $this->answers->funding = ['github' => ['a', 'b'], 'patreon' => 'ramsey', 'tidelift' => ''];

        $this->assertSame('a,b', (new FundingPlatformValue($this->answers, FundingPlatform::Github))->getDefault());
        $this->assertSame('ramsey', (new FundingPlatformValue($this->answers, FundingPlatform::Patreon))->getDefault());
        $this->assertNull((new FundingPlatformValue($this->answers, FundingPlatform::Tidelift))->getDefault());
    }

    public function testDefaultIsNullForAnEmptyList(): void
    {
        $this->answers->funding = ['github' => []];

        $this->assertNull((new FundingPlatformValue($this->answers, FundingPlatform::Github))->getDefault());
    }

    public function testIsSkippedUnlessThePlatformWasChosen(): void
    {
        $this->answers->funding = ['github' => ''];

        $this->assertFalse((new FundingPlatformValue($this->answers, FundingPlatform::Github))->shouldSkip());
        $this->assertTrue((new FundingPlatformValue($this->answers, FundingPlatform::Patreon))->shouldSkip());
    }

    public function testIsSkippedWhenFundingIsNull(): void
    {
        $this->assertTrue((new FundingPlatformValue($this->answers, FundingPlatform::Github))->shouldSkip());
    }

    public function testNormalizerSplitsListsAndStripsALeadingAtFromGithubUsernames(): void
    {
        $github = (new FundingPlatformValue($this->answers, FundingPlatform::Github))->getNormalizer();
        $patreon = (new FundingPlatformValue($this->answers, FundingPlatform::Patreon))->getNormalizer();

        $this->assertSame(['octocat', 'surftocat'], $github(' @octocat, surftocat ,'));
        $this->assertSame('ramsey', $patreon(' ramsey '));
        $this->assertSame([], $github(null));
        $this->assertSame('', $patreon(null));
    }

    public function testNormalizerDoesNotStripAtFromOtherListEntries(): void
    {
        $custom = (new FundingPlatformValue($this->answers, FundingPlatform::Custom))->getNormalizer();

        $this->assertSame(['@a', 'https://b.example'], $custom('@a, https://b.example'));
    }

    public function testValidatorSetsTheValueAndKeepsTheOtherKeys(): void
    {
        $this->answers->funding = ['github' => '', 'patreon' => 'ramsey'];
        $validator = (new FundingPlatformValue($this->answers, FundingPlatform::Github))->getValidator();

        $this->assertSame(['github' => ['ramsey'], 'patreon' => 'ramsey'], $validator(['ramsey']));
    }

    public function testValidatorRemovesTheKeyForAnEmptyValue(): void
    {
        $this->answers->funding = ['github' => '', 'patreon' => 'ramsey'];
        $validator = (new FundingPlatformValue($this->answers, FundingPlatform::Github))->getValidator();

        $this->assertSame(['patreon' => 'ramsey'], $validator([]));
    }

    public function testValidatorRemovesTheKeyForAnEmptyString(): void
    {
        $this->answers->funding = ['patreon' => '', 'github' => ['a']];
        $validator = (new FundingPlatformValue($this->answers, FundingPlatform::Patreon))->getValidator();

        $this->assertSame(['github' => ['a']], $validator(''));
    }

    public function testValidatorAcceptsFourEntries(): void
    {
        $this->answers->funding = ['github' => ''];
        $validator = (new FundingPlatformValue($this->answers, FundingPlatform::Github))->getValidator();

        $this->assertSame(['github' => ['a', 'b', 'c', 'd']], $validator(['a', 'b', 'c', 'd']));
    }

    public function testValidatorDoesNotChangeTheAnswersItself(): void
    {
        $this->answers->funding = ['github' => ''];
        $validator = (new FundingPlatformValue($this->answers, FundingPlatform::Github))->getValidator();

        $validator(['ramsey']);

        $this->assertSame(['github' => ''], $this->answers->funding);
    }

    public function testValidatorTreatsAStringGivenToAListPlatformAsOneEntry(): void
    {
        $this->answers->funding = ['github' => '', 'custom' => ''];

        $github = (new FundingPlatformValue($this->answers, FundingPlatform::Github))->getValidator();
        $custom = (new FundingPlatformValue($this->answers, FundingPlatform::Custom))->getValidator();

        $this->assertSame(['github' => ['ramsey'], 'custom' => ''], $github('ramsey'));
        $this->assertSame(['github' => '', 'custom' => ['https://a.example']], $custom('https://a.example'));
    }

    public function testValidatorAcceptsAListOfOneGivenToASingleValuePlatform(): void
    {
        $this->answers->funding = ['patreon' => ''];
        $validator = (new FundingPlatformValue($this->answers, FundingPlatform::Patreon))->getValidator();

        $this->assertSame(['patreon' => 'ramsey'], $validator(['ramsey']));
        $this->assertSame([], $validator([]));
    }

    public function testValidatorRefusesAListOfManyGivenToASingleValuePlatform(): void
    {
        $this->answers->funding = ['patreon' => ''];
        $validator = (new FundingPlatformValue($this->answers, FundingPlatform::Patreon))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessageIsOrContains('Enter only one Patreon entry');

        $validator(['a', 'b']);
    }

    /**
     * @param string | list<string> $value
     */
    #[DataProvider('refusedValues')]
    public function testValidatorRefusesAValueThatBreaksThePlatformsRule(
        FundingPlatform $platform,
        string | array $value,
        string $message,
    ): void {
        $this->answers->funding = [$platform->value => ''];
        $validator = (new FundingPlatformValue($this->answers, $platform))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessageIsOrContains($message);

        $validator($value);
    }

    /**
     * @return iterable<string, array{platform: FundingPlatform, value: string | list<string>, message: string}>
     */
    public static function refusedValues(): iterable
    {
        yield 'five github usernames' => [
            'platform' => FundingPlatform::Github,
            'value' => ['a', 'b', 'c', 'd', 'e'],
            'message' => 'Enter no more than four GitHub Sponsors entries',
        ];

        yield 'five custom urls' => [
            'platform' => FundingPlatform::Custom,
            'value' => [
                'https://a.example',
                'https://b.example',
                'https://c.example',
                'https://d.example',
                'https://e.example',
            ],
            'message' => 'Enter no more than four Custom URL entries',
        ];

        yield 'custom not a url' => [
            'platform' => FundingPlatform::Custom,
            'value' => ['octocat.com'],
            'message' => '"octocat.com" is not a URL beginning with "http://" or "https://"',
        ];

        yield 'custom a url with another scheme' => [
            'platform' => FundingPlatform::Custom,
            'value' => ['ftp://octocat.com'],
            'message' => '"ftp://octocat.com" is not a URL beginning with "http://" or "https://"',
        ];

        yield 'custom a url starting with http but no separator' => [
            'platform' => FundingPlatform::Custom,
            'value' => ['httpfoo://octocat.com'],
            'message' => '"httpfoo://octocat.com" is not a URL beginning with "http://" or "https://"',
        ];

        yield 'tidelift without a platform' => [
            'platform' => FundingPlatform::Tidelift,
            'value' => 'octo-package',
            'message' => 'A Tidelift value is PLATFORM-NAME/PACKAGE-NAME, where the platform is npm, pypi, '
                . 'rubygems, maven, packagist, or nuget; "octo-package" given',
        ];

        yield 'thanks.dev without the prefix' => [
            'platform' => FundingPlatform::ThanksDev,
            'value' => 'octocat',
            'message' => 'A thanks.dev value is u/gh/USERNAME; "octocat" given',
        ];
    }

    public function testValidatorAcceptsAValueThatFollowsThePlatformsRule(): void
    {
        $this->answers->funding = ['tidelift' => '', 'thanks_dev' => '', 'custom' => ''];

        $tidelift = new FundingPlatformValue($this->answers, FundingPlatform::Tidelift);
        $thanksDev = new FundingPlatformValue($this->answers, FundingPlatform::ThanksDev);
        $custom = new FundingPlatformValue($this->answers, FundingPlatform::Custom);

        $this->assertSame('packagist/acme/awesome', $tidelift->getValidator()('packagist/acme/awesome')['tidelift']);
        $this->assertSame('u/gh/octocat', $thanksDev->getValidator()('u/gh/octocat')['thanks_dev']);
        $this->assertSame(
            ['https://www.paypal.me/octocat'],
            $custom->getValidator()(['https://www.paypal.me/octocat'])['custom'],
        );
    }
}
