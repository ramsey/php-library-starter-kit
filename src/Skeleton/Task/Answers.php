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

namespace Ramsey\Skeleton\Task;

use ReflectionObject;
use ReflectionProperty;

use function array_combine;

final class Answers
{
    public ?string $authorEmail = null;
    public bool $authorHoldsCopyright = false;
    public ?string $authorName = null;
    public ?string $authorUrl = null;
    public ?string $codeOfConduct = null;
    public ?string $codeOfConductCommittee = null;
    public ?string $codeOfConductEmail = null;
    public ?string $codeOfConductPoliciesUrl = null;
    public ?string $codeOfConductReportingUrl = null;
    public ?string $commandPrefix = null;
    public ?string $copyrightEmail = null;
    public ?string $copyrightHolder = null;
    public ?string $copyrightUrl = null;
    public ?string $copyrightYear = null;
    public ?string $githubUsername = null;
    public ?string $license = null;
    public ?string $packageDescription = null;

    /** @var list<string> */
    public array $packageKeywords = [];

    public ?string $packageName = null;
    public ?string $packageNamespace = null;
    public ?string $projectName = null;
    public ?string $vendorName = null;

    /**
     * @return list<string>
     */
    public function getTokens(): array
    {
        $tokens = [];

        $reflected = new ReflectionObject($this);
        foreach ($reflected->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $tokens[] = $property->getName();
        }

        return $tokens;
    }

    /**
     * @return list<mixed>
     */
    public function getValues(): array
    {
        $values = [];

        $reflected = new ReflectionObject($this);
        foreach ($reflected->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            /** @psalm-var mixed */
            $values[] = $property->getValue($this);
        }

        return $values;
    }

    /**
     * @return array<string, mixed>
     */
    public function getArrayCopy(): array
    {
        /** @psalm-var array<string, mixed> */
        return (array) array_combine($this->getTokens(), $this->getValues());
    }
}
