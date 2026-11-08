<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatform;
use Ramsey\Dev\LibraryStarterKit\Console\Question\FundingPlatformValue;
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
        return 'Which funding platforms do you want to include? Press Enter for none';
    }

    public function testQuestionLeavesOutTheHintWhenPlatformsAreSaved(): void
    {
        $this->answers->funding = ['github' => ['ramsey']];

        $this->assertSame(
            'Which funding platforms do you want to include?',
            (new FundingPlatforms($this->answers))->getQuestion(),
        );
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

    public function testValidatorReturnsNullForNoPlatforms(): void
    {
        $this->answers->funding = [];
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->assertNull($validator([]));
        $this->assertNull($validator(' , '));
    }

    public function testPressingEnterWithNothingSavedDropsTheFundingFile(): void
    {
        $this->answers->funding = [];

        [$answer, $display] = $this->ask(new FundingPlatforms($this->answers), "\n");

        $this->assertNull($answer);
        $this->assertStringNotContainsString('ERROR', $display);

        $this->answers->funding = $answer;
        $this->assertTrue((new FundingPlatformValue($this->answers, FundingPlatform::Github))->shouldSkip());
    }

    public function testPressingEnterKeepsSavedPlatforms(): void
    {
        $this->answers->funding = ['github' => ['ramsey'], 'tidelift' => 'packagist/ramsey/uuid'];

        $this->assertSame(
            ['github' => ['ramsey'], 'tidelift' => 'packagist/ramsey/uuid'],
            $this->ask(new FundingPlatforms($this->answers), "\n")[0],
        );
    }

    public function testValidatorWorksWhenFundingIsNull(): void
    {
        $validator = (new FundingPlatforms($this->answers))->getValidator();

        $this->assertSame(['github' => '', 'custom' => ''], $validator(['custom', 'github', 'custom']));
    }

    public function testAskingListsThePlatformsWithinEightyColumns(): void
    {
        $this->answers->funding = [];

        [$answer, $display] = $this->ask(new FundingPlatforms($this->answers), "2, Tidelift\n");

        $this->assertSame(['github' => '', 'tidelift' => ''], $answer);
        $this->assertStringContainsString('[2 ] GitHub Sponsors', $display);
        $this->assertStringContainsString('[12] Custom URL', $display);

        foreach (explode("\n", $display) as $line) {
            $this->assertLessThanOrEqual(80, mb_strlen($line));
        }
    }

    /**
     * Asks the question through Symfony, with the typed text as input
     *
     * @return array{mixed, string} the answer and what the console printed
     */
    private function ask(FundingPlatforms $question, string $typed): array
    {
        $stream = fopen('php://memory', 'r+');
        $this->assertNotFalse($stream);
        fwrite($stream, $typed);
        rewind($stream);

        $input = new ArrayInput([]);
        $input->setStream($stream);
        $output = new BufferedOutput();

        // Autocompletion reads the terminal one key at a time, which a test
        // stream cannot do.
        $question->setAutocompleterValues(null);

        $answer = (new SymfonyStyle($input, $output))->askQuestion($question);

        return [$answer, $output->fetch()];
    }
}
