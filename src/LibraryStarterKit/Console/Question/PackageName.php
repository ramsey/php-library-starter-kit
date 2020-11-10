<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * ramsey/php-library-starter-kit is open source software: you can
 * distribute it and/or modify it under the terms of the MIT License
 * (the "License"). You may not use this file except in
 * compliance with the License.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Exception\InvalidConsoleInput;
use Ramsey\Dev\LibraryStarterKit\Task\Answers;
use Symfony\Component\Console\Question\Question;

use function preg_match;
use function strpos;
use function strtolower;
use function trim;

/**
 * Asks for the package name (i.e., the name to use for this package on Packagist.org)
 */
class PackageName extends Question implements StarterKitQuestion
{
    use AnswersTool;

    private const VALID_PATTERN = '/^[a-z0-9]([_.-]?[a-z0-9]+)*\/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$/';

    public function getName(): string
    {
        return 'packageName';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct('What is your package name?');

        $this->answers = $answers;
    }

    /**
     * @inheritDoc
     */
    public function getDefault()
    {
        $packageName = (string) $this->getAnswers()->vendorName
            . '/'
            . (string) $this->getAnswers()->projectName;

        if (trim($packageName) === '/') {
            return null;
        }

        return $packageName;
    }

    public function getValidator(): callable
    {
        return function (?string $data): string {
            $vendorPrefix = '';
            if ($this->getAnswers()->vendorName !== null) {
                $vendorPrefix = strtolower((string) $this->getAnswers()->vendorName) . '/';
            }

            $data = strtolower((string) $data);

            if ($vendorPrefix !== '' && strpos($data, $vendorPrefix) !== 0) {
                $data = $vendorPrefix . $data;
            }

            if (preg_match(self::VALID_PATTERN, $data)) {
                return $data;
            }

            throw new InvalidConsoleInput(
                'You must enter a valid package name.',
            );
        };
    }
}
