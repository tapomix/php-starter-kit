<?php

namespace docker;

use Castor\Attribute\AsTask;
use Castor\Console\Output\VerbosityLevel;
use Castor\Context;
use Symfony\Component\Process\Process;

use function Castor\capture;
use function Castor\context;
use function Castor\io;
use function Castor\run as castor_run;

#[AsTask(description: 'Build server', aliases: ['build'])]
function build(): void
{
    io()->title('Building server');

    castor_run(array_merge(buildBaseDockerComposeCmd(), ['up', '--detach', '--build', '--wait']), context: context()->withVerbosityLevel(VerbosityLevel::VERBOSE));
}

#[AsTask(description: 'Start server', aliases: ['start', 'up'])]
function start(): void
{
    io()->title('Starting server');

    capture(array_merge(buildBaseDockerComposeCmd(), ['up', '--detach', '--wait']));
}

#[AsTask(description: 'Stopping server', aliases: ['stop', 'down'])]
function stop(): void
{
    io()->title('Stopping server');

    capture(array_merge(buildBaseDockerComposeCmd(), ['down', '--remove-orphans']));
}

#[AsTask(description: 'Show server logs', aliases: ['logs'])]
function logs(): void
{
    castor_run(array_merge(buildBaseDockerComposeCmd(), ['logs', '-f']));
}

#[AsTask(description: 'Open terminal in container', aliases: ['shell'])]
function shell(string $service): void
{
    \docker\exec($service, ['bash'], context('interactive')->withTty(true));
}

/** @return string[] */
function buildBaseDockerComposeCmd(): array
{
    $cmd = [
        'docker',
        'compose',
        '--env-file=.env.docker',
    ];

    return $cmd;
}

/** @param string[] $command */
function run(string $service, array $command, ?Context $context = null): Process
{
    return castor_run(array_merge(buildBaseDockerComposeCmd(), ['run', '--rm'], [$service], $command), context: $context);
}

/** @param string[] $command */
function exec(string $service, array $command, ?Context $context = null): Process
{
    return castor_run(array_merge(buildBaseDockerComposeCmd(), ['exec'], [$service], $command), context: $context);
}
