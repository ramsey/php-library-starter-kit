<?php

/**
 * This file is part of the ramsey/php-library-skeleton project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Skeleton\Task\Questions;

use Composer\IO\IOInterface;

/**
 * Questions to use when prompting the user during project configuration and setup.
 */
class InstallQuestions
{
    /**
     * Returns an array of questions and additional information to pass to an
     * IO object for use when prompting the user and validating their input.
     *
     * @return array
     */
    public function getQuestions(): array
    {
        return [
            'authorName' => [
                'prompt' => PHP_EOL . 'What is your name?' . PHP_EOL . '> ',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'authorEmail' => [
                'prompt' => PHP_EOL . 'What is your email address?' . PHP_EOL . '> ',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'authorUrl' => [
                'prompt' => PHP_EOL . 'What is your website address?' . PHP_EOL . '> ',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'copyrightHolder' => [
                'prompt' => PHP_EOL . 'What is the name of the copyright holder?' . PHP_EOL . '[{{ authorName }}] > ',
                'default' => '{{ authorName }}',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'copyrightEmail' => [
                'prompt' => PHP_EOL . 'What is the copyright holder\'s email address?'
                    . PHP_EOL . '[{{ authorEmail }}] > ',
                'default' => '{{ authorEmail }}',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'copyrightUrl' => [
                'prompt' => PHP_EOL . 'What is the copyright holder\'s website address?'
                    . PHP_EOL . '[{{ authorUrl }}] > ',
                'default' => '{{ authorUrl }}',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'copyrightYear' => [
                'prompt' => PHP_EOL . 'What is the copyright year?' . PHP_EOL . '[' . date('Y') . '] > ',
                'default' => date('Y'),
            ],
            'conductEmail' => [
                'prompt' => PHP_EOL . 'What email address should people use to report code of conduct issues?'
                    . PHP_EOL . '[{{ authorEmail }}] > ',
                'default' => '{{ authorEmail }}',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'githubUsername' => [
                'prompt' => PHP_EOL . 'What is the GitHub username or organization name for the library?'
                    . PHP_EOL . '> ',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'githubProject' => [
                'prompt' => PHP_EOL . 'What is the GitHub project name for the library?' . PHP_EOL . '> ',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'packageName' => [
                'prompt' => PHP_EOL . 'What is the Packagist package name for the library?'
                    . PHP_EOL . '[{{ githubUsername }}/{{ githubProject }}] > ',
                'default' => '{{ githubUsername }}/{{ githubProject }}',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'packageDescription' => [
                'prompt' => PHP_EOL . 'Enter a brief description of your library.' . PHP_EOL . '> ',
            ],
            'keywords' => [
                'prompt' => PHP_EOL . 'Optionally provide a set of comma-separated keywords.' . PHP_EOL . '> ',
            ],
            'namespace' => [
                'prompt' => PHP_EOL . 'What is the library\'s full namespace? (i.e. Foo\\Bar\\Baz)' . PHP_EOL . '> ',
                'operation' => $this->defaultCustomPrompt(),
            ],
            'baseClass' => [
                'prompt' => PHP_EOL . 'What is the library\'s base class?' . PHP_EOL . '{{ namespace }}\\',
                'operation' => $this->defaultCustomPrompt(),
            ],
        ];
    }

    /**
     * Handles logic for replacing tokens in prompts and default values with
     * data the user entered in previous responses. If a user does not provide
     * a value, this throws an exception, which causes the Composer IO object
     * to continue prompting the user.
     *
     * @return callable
     */
    private function defaultCustomPrompt(): callable
    {
        return function (IOInterface $io, string $prompt, array $answers, $default) {
            $tokens = array_map(function ($value) {
                return "{{ {$value} }}";
            }, array_keys($answers));

            $tokenValues = array_values($answers);

            $prompt = str_replace($tokens, $tokenValues, $prompt);
            $default = $default === null ? null : str_replace($tokens, $tokenValues, $default);

            return $io->askAndValidate(
                $prompt,
                function ($data) {
                    if (!trim((string) $data)) {
                        throw new \InvalidArgumentException('You must enter a value.');
                    }
                    return $data;
                },
                null,
                $default
            );
        };
    }
}
