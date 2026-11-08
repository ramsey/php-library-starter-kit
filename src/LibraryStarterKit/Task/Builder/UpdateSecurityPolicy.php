<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * Copyright © Ben Ramsey <ben@ramsey.dev>
 *
 * Licensed under the MIT License (the "License"); you may not use this file
 * except in compliance with the License. You may obtain a copy of the License
 * at <https://opensource.org/license/MIT>.
 *
 * SPDX-License-Identifier: MIT
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
