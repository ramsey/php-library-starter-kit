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

namespace Ramsey\Skeleton\Task;

use Twig_Environment;

/**
 * The `Build` processes the skeleton templates and copies them to their new
 * locations for the project.
 */
class Build extends AbstractTask
{
    /**
     * @var array
     */
    private $variables = [];

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Sets the variables to inject into templates.
     *
     * @param array $variables
     *
     * @return Build
     */
    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Returns the template variables.
     *
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Sets the Twig environment to use with this `Build` task.
     *
     * @param Twig_Environment $twig
     *
     * @return Build
     */
    public function setTwigEnvironment(Twig_Environment $twig): self
    {
        $this->twig = $twig;

        return $this;
    }

    /**
     * Returns this `Build` task's Twig environment.
     *
     * @return Twig_Environment
     */
    public function getTwigEnvironment(): Twig_Environment
    {
        return $this->twig;
    }

    /**
     * Runs the `Build` task.
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run(): void
    {
        $root = realpath('.');
        $variables = $this->getVariables();

        $templates = clone $this->getFinder();

        // Return back into a variable of the same name to allow easier mocking
        // of this code in tests.
        $templates = $templates
            ->ignoreDotFiles(false)
            ->files()
            ->in($root . '/skeleton');

        $twig = $this->getTwigEnvironment();

        foreach ($templates as $template) {
            $templatePath = $template->getRelativePathname();

            if (substr($templatePath, -5) !== '.twig') {
                $this->getFilesystem()->dumpFile($templatePath, $template->getContents());
                continue;
            }

            $savePath = str_replace(
                array_keys($variables),
                array_values($variables),
                substr($templatePath, 0, -5)
            );

            $this->getFilesystem()->dumpFile(
                $savePath,
                $twig->render($templatePath, $this->getVariables())
            );
        }
    }
}
