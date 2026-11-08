<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatforms;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

use function explode;
use function fopen;
use function fwrite;
use function mb_strlen;
use function rewind;

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
        return 'Which funding platforms do you want to include?';
    }

    public function testGetDefaultWhenAnswerAlreadySet(): void
    {
        $this->answers->funding = ['github' => ['ramsey'], 'patreon' => 'ramsey'];

        $this->assertSame('2,7', (new FundingPlatforms($this->answers))->getDefault());
    }

    public function testChoicesAreNumberedPlatformLabels(): void
    {
        $question = new FundingPlatforms($this->answers);

        $this->assertTrue($question->isMultiselect());
        $this->assertSame(
            [
                1 => 'LFX Mentorship',
                2 => 'GitHub Sponsors',
                3 => 'IssueHunt',
                4 => 'Ko-fi',
                5 => 'Liberapay',
                6 => 'Open Collective',
                7 => 'Patreon',
                8 => 'Tidelift',
                9 => 'Polar',
                10 => 'Buy Me a Coffee',
                11 => 'thanks.dev',
                12 => 'Custom URL',
            ],
            $question->getChoices(),
        );
    }

    public function testIsSkippedWhenFundingIsNull(): void
    {
        $this->assertTrue((new FundingPlatforms($this->answers))->shouldSkip());

        $this->answers->funding = [];
        $this->assertFalse((new FundingPlatforms($this->answers))->shouldSkip());
    }

    public function testNormalizerTurnsNumbersLabelsAndKeysIntoPlatformKeys(): void
    {
        $normalizer = (new FundingPlatforms($this->answers))->getNormalizer();

        $this->assertSame(
            ['github', 'tidelift', 'ko_fi', 'custom', 'community_bridge'],
            $normalizer(' 2 ,, Tidelift, ko-FI,CUSTOM ,1,'),
        );
        $this->assertSame(['paypal', '0', '13'], $normalizer('paypal,0,13'));
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

    public function testValidatorNormalizesAString(): void
    {
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->assertSame(['github' => '', 'patreon' => ''], $validator('7,2'));
    }

    public function testValidatorRefusesAnUnknownEntry(): void
    {
        $this->answers->funding = [];
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessageIsOrContains(
            '"paypal" is not a funding platform; enter numbers or names from the list',
        );

        $validator(['github', 'paypal']);
    }

    public function testValidatorRefusesNoPlatforms(): void
    {
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->expectException(InvalidConsoleInput::class);
        $this->expectExceptionMessageIsOrContains('Choose at least one funding platform');

        $validator([]);
    }

    public function testValidatorWorksWhenFundingIsNull(): void
    {
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->assertSame(['github' => '', 'custom' => ''], $validator(['custom', 'github', 'custom']));
    }

    public function testAskingListsThePlatformsWithinEightyColumns(): void
    {
        $this->answers->funding = [];

        $stream = fopen('php://memory', 'r+');
        $this->assertNotFalse($stream);
        fwrite($stream, "2, Tidelift\n");
        rewind($stream);

        $input = new ArrayInput([]);
        $input->setStream($stream);
        $output = new BufferedOutput();

        $question = new FundingPlatforms($this->answers);

        // Autocompletion reads the terminal one key at a time, which a test
        // stream cannot do.
        $question->setAutocompleterValues(null);

        $answer = (new SymfonyStyle($input, $output))->askQuestion($question);
        $display = $output->fetch();

        $this->assertSame(['github' => '', 'tidelift' => ''], $answer);
        $this->assertStringContainsString('[2 ] GitHub Sponsors', $display);
        $this->assertStringContainsString('[12] Custom URL', $display);

        foreach (explode("\n", $display) as $line) {
            $this->assertLessThanOrEqual(80, mb_strlen($line));
        }
    }
}
