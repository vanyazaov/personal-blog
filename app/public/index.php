<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

header('Content-Type: text/plain; charset=utf-8');

echo "PHP " . PHP_VERSION . "\n";
echo "OPcache: " . (\function_exists('opcache_get_status') ? 'on' : 'off') . "\n";
echo "Extensions: pdo_mysql=" . (\extension_loaded('pdo_mysql') ? 'yes' : 'no')
    . ", redis=" . (\extension_loaded('redis') ? 'yes' : 'no')
    . ", intl=" . (\extension_loaded('intl') ? 'yes' : 'no') . "\n";
