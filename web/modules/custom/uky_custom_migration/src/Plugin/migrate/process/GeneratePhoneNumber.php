<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;
use Faker\Factory as FakerFactory;

/**
 * Generates a fake phone number.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_phone_number"
 * )
 */
class GeneratePhoneNumber extends ProcessPluginBase {

  /**
   * Transforms the value.
   *
   * @param mixed $value
   *   The input value. This is not used in this plugin.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migration executable.
   * @param \Drupal\migrate\Row $row
   *   The migration row.
   * @param string $destination_property
   *   The destination property.
   *
   * @return string
   *   The transformed value.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $faker = FakerFactory::create();
    return $faker->phoneNumber;
  }
}