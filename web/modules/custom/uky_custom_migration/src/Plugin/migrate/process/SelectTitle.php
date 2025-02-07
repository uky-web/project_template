<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;

/**
 * Provides a 'select_title' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "select_title"
 * )
 */
class SelectTitle extends ProcessPluginBase {

  /**
   * The list of possible titles.
   *
   * @var array
   */
  protected $titles = [
    'Mr.',
    'Mrs.',
    'Miss',
    'Ms.',
    'Dr.',
    'Prof.'
  ];

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Randomly select a title from the list.
    $random_title = $this->titles[array_rand($this->titles)];
    return $random_title;
  }
}