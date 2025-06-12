<?php

namespace qa;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Castor\Exception\ProblemException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

use function Castor\fs;
use function Castor\io;
use function Castor\parallel;

#[AsTask(description: 'Run all QA tools', aliases: ['qa'])]
function all(bool $parallel = false): int
{
    io()->title('Running all QA tools');

    if ($parallel) {
        [$rector, $phpstan, $pint] = parallel(
            fn (): Process => rector(),
            fn (): Process => phpstan(),
            fn (): Process => pint(),
        );
    } else {
        $rector = rector();
        $phpstan = phpstan();
        $pint = pint();
    }

    return max(
        $rector->getExitCode() ?? 0,
        $phpstan->getExitCode() ?? 0,
        $pint->getExitCode() ?? 0,
    );
}

#[AsTask(description: 'Run PHPStan', aliases: ['phpstan'])]
function phpstan(): Process
{
    $binary = 'vendor/bin/phpstan';

    if (!fs()->exists(__DIR__ . '/../' . $binary)) {
        throw new ProblemException('Binary phpstan not found');
    }

    io()->title('Running PHPStan');

    return \docker\exec('php', [$binary, 'analyse', '--memory-limit', '256M']);
}

#[AsTask(description: 'Run Rector', aliases: ['rector'])]
function rector(
    #[AsOption(shortcut: 'f', description: 'Really fix issues', mode: InputOption::VALUE_NEGATABLE)]
    bool $fix = false,
): Process {
    $binary = 'vendor/bin/rector';

    if (!fs()->exists(__DIR__ . '/../' . $binary)) {
        throw new ProblemException('Binary rector not found');
    }

    $cmd = [$binary, 'process'];
    if (!$fix) {
        $cmd = array_merge($cmd, ['--dry-run', '--debug']);
    }

    io()->title('Running Rector'.($fix ? '' : ' (**dry-run**)'));

    return \docker\exec('php', $cmd);
}

#[AsTask(description: 'Run PHP-CS-Fixer', aliases: ['phpcs', 'cs'])]
function phpcs(
    #[AsOption(shortcut: 'f', description: 'Really fix issues', mode: InputOption::VALUE_NEGATABLE)]
    bool $fix = false,
): Process {
    $binary = 'vendor/bin/php-cs-fixer';

    if (!fs()->exists(__DIR__ . '/../' . $binary)) {
        throw new ProblemException('Binary php-cs-fixer not found');
    }

    $cmd = [$binary, 'fix'];
    if (!$fix) {
        $cmd = array_merge($cmd, ['--dry-run', '-vv', '--diff', '--show-progress=dots']);
    }

    io()->title('Running PHP-CS-Fixer'.($fix ? '' : ' (**dry-run**)'));

    return \docker\exec('php', $cmd);
}

#[AsTask(description: 'Lint Twig templates', aliases: ['lint'])]
function lint(): Process
{
    if (!fs()->exists(__DIR__ . '/../vendor/symfony/twig-bundle')) {
        throw new ProblemException('Twig bundle not found');
    }

    io()->title('Running Twig Linter');

    return \docker\exec('php', ['php', 'bin/console', 'lint:twig', '--show-deprecations', 'templates/']);
}

#[AsTask(description: 'Run Pint', aliases: ['pint'])]
function pint(): Process
{
    $binary = 'vendor/bin/pint';

    if (!fs()->exists(__DIR__ . '/../' . $binary)) {
        throw new ProblemException('Binary pint not found');
    }

    $cmd = [$binary, 'app/', '--test', '-v'];

    return \docker\exec('php', $cmd);
}
