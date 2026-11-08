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

use Ramsey\Dev\LibraryStarterKit\Answers;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use function assert;

/**
 * Asks whether the project wants a FUNDING.yml file
 *
 * A yes answers with the existing funding map, or an empty map if there is
 * none yet. A no answers with null.
 */
class Funding extends ConfirmationQuestion implements StarterKitQuestion
{
    use AnswersTool;

    public function getName(): string
    {
        return 'funding';
    }

    public function __construct(Answers $answers)
    {
        parent::__construct(
            'Do you want to add a FUNDING.yml file to display a sponsor button on GitHub?',
            $answers->funding !== null,
        );

        $this->answers = $answers;

        $default = $this->getNormalizer();

        // Symfony's ConfirmationQuestion always sets a normalizer.
        assert($default !== null);

        $this->setNormalizer(
            fn (mixed $answer): ?array => $default($answer) ? ($this->answers->funding ?? []) : null,
        );
    }
}
