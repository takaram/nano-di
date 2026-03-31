<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php');

return (new Config())
    ->setFinder($finder)
    ->setRiskyAllowed(false)
    ->setRules([
        '@PER-CS3x0' => true,
    ]);
