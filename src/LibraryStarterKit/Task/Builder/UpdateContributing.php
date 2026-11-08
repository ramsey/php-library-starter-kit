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

use JsonException;
use Ramsey\Dev\LibraryStarterKit\Task\Builder;
use RuntimeException;

use function is_array;
use function is_string;
use function json_decode;
use function preg_match;

use const JSON_THROW_ON_ERROR;

/**
 * Updates the CONTRIBUTING.md file based on responses to setup questions
 */
class UpdateContributing extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Updating CONTRIBUTING.md');

        $changelog = $this->getEnvironment()->getTwigEnvironment()->render(
            'CONTRIBUTING.md.twig',
            $this->getAnswers()->getArrayCopy() + ['phpVersion' => $this->getPhpVersion()],
        );

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('CONTRIBUTING.md'),
            $changelog,
        );
    }

    /**
     * Returns the minimum PHP version, as major.minor, from composer.json
     */
    private function getPhpVersion(): string
    {
        $finder = $this->getEnvironment()->getFinder();
        $finder
            ->in($this->getEnvironment()->getAppPath())
            ->files()
            ->depth('== 0')
            ->name('composer.json');

        $composerContents = null;

        foreach ($finder as $file) {
            $composerContents = $file->getContents();

            break;
        }

        if ($composerContents === null) {
            throw $this->unknownPhpVersion();
        }

        try {
            $composer = json_decode($composerContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw $this->unknownPhpVersion();
        }

        if (!is_array($composer) || !is_array($composer['require'] ?? null)) {
            throw $this->unknownPhpVersion();
        }

        $constraint = $composer['require']['php'] ?? null;

        if (!is_string($constraint) || preg_match('/\d+\.\d+/', $constraint, $matches) !== 1) {
            throw $this->unknownPhpVersion();
        }

        return $matches[0];
    }

    private function unknownPhpVersion(): RuntimeException
    {
        return new RuntimeException('Unable to determine the minimum PHP version from composer.json');
    }
}
