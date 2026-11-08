<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatforms;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;

class FundingPlatformsTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return FundingPlatforms::class;
    }

    protected function getQuestionName(): string
    {
        return 'funding';
    }

    protected function getQuestionText(): string
    {
        return 'Which funding platforms do you want to include? Enter comma-separated platform keys '
            . '(community_bridge, github, issuehunt, ko_fi, liberapay, open_collective, patreon, tidelift, '
            . 'polar, buy_me_a_coffee, thanks_dev, custom)';
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->funding = ['github' => ['ramsey'], 'patreon' => 'ramsey'];

        $this->assertSame('github,patreon', (new FundingPlatforms($this->answers))->getDefault());
    }

    public function testIsSkippedWhenFundingIsNull(): void
    {
        $this->assertTrue((new FundingPlatforms($this->answers))->shouldSkip());

        $this->answers->funding = [];
        $this->assertFalse((new FundingPlatforms($this->answers))->shouldSkip());
    }

    public function testNormalizerSplitsAndTrims(): void
    {
        $normalizer = (new FundingPlatforms($this->answers))->getNormalizer();

        $this->assertSame(['github', 'custom'], $normalizer(' github ,, custom,'));
        $this->assertSame([], $normalizer(null));
    }

    public function testValidatorOrdersChosenKeysAndKeepsExistingValues(): void
    {
        $this->answers->funding = ['patreon' => 'ramsey', 'github' => ['ramsey']];
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->assertSame(
            ['github' => ['ramsey'], 'tidelift' => ''],
            $validator(['tidelift', 'github']),
        );
    }

    public function testValidatorRefusesAnUnknownKey(): void
    {
        $this->answers->funding = [];
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessageIsOrContains(
            '"paypal" is not a funding platform; choose from community_bridge, github, issuehunt, ko_fi, '
            . 'liberapay, open_collective, patreon, tidelift, polar, buy_me_a_coffee, thanks_dev, and custom',
        );

        $validator(['github', 'paypal']);
    }

    public function testValidatorWorksWhenFundingIsNull(): void
    {
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->assertSame(['github' => '', 'custom' => ''], $validator(['custom', 'github', 'custom']));
        $this->assertSame([], $validator([]));
    }
}
