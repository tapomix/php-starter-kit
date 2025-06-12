<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PHP83Migration' => true,
        '@PSR12' => true,
        '@PhpCsFixer' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(false)
;
