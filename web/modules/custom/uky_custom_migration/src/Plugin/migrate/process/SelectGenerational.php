<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;

/**
 * Provides a 'select_generational' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "select_generational"
 * )
 */
class SelectGenerational extends ProcessPluginBase {

  /**
   * The list of possible titles.
   *
   * @var array
   */
  protected $generationals = [
    'Jr.',
    'Sr.',
    'I',
    'II',
    'III',
    'IV',
    'V',
    'VI',
    'VII',
    'VIII',
    'IX',
    'X'
  ];

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Randomly select a title from the list.
    $random_generational = $this->generationals[array_rand($this->generationals)];
    return $random_generational;
  }
}