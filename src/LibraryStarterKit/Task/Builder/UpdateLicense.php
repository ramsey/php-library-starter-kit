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

use function str_starts_with;

use const DIRECTORY_SEPARATOR;

/**
 * Updates the license files with those chosen during the project setup
 */
class UpdateLicense extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Updating license and copyright information');

        $licenseChoice = $this->getAnswers()->license ?? '';

        // Remove the existing LICENSE file.
        $this->getEnvironment()->getFilesystem()->remove('LICENSE');

        $this->handleLicenseFile($licenseChoice);

        if ($this->hasNoticeFile($licenseChoice)) {
            $this->handleNoticeFile($licenseChoice);
        }
    }

    private function hasNoticeFile(string $license): bool
    {
        return match ($license) {
            'AGPL-3.0-or-later', 'Apache-2.0', 'GPL-3.0-or-later', 'LGPL-3.0-or-later', 'MPL-2.0' => true,
            default => false,
        };
    }

    private function getLicenseFilename(string $license): string
    {
        return match ($license) {
            'AGPL-3.0-or-later', 'GPL-3.0-or-later' => 'COPYING',
            'LGPL-3.0-or-later' => 'COPYING.LESSER',
            'Proprietary' => 'COPYRIGHT',
            'Unlicense' => 'UNLICENSE',
            default => 'LICENSE',
        };
    }

    private function handleLicenseFile(string $license): void
    {
        $licenseContents = $this->getEnvironment()->getTwigEnvironment()->render(
            'license' . DIRECTORY_SEPARATOR . $license . '.twig',
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path($this->getLicenseFilename($license)),
            $licenseContents,
        );

        if (str_starts_with($license, 'LGPL-3.0')) {
            $this->includeGplWithLesserGpl();
        }
    }

    private function handleNoticeFile(string $license): void
    {
        $noticeContents = $this->getEnvironment()->getTwigEnvironment()->render(
            'license' . DIRECTORY_SEPARATOR . $license . '-NOTICE.twig',
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('NOTICE'),
            $noticeContents,
        );
    }

    private function includeGplWithLesserGpl(): void
    {
        $gplContents = $this->getEnvironment()->getTwigEnvironment()->render(
            'license' . DIRECTORY_SEPARATOR . 'GPL-3.0-or-later.twig',
            $this->getAnswers()->getArrayCopy(),
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('COPYING'),
            $gplContents,
        );
    }
}
