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

namespace Ramsey\Dev\LibraryStarterKit\Task\Builder;

use Ramsey\Dev\LibraryStarterKit\Task\Builder;

use const DIRECTORY_SEPARATOR;

/**
 * Updates the project's security policy using the one selected during setup
 */
class UpdateSecurityPolicy extends Builder
{
    public function build(): void
    {
        if ($this->getAnswers()->securityPolicy === false) {
            $this->getConsole()->section('Removing SECURITY.md');
            $this->getEnvironment()->getFilesystem()->remove(
                $this->getEnvironment()->path('SECURITY.md'),
            );

            return;
        }

        $this->getConsole()->section('Updating SECURITY.md');

        $securityPolicyTemplate = 'security-policy' . DIRECTORY_SEPARATOR;
        $securityPolicyTemplate .= 'HackerOne.md.twig';

        $securityPolicy = $this->getEnvironment()->getTwigEnvironment()->render(
            $securityPolicyTemplate,
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('SECURITY.md'),
            $securityPolicy,
        );
    }
}
