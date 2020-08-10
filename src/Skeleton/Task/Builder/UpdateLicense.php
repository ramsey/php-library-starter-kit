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

namespace Ramsey\Skeleton\Task\Builder;

use Ramsey\Skeleton\Task\Builder;

use const DIRECTORY_SEPARATOR;

/**
 * Updates the license files with those chosen during the project setup
 */
class UpdateLicense extends Builder
{
    public function build(): void
    {
        $this->getBuildTask()->getIO()->write(
            '<info>Updating license and copyright information</info>',
        );

        $licenseChoice = $this->getBuildTask()->getAnswers()->license ?? '';

        // Remove the existing LICENSE file for the skeleton project.
        $this->getBuildTask()->getFilesystem()->remove('LICENSE');

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
        $licenseContents = $this->getBuildTask()->getTwigEnvironment()->render(
            'license' . DIRECTORY_SEPARATOR . $license . '.twig',
            $this->getBuildTask()->getAnswers()->getArrayCopy(),
        );

        $this->getBuildTask()->getFilesystem()->dumpFile(
            $this->getBuildTask()->path($this->getLicenseFilename($license)),
            $licenseContents,
        );

        if (strpos($license, 'LGPL-3.0') === 0) {
            $this->includeGplWithLesserGpl();
        }
    }

    private function handleNoticeFile(string $license): void
    {
        $noticeContents = $this->getBuildTask()->getTwigEnvironment()->render(
            'license' . DIRECTORY_SEPARATOR . $license . '-NOTICE.twig',
            $this->getBuildTask()->getAnswers()->getArrayCopy(),
        );

        $this->getBuildTask()->getFilesystem()->dumpFile(
            $this->getBuildTask()->path('NOTICE'),
            $noticeContents,
        );
    }

    private function includeGplWithLesserGpl(): void
    {
        $gplContents = $this->getBuildTask()->getTwigEnvironment()->render(
            'license' . DIRECTORY_SEPARATOR . 'GPL-3.0-or-later.twig',
            $this->getBuildTask()->getAnswers()->getArrayCopy(),
        );

        $this->getBuildTask()->getFilesystem()->dumpFile(
            $this->getBuildTask()->path('COPYING'),
            $gplContents,
        );
    }
}
