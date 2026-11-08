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

namespace Ramsey\Dev\LibraryStarterKit\Console;

use Ramsey\Dev\LibraryStarterKit\Answers;
use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorEmail;
use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorHoldsCopyright;
use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorName;
use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorUrl;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConduct;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductCommittee;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductEmail;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductPoliciesUrl;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConductReportingUrl;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightEmail;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightHolder;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightUrl;
use Ramsey\Dev\LibraryStarterKit\Console\Question\CopyrightYear;
use Ramsey\Dev\LibraryStarterKit\Console\Question\GithubUsername;
use Ramsey\Dev\LibraryStarterKit\Console\Question\License;
use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageDescription;
use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageKeywords;
use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageName;
use Ramsey\Dev\LibraryStarterKit\Console\Question\PackageNamespace;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SecurityPolicy;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SecurityPolicyContactEmail;
use Ramsey\Dev\LibraryStarterKit\Console\Question\SecurityPolicyContactFormUrl;
use Ramsey\Dev\LibraryStarterKit\Console\Question\StarterKitQuestion;
use Ramsey\Dev\LibraryStarterKit\Console\Question\VendorName;
use Symfony\Component\Console\Question\Question as SymfonyQuestion;

/**
 * A list of questions to ask the user upon installation
 */
class InstallQuestions
{
    /**
     * @return list<StarterKitQuestion & SymfonyQuestion>
     */
    public function getQuestions(Answers $answers): array
    {
        return [
            new GithubUsername($answers),
            new VendorName($answers),
            new PackageName($answers),
            new PackageDescription($answers),
            new AuthorName($answers),
            new AuthorEmail($answers),
            new AuthorUrl($answers),
            new AuthorHoldsCopyright($answers),
            new CopyrightHolder($answers),
            new CopyrightEmail($answers),
            new CopyrightUrl($answers),
            new CopyrightYear($answers),
            new License($answers),
            new SecurityPolicy($answers),
            new SecurityPolicyContactEmail($answers),
            new SecurityPolicyContactFormUrl($answers),
            new CodeOfConduct($answers),
            new CodeOfConductEmail($answers),
            new CodeOfConductCommittee($answers),
            new CodeOfConductPoliciesUrl($answers),
            new CodeOfConductReportingUrl($answers),
            new PackageKeywords($answers),
            new PackageNamespace($answers),
        ];
    }
}
