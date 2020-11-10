<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * ramsey/php-library-starter-kit is open source software: you can
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

namespace Ramsey\Dev\LibraryStarterKit\Task;

use ReflectionObject;
use ReflectionProperty;

use function array_combine;

/**
 * Answers to questions prompted to the user building a library
 */
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

    /** @var string[] */
    public array $packageKeywords = [];

    public ?string $packageName = null;
    public ?string $packageNamespace = null;
    public ?string $projectName = null;
    public ?string $vendorName = null;

    /**
     * Returns the property names a tokens to use in templates
     *
     * @return string[]
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
     * Returns the property values to replace tokens in template
     *
     * @return mixed[]
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
     * Returns an array of key-value pairs of token names and values
     *
     * @return array<string, mixed>
     */
    public function getArrayCopy(): array
    {
        /** @psalm-var array<string, mixed> */
        return (array) array_combine($this->getTokens(), $this->getValues());
    }
}
