<?php

/**
 * This file is part of the ramsey/php-library-skeleton project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Skeleton;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Clean;
use Ramsey\Skeleton\Task\Prompt;
use Ramsey\Skeleton\Task\Questions;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Twig_Environment;
use Twig_Extension_Escaper;
use Twig_Loader_Filesystem;

/**
 * `Setup` is a static class for use with the Composer post-create-project event.
 */
class Setup
{
    /**
     * Callback for the Composer post-create-project event
     *
     * @param Event $event
     */
    public static function wizard(Event $event)
    {
        $io = $event->getIO();
        $filesystem = new Filesystem();
        $finder = new Finder();

        $twig = static::getTwigEnvironment();
        /** @var Twig_Extension_Escaper $extension */
        $extension = $twig->getExtension(Twig_Extension_Escaper::class);
        $extension->setEscaper('json', static function ($env, string $string) {
            $encoded = json_encode($string, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            return $encoded ? substr($encoded, 1, -1) : '';
        });

        $prompt = static::getPrompt($io, $filesystem, $finder);
        $prompt->setQuestions(new Questions\InstallQuestions());
        $prompt->run();

        $build = static::getBuild($io, $filesystem, $finder);
        $build->setVariables($prompt->getAnswers());
        $build->setTwigEnvironment($twig);
        $build->run();

        $clean = static::getClean($io, $filesystem, $finder);
        $clean->run();

        echo PHP_EOL;
        echo sprintf(
            'Congratulations! Your project, %s, is ready!',
            $prompt->getAnswers()['packageName']
        );
        echo PHP_EOL;
    }

    /**
     * Returns a Prompt task.
     *
     * @param IOInterface $io
     * @param Filesystem $filesystem
     * @param Finder $finder
     *
     * @return Prompt
     *
     * @codeCoverageIgnore
     */
    public static function getPrompt(IOInterface $io, Filesystem $filesystem, Finder $finder): Prompt
    {
        return new Prompt($io, $filesystem, $finder);
    }

    /**
     * Returns a Build task.
     *
     * @param IOInterface $io
     * @param Filesystem $filesystem
     * @param Finder $finder
     *
     * @return Build
     *
     * @codeCoverageIgnore
     */
    public static function getBuild(IOInterface $io, Filesystem $filesystem, Finder $finder): Build
    {
        return new Build($io, $filesystem, $finder);
    }

    /**
     * Returns a Clean task.
     *
     * @param IOInterface $io
     * @param Filesystem $filesystem
     * @param Finder $finder
     *
     * @return Clean
     *
     * @codeCoverageIgnore
     */
    public static function getClean(IOInterface $io, Filesystem $filesystem, Finder $finder): Clean
    {
        return new Clean($io, $filesystem, $finder);
    }

    /**
     * @return Twig_Environment
     *
     * @codeCoverageIgnore
     */
    public static function getTwigEnvironment(): Twig_Environment
    {
        return new Twig_Environment(
            new Twig_Loader_Filesystem(realpath('.') . '/skeleton'),
            [
                'debug' => true,
                'strict_variables' => true,
                'autoescape' => false,
            ]
        );
    }
}
