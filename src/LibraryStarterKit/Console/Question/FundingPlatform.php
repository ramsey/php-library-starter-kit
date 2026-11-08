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

namespace Ramsey\Dev\LibraryStarterKit\Console\Question;

use function array_map;

/**
 * A funding platform that GitHub's FUNDING.yml file can link to
 *
 * The value of each case is the platform key used in FUNDING.yml, and the
 * cases are in the order GitHub lists them.
 */
enum FundingPlatform: string
{
    case CommunityBridge = 'community_bridge';
    case Github = 'github';
    case IssueHunt = 'issuehunt';
    case KoFi = 'ko_fi';
    case Liberapay = 'liberapay';
    case OpenCollective = 'open_collective';
    case Patreon = 'patreon';
    case Tidelift = 'tidelift';
    case Polar = 'polar';
    case BuyMeACoffee = 'buy_me_a_coffee';
    case ThanksDev = 'thanks_dev';
    case Custom = 'custom';

    /**
     * Returns the platform keys, in the order of the cases
     *
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_map(fn (self $platform): string => $platform->value, self::cases());
    }

    /**
     * Returns the name of the platform, as people know it
     */
    public function label(): string
    {
        return match ($this) {
            self::CommunityBridge => 'LFX Mentorship',
            self::Github => 'GitHub Sponsors',
            self::IssueHunt => 'IssueHunt',
            self::KoFi => 'Ko-fi',
            self::Liberapay => 'Liberapay',
            self::OpenCollective => 'Open Collective',
            self::Patreon => 'Patreon',
            self::Tidelift => 'Tidelift',
            self::Polar => 'Polar',
            self::BuyMeACoffee => 'Buy Me a Coffee',
            self::ThanksDev => 'thanks.dev',
            self::Custom => 'Custom URL',
        };
    }

    /**
     * Returns whether FUNDING.yml accepts a list of values for this platform
     */
    public function acceptsList(): bool
    {
        return $this === self::Github || $this === self::Custom;
    }

    /**
     * Returns the text of the question that asks for this platform's value
     */
    public function prompt(): string
    {
        return match ($this) {
            self::CommunityBridge => 'What is your LFX Mentorship project name?',
            self::Github => 'Which GitHub Sponsors usernames should the button link to? '
                . 'Enter up to four, comma-separated',
            self::IssueHunt => 'What is your IssueHunt username?',
            self::KoFi => 'What is your Ko-fi username?',
            self::Liberapay => 'What is your Liberapay username?',
            self::OpenCollective => 'What is your Open Collective username?',
            self::Patreon => 'What is your Patreon username?',
            self::Tidelift => 'What is your Tidelift package, as PLATFORM-NAME/PACKAGE-NAME '
                . '(for example, packagist/acme/awesome)?',
            self::Polar => 'What is your Polar username?',
            self::BuyMeACoffee => 'What is your Buy Me a Coffee username?',
            self::ThanksDev => 'What is your thanks.dev identifier, as u/gh/USERNAME?',
            self::Custom => 'Which custom funding URLs should the button link to? '
                . 'Enter up to four, comma-separated',
        };
    }
}
