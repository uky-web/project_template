<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Faker\Factory as FakerFactory;

/**
 * Generates a headline group with a superhead and a subhead.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_headline_group"
 * )
 */
class GenerateHeadlineGroup extends ProcessPluginBase {

  /**
   * Transforms the value.
   *
   * This method generates a superhead and a subhead.
   *
   * @param mixed $value
   *   The input value (not used in this plugin).
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migrate executable instance.
   * @param \Drupal\migrate\Row $row
   *   The current migration row.
   * @param string $destination_property
   *   The destination property name.
   *
   * @return array
   *   The transformed value with 'superhead' and 'subhead' keys.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Create a Faker instance for generating fake data.
    $faker = FakerFactory::create();

    // Generate a superhead and a subhead.
    $superhead = $faker->sentence(6, true); // Generate a superhead with 6 words.
    $subhead = $faker->sentence(10, true); // Generate a subhead with 10 words.
    
    return [
      'superhead' => $superhead,
      'subhead' => $subhead,
    ];
  }

}