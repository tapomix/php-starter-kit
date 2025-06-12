<?php

namespace laravel;

use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;
use Castor\Exception\ProblemException;

use function Castor\context;
use function Castor\fs;
use function Castor\io;
use function Castor\run;

/** @param string[] $args */
#[AsTask(description: 'Execute artisan command', aliases: ['artisan'])]
function artisan(
    #[AsRawTokens]
    array $args = [],
): void {
    if (!fs()->exists(__DIR__ . '/../artisan')) {
        throw new ProblemException('Binary artisan not found');
    }

    \docker\exec('php', array_merge(['php', 'artisan'], $args), context('interactive'));
}

#[AsTask(description: 'List all routes')]
function routes(): void
{
    artisan(['route:list']);
}

#[AsTask(description: 'Initialize a Laravel website', aliases: ['laravel'])]
function install(): void
{
    // check if composer already exists
    if (fs()->exists(__DIR__ . '/../composer.json')) {
        io()->warning('Project already initialized');

        return;
    }

    io()->title('Init project');

    // laravel installer
    \composer\execGlobal(['require', 'laravel/installer']);
    // create new project
    $tmpApp = 'code';
    \docker\exec('php', ['laravel', 'new', '--no-ansi', '--no-interaction', $tmpApp]); // relative path (because it's executed in container)
    fs()->mirror(__DIR__ . '/../' . $tmpApp, __DIR__ . '/../');
    fs()->remove(__DIR__ . '/../' . $tmpApp);
    // dev dependencies
    \composer\exec(['require', 'barryvdh/laravel-debugbar', '--dev']);
    \composer\exec(['require', 'barryvdh/laravel-ide-helper', '--dev']);
    artisan(['ide-helper:generate']);
    artisan(['ide-helper:models']);
    // qa tools
    \composer\exec(['require', 'driftingly/rector-laravel', '--dev']);
    \composer\exec(['require', 'larastan/larastan', '--dev']);

    // init git
    if (!fs()->exists(__DIR__ . '/../.git')) {
        run(['git', 'init', __DIR__ . '/../.']);
    }
}
