<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Symfony\Component\Console\Question\Question;

use function array_map;
use function explode;
use function implode;
use function preg_match;
use function preg_replace;
use function str_replace;
use function substr;
use function trim;
use function ucwords;

/**
 * Asks for the namespace to use for this package
 */
class PackageNamespace extends Question implements StarterKitQuestion
{
    use AnswersTool;

    private const VALID_PATTERN = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/';

    public function getName(): string
    {
        return 'packageNamespace';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct('What is the library\'s root namespace?');

        $this->answers = $answers;
    }

    public function getQuestion(): string
    {
        $question = parent::getQuestion();

        if ($this->getDefault() === null) {
            $question .= ' (e.g., Foo\\Bar\\Baz)';
        }

        return $question;
    }

    public function getDefault(): float | bool | int | string | null
    {
        if ($this->getAnswers()->packageNamespace !== null) {
            return $this->getAnswers()->packageNamespace;
        }

        $packageName = $this->getAnswers()->packageName;

        if ($packageName === null || trim($packageName) === '') {
            return null;
        }

        $packageName = str_replace('/', '\\', $packageName);
        $packageNameParts = explode('\\', $packageName);

        /** @var string[] $packageNameParts */
        $packageNameParts = array_map($this->namify(), $packageNameParts);

        return implode('\\', $packageNameParts);
    }

    public function getValidator(): callable
    {
        return function (?string $data): string {
            $packageNameParts = explode('\\', (string) $data);

            foreach ($packageNameParts as $namePart) {
                if (!preg_match(self::VALID_PATTERN, $namePart)) {
                    throw new InvalidConsoleInput(
                        'You must enter a valid library namespace.',
                    );
                }
            }

            return (string) $data;
        };
    }

    private function namify(): callable
    {
        return function (string $value): string {
            // Replace any invalid characters with a space.
            $value = preg_replace('/[^a-zA-Z0-9_\x80-\xff]/', ' ', $value);
            $valueParts = explode(' ', (string) $value);

            foreach ($valueParts as &$part) {
                // Check the first character to make sure it's valid.
                if (preg_match('/[^a-zA-Z_\x80-\xff]/', $part[0]) === 1) {
                    $part = substr($part, 1);
                }
            }

            // Upper-case the words and separate with namespaces.
            return str_replace(' ', '\\', ucwords(implode(' ', $valueParts)));
        };
    }
}
