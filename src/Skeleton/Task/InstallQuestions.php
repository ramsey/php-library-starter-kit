<?php

/**
 * This file is part of ramsey/php-library-skeleton
 *
 * ramsey/php-library-skeleton is open source software: you can
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

namespace Ramsey\Skeleton\Task;

use Composer\IO\IOInterface;
use Ramsey\Skeleton\Task\Questions\AuthorEmail;
use Ramsey\Skeleton\Task\Questions\AuthorHoldsCopyright;
use Ramsey\Skeleton\Task\Questions\AuthorName;
use Ramsey\Skeleton\Task\Questions\AuthorUrl;
use Ramsey\Skeleton\Task\Questions\CodeOfConduct;
use Ramsey\Skeleton\Task\Questions\CodeOfConductCommittee;
use Ramsey\Skeleton\Task\Questions\CodeOfConductEmail;
use Ramsey\Skeleton\Task\Questions\CodeOfConductPoliciesUrl;
use Ramsey\Skeleton\Task\Questions\CodeOfConductReportingUrl;
use Ramsey\Skeleton\Task\Questions\CommandPrefix;
use Ramsey\Skeleton\Task\Questions\CopyrightEmail;
use Ramsey\Skeleton\Task\Questions\CopyrightHolder;
use Ramsey\Skeleton\Task\Questions\CopyrightUrl;
use Ramsey\Skeleton\Task\Questions\CopyrightYear;
use Ramsey\Skeleton\Task\Questions\GithubUsername;
use Ramsey\Skeleton\Task\Questions\License;
use Ramsey\Skeleton\Task\Questions\PackageDescription;
use Ramsey\Skeleton\Task\Questions\PackageKeywords;
use Ramsey\Skeleton\Task\Questions\PackageName;
use Ramsey\Skeleton\Task\Questions\PackageNamespace;
use Ramsey\Skeleton\Task\Questions\VendorName;

class InstallQuestions
{
    private string $projectName;

    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        $this->projectName = (string) ($config['projectName'] ?? '');
    }

    /**
     * Returns an array of questions and additional information to pass to an
     * IO object for use when prompting the user and validating their input.
     *
     * @return array<Question>
     */
    public function getQuestions(IOInterface $io, Answers $answers): array
    {
        $answers->projectName = $this->projectName;

        return [
            new GithubUsername($io, $answers),
            new VendorName($io, $answers),
            new PackageName($io, $answers),
            new PackageDescription($io, $answers),
            new AuthorName($io, $answers),
            new AuthorEmail($io, $answers),
            new AuthorUrl($io, $answers),
            new AuthorHoldsCopyright($io, $answers),
            new CopyrightHolder($io, $answers),
            new CopyrightEmail($io, $answers),
            new CopyrightUrl($io, $answers),
            new CopyrightYear($io, $answers),
            new License($io, $answers),
            new CodeOfConduct($io, $answers),
            new CodeOfConductEmail($io, $answers),
            new CodeOfConductCommittee($io, $answers),
            new CodeOfConductPoliciesUrl($io, $answers),
            new CodeOfConductReportingUrl($io, $answers),
            new PackageKeywords($io, $answers),
            new PackageNamespace($io, $answers),
            new CommandPrefix($io, $answers),
        ];
    }
}
