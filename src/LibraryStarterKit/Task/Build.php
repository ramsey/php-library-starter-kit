<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit\Task;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Setup;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\Cleanup;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\FixStyle;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\InstallDependencies;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\RenameTemplates;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\RunTests;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\SetupRepository;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateChangelog;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateCodeOfConduct;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateComposerJson;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateContributing;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateFunding;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateLicense;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateNamespace;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateReadme;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateSecurityPolicy;
use Ramsey\Dev\LibraryStarterKit\Task\Builder\UpdateSourceFileHeaders;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * The Build task executes all the builders used to build the library
 */
class Build
{
    private Answers $answers;
    private Setup $setup;
    private SymfonyStyle $console;

    public function __construct(
        Setup $setup,
        SymfonyStyle $console,
        Answers $answers,
    ) {
        $this->setup = $setup;
        $this->console = $console;
        $this->answers = $answers;
    }

    public function getAnswers(): Answers
    {
        return $this->answers;
    }

    public function getConsole(): SymfonyStyle
    {
        return $this->console;
    }

    public function getSetup(): Setup
    {
        return $this->setup;
    }

    /**
     * Executes each builder
     */
    public function run(): void
    {
        foreach ($this->getBuilders() as $builder) {
            $builder->build();
        }
    }

    /**
     * Returns a list of builders to use for creating a library
     *
     * @return Builder[]
     */
    public function getBuilders(): array
    {
        return [
            new RenameTemplates($this),
            new UpdateComposerJson($this),
            new UpdateReadme($this),
            new UpdateLicense($this),
            new UpdateSourceFileHeaders($this),
            new UpdateNamespace($this),
            new UpdateSecurityPolicy($this),
            new UpdateCodeOfConduct($this),
            new UpdateChangelog($this),
            new UpdateContributing($this),
            new UpdateFunding($this),
            new InstallDependencies($this),
            new Cleanup($this),
            new FixStyle($this),
            new SetupRepository($this),
            new RunTests($this),
        ];
    }
}
