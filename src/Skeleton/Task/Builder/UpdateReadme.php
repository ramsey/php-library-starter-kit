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

use Ramsey\Skeleton\Task\Answers;
use Ramsey\Skeleton\Task\Builder;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment as TwigEnvironment;

/**
 * Updates the README.md file based on answers obtained during project setup
 */
class UpdateReadme extends Builder
{
    public function build(): void
    {
        $this->getBuildTask()->getIO()->write('<info>Updating README.md</info>');

        $readmeContents = $this->getReadmeContents();

        $replacements = [
            '/<!-- NAME_START -->(.*)<!-- NAME_END -->/s' => $this->getAnswers()->packageName,
            '/<!-- BADGES_START -->(.*)<!-- BADGES_END -->/s' => $this->getBadges(),
            '/<!-- DESC_START -->(.*)<!-- DESC_END -->/s' => $this->getAnswers()->packageDescription,
            '/<!-- COC_START -->(.*)<!-- COC_END -->/s' => $this->getCodeOfConduct(),
            '/<!-- USAGE_START -->(.*)<!-- USAGE_END -->/s' => $this->getUsage(),
            '/<!-- FAQ_START -->(.*)<!-- FAQ_END -->/s' => '',
            '/<!-- COPYRIGHT_START -->(.*)<!-- COPYRIGHT_END -->/s' => $this->getCopyright(),
        ];

        /** @var string[] $searches */
        $searches = array_keys($replacements);

        /** @var string[] $replaces */
        $replaces = array_values($replacements);

        $readme = (string) preg_replace(
            $searches,
            $replaces,
            $readmeContents,
        );

        $this->getBuildTask()->getFilesystem()->dumpFile(
            $this->getBuildTask()->path('README.md'),
            $readme,
        );
    }

    private function getReadmeContents(): string
    {
        $finder = $this->getBuildTask()->getFinder();
        $finder
            ->in($this->getBuildTask()->getAppPath())
            ->files()
            ->depth('== 0')
            ->name('README.md');

        $readmeContents = null;

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $readmeContents = $file->getContents();

            break;
        }

        if ($readmeContents === null) {
            throw new RuntimeException('Unable to get contents of README.md');
        }

        return (string) $readmeContents;
    }

    private function getTwig(): TwigEnvironment
    {
        return $this->getBuildTask()->getTwigEnvironment();
    }

    private function getAnswers(): Answers
    {
        return $this->getBuildTask()->getAnswers();
    }

    private function getBadges(): string
    {
        return $this->getTwig()->render(
            'readme/badges.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getCodeOfConduct(): string
    {
        if ($this->getAnswers()->codeOfConduct === null) {
            return '';
        }

        return $this->getTwig()->render(
            'readme/code-of-conduct.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getUsage(): string
    {
        return $this->getTwig()->render(
            'readme/usage.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getCopyright(): string
    {
        return $this->getTwig()->render(
            'readme/copyright.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }
}
