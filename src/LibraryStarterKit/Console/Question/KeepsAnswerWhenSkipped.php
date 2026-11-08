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

/**
 * A skippable question whose answer is left as it is when the wizard skips it,
 * instead of being set to the question's default
 */
interface KeepsAnswerWhenSkipped extends SkippableQuestion
{
}
