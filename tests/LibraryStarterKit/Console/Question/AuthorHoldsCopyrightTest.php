<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorHoldsCopyright;

class AuthorHoldsCopyrightTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return AuthorHoldsCopyright::class;
    }

    protected function getQuestionName(): string
    {
        return 'authorHoldsCopyright';
    }

    protected function getQuestionText(): string
    {
        return 'Are you the copyright holder?';
    }

    /**
     * @inheritDoc
     */
    protected function getQuestionDefault()
    {
        return true;
    }
}
