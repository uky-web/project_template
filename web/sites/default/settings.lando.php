<?php

$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['extension_discovery_scan_tests'] = FALSE;

$settings['hash_salt'] = '1234';

$databases['default']['default'] = [
    'database' => 'drupal8',
    'driver' => 'mysql',
    'host' => 'database',
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'password' =>  'drupal8',
    'port' => '3306',
    'prefix' => '',
    'username' => 'drupal8'
];

/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;