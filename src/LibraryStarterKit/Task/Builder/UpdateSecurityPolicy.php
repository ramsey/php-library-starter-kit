<?php

/**
 * This file is part of ramsey/php-library-starter-kit
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
