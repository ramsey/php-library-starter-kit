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
        switch ($license) {
            case 'AGPL-3.0-or-later':
            case 'Apache-2.0':
            case 'GPL-3.0-or-later':
            case 'LGPL-3.0-or-later':
            case 'MPL-2.0':
                return true;
            default:
                return false;
        }
    }

    private function getLicenseFilename(string $license): string
    {
        switch ($license) {
            case 'AGPL-3.0-or-later':
            case 'GPL-3.0-or-later':
                return 'COPYING';
            case 'LGPL-3.0-or-later':
                return 'COPYING.LESSER';
            case 'Proprietary':
                return 'COPYRIGHT';
            case 'Unlicense':
                return 'UNLICENSE';
            default:
                return 'LICENSE';
        }
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
