<?php

namespace composer;

use Castor\Attribute\AsTask;

use function Castor\context;

/** @param string[] $args */
#[AsTask(description: 'Execute a composer command', aliases: ['composer'])]
function exec(
    #[AsRawTokens]
    array $args = [],
): void {
    \docker\exec('php', array_merge(['composer'], $args), context('interactive'));
}

/** @param string[] $args */
#[AsTask(description: 'Execute a global compose command', aliases: ['composer:global'])]
function execGlobal(
    #[AsRawTokens]
    array $args = [],
): void {
    \docker\exec('php', array_merge(['composer', 'global'], $args), context('interactive'));
}
