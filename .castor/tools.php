<?php

namespace tools;

use Castor\Attribute\AsTask;

use function Castor\load_dot_env;
use function Castor\open;

// @see https://stackoverflow.com/a/75956168 (to fix vscode settings)

#[AsTask(description: 'Open browser', aliases: ['open', 'browse'])]
function browse(): void
{
    // need to load .env.docker file to get DC_SRV_NAME and DC_SRV_PORT in $_SERVER
    load_dot_env(__DIR__ . '/../.env.docker');

    open('https://'.($_SERVER['DC_SRV_NAME'] ?? 'localhost').':'.($_SERVER['DC_SRV_PORT'] ?? 443));
}

#[AsTask(description: 'Open Mailpit instance', aliases: ['mailpit'])]
function mailpit(): void
{
    open('http://localhost:8525');
}

#[AsTask(description: 'Open DBGate instance', aliases: ['dbgate'])]
function dbgate(): void
{
    open('http://localhost:8800');
}
