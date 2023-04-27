<?php

/**
 * This file is part of ramsey/php-library-starter-kit
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace Ramsey\Dev\LibraryStarterKit;

use Ramsey\Dev\LibraryStarterKit\Console\Question\CodeOfConduct;
use Ramsey\Dev\LibraryStarterKit\Console\Question\License;
use ReflectionObject;
use ReflectionProperty;

use function array_keys;
use function array_values;
use function json_decode;
use function json_encode;
use function property_exists;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * Answers to questions prompted to the user building a library
 */
final class Answers
{
    public ?string $authorEmail = null;
    public bool $authorHoldsCopyright = true;
    public ?string $authorName = null;
    public ?string $authorUrl = null;
    public string $codeOfConduct = CodeOfConduct::DEFAULT;
    public ?string $codeOfConductCommittee = null;
    public ?string $codeOfConductEmail = null;
    public ?string $codeOfConductPoliciesUrl = null;
    public ?string $codeOfConductReportingUrl = null;
    public ?string $copyrightEmail = null;
    public ?string $copyrightHolder = null;
    public ?string $copyrightUrl = null;
    public ?string $copyrightYear = null;
    public ?string $githubUsername = null;
    public ?string $license = License::DEFAULT;
    public ?string $packageDescription = null;

    /** @var string[] */
    public array $packageKeywords = [];

    public ?string $packageName = null;
    public ?string $packageNamespace = null;
    public ?string $projectName = null;
    public bool $securityPolicy = true;
    public ?string $securityPolicyContactEmail = null;
    public ?string $securityPolicyContactFormUrl = null;
    public bool $skipPrompts = false;
    public ?string $vendorName = null;

    private string $saveToPath;
    private Filesystem $filesystem;

    public function __construct(string $saveToPath, Filesystem $filesystem)
    {
        $this->saveToPath = $saveToPath;
        $this->filesystem = $filesystem;
        $this->loadFile();
    }

    /**
     * Returns the property names a tokens to use in templates
     *
     * @return string[]
     */
    public function getTokens(): array
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * Returns the property values to replace tokens in template
     *
     * @return array<string | string[] | bool | null>
     */
    public function getValues(): array
    {
        return array_values($this->getArrayCopy());
    }

    /**
     * Returns an array of key-value pairs of token names and values
     *
     * @return array<string, string | string[] | bool | null>
     */
    public function getArrayCopy(): array
    {
        $answers = [];

        $reflected = new ReflectionObject($this);
        foreach ($reflected->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            /** @var string | string[] | bool | null $value */
            $value = $property->getValue($this);
            $answers[$property->getName()] = $value;
        }

        return $answers;
    }

    /**
     * Stores the answers to a JSON file on local disk
     */
    public function saveToFile(): void
    {
        $this->filesystem->dumpFile(
            $this->saveToPath,
            (string) json_encode(
                $this->getArrayCopy(),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
            ),
        );
    }

    /**
     * If an answers file already exists, loads the file and hydrates this
     * answers instance with the values
     */
    private function loadFile(): void
    {
        if (!$this->filesystem->exists($this->saveToPath)) {
            return;
        }

        $file = $this->filesystem->getFile($this->saveToPath);

        /** @var array<string, mixed> $answers */
        $answers = json_decode($file->getContents(), true);

        /** @var mixed $value */
        foreach ($answers as $propertyName => $value) {
            if (property_exists($this, $propertyName)) {
                $this->{$propertyName} = $value;
            }
        }
    }
}
