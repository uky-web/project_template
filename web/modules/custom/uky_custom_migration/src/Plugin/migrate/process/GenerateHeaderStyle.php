<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;

/**
 * Provides a 'generate_header_style' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_header_style"
 * )
 */
class GenerateHeaderStyle extends ProcessPluginBase {

  /**
   * The list of possible titles.
   *
   * @var array
   */
  protected $generationals = [
    'full_width',
    'full_width_3x1',
    'split_header',
    'split_header_16x9',
    'text_only'
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