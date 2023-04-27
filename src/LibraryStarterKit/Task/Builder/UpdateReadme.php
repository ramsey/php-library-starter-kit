<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Task\Builder;

use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConduct;
use Ramsey\Dev\LibraryStarterKit\Task\Builder;
use RuntimeException;

use function array_keys;
use function array_values;
use function preg_replace;

/**
 * Updates the README.md file based on answers obtained during project setup
 */
class UpdateReadme extends Builder
{
    public function build(): void
    {
        $this->getConsole()->section('Updating README.md');

        $readmeContents = $this->getReadmeContents();

        $replacements = [
            '/<!-- NAME_START -->(.*)<!-- NAME_END -->/s' => $this->getAnswers()->packageName,
            '/<!-- BADGES_START -->(.*)<!-- BADGES_END -->/s' => $this->getBadges(),
            '/<!-- DESC_START -->(.*)<!-- DESC_END -->/s' => $this->getDescription(),
            '/<!-- COC_START -->(.*)<!-- COC_END -->/s' => $this->getCodeOfConduct(),
            '/<!-- USAGE_START -->(.*)<!-- USAGE_END -->/s' => $this->getUsage(),
            '/<!-- FAQ_START -->(.*)<!-- FAQ_END -->/s' => '',
            '/<!-- COPYRIGHT_START -->(.*)<!-- COPYRIGHT_END -->/s' => $this->getCopyright(),
            '/<!-- SECURITY_START -->(.*)<!-- SECURITY_END -->/s' => $this->getSecurityStatement(),
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

        $this->getEnvironment()->getFilesystem()->dumpFile(
            $this->getEnvironment()->path('README.md'),
            $readme,
        );
    }

    private function getReadmeContents(): string
    {
        $finder = $this->getEnvironment()->getFinder();
        $finder
            ->in($this->getEnvironment()->getAppPath())
            ->files()
            ->depth('== 0')
            ->name('README.md');

        $readmeContents = null;

        foreach ($finder as $file) {
            $readmeContents = $file->getContents();

            break;
        }

        if ($readmeContents === null) {
            throw new RuntimeException('Unable to get contents of README.md');
        }

        return $readmeContents;
    }

    private function getBadges(): string
    {
        return $this->getEnvironment()->getTwigEnvironment()->render(
            'readme/badges.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getDescription(): string
    {
        return $this->getEnvironment()->getTwigEnvironment()->render(
            'readme/description.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getCodeOfConduct(): string
    {
        if ($this->getAnswers()->codeOfConduct === CodeOfConduct::DEFAULT) {
            return '';
        }

        return $this->getEnvironment()->getTwigEnvironment()->render(
            'readme/code-of-conduct.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getUsage(): string
    {
        return $this->getEnvironment()->getTwigEnvironment()->render(
            'readme/usage.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getCopyright(): string
    {
        return $this->getEnvironment()->getTwigEnvironment()->render(
            'readme/copyright.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }

    private function getSecurityStatement(): string
    {
        if ($this->getAnswers()->securityPolicy === false) {
            return '';
        }

        return $this->getEnvironment()->getTwigEnvironment()->render(
            'readme/security.md.twig',
            $this->getAnswers()->getArrayCopy(),
        );
    }
}
