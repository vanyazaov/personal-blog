<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/tests'])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/var/cache/php-cs-fixer.cache')
    ->setRules([
        '@PER-CS2.0'                  => true,
        '@PER-CS2.0:risky'            => true,
        '@PHP83Migration'             => true,
        '@PHP82Migration:risky'       => true,
        '@PHPUnit100Migration:risky'  => true,
        'declare_strict_types'        => true,
        'strict_param'                => true,
        'strict_comparison'           => true,
        'native_function_invocation'  => ['include' => ['@compiler_optimized'], 'scope' => 'namespaced'],
        'no_unused_imports'           => true,
        'ordered_imports'             => ['sort_algorithm' => 'alpha'],
        'array_syntax'                => ['syntax' => 'short'],
        'fully_qualified_strict_types'=> true,
        'global_namespace_import'     => ['import_classes' => true, 'import_constants' => true, 'import_functions' => false],
        'phpdoc_align'                => ['align' => 'left'],
        'phpdoc_order'                => true,
        'phpdoc_separation'           => true,
        'final_class'                 => false,
        'final_internal_class'        => true,
    ])
    ->setFinder($finder);
