<?php

declare(strict_types=1);

namespace Ramsey\Skeleton\Test;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Ramsey\Skeleton\Setup;
use Ramsey\Skeleton\Task\Build;
use Ramsey\Skeleton\Task\Clean;
use Ramsey\Skeleton\Task\Prompt;
use Ramsey\Skeleton\Task\Questions\InstallQuestions;

class SetupTest extends TestCase
{
    public function testWizard()
    {
        $answers = [
            'packageName' => 'foo/bar',
        ];

        $twig = \Mockery::mock(\Twig_Environment::class, [
            'addFilter' => null,
        ]);

        $prompt = \Mockery::mock(Prompt::class, [
            'getAnswers' => $answers,
        ]);
        $prompt->expects()->setQuestions(anInstanceOf(InstallQuestions::class));
        $prompt->expects()->run();

        $build = \Mockery::mock(Build::class);
        $build->expects()->setVariables($answers);
        $build->expects()->setTwigEnvironment(anInstanceOf(\Twig_Environment::class));
        $build->expects()->run();

        $clean = \Mockery::mock(Clean::class);
        $clean->expects()->run();

        $io = \Mockery::mock(IOInterface::class);

        $event = \Mockery::mock(Event::class, [
            'getIO' => $io,
        ]);

        $setup = \Mockery::mock(Setup::class);
        $setup->shouldReceive('wizard')->passthru();
        $setup->shouldReceive('getPrompt')->andReturn($prompt);
        $setup->shouldReceive('getBuild')->andReturn($build);
        $setup->shouldReceive('getClean')->andReturn($clean);
        $setup->shouldReceive('getTwigEnvironment')->andReturn($twig);

        ob_start();

        $setup::wizard($event);

        $output = ob_get_clean();

        $this->assertSame(
            PHP_EOL . 'Congratulations! Your project, foo/bar, is ready!' . PHP_EOL,
            $output
        );
    }
}
