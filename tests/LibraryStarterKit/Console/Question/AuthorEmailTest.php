<?php

declare(strict_types=1);

namespace Ramsey\Test\Dev\LibraryStarterKit\Console\Question;

use Ramsey\Dev\LibraryStarterKit\Console\Question\AuthorEmail;

class AuthorEmailTest extends QuestionTestCase
{
    protected function getTestClass(): string
    {
        return AuthorEmail::class;
    }

    protected function getQuestionName(): string
    {
        return 'authorEmail';
    }

    protected function getQuestionText(): string
    {
        return 'What is your email address?';
    }
}
