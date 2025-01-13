<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use DateTime;
use DateTimeZone;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Faker\Factory as FakerFactory;

/**
 * Generates a date range with a start date and end date.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_date_range"
 * )
 */
class GenerateDateRange extends ProcessPluginBase {

  /**
   * Transforms the value.
   *
   * This method generates a start date and an end date, formats them
   * correctly, and returns them as an array.
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
   *   The transformed value with 'value' and 'end_value' keys.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Create a Faker instance for generating fake data.
    $faker = FakerFactory::create();

    // Generate a random start date between -2 years and +2 years from now.
    $startDate = $faker->dateTimeBetween('-2 years', '+2 years', 'UTC');
    
    // Clone the start date to create the end date.
    $endDate = clone $startDate;
    // Modify the end date to be one day after the start date.
    $endDate->modify('+1 day');

    // Format the start date to 'Y-m-d\TH:i:s' format in UTC.
    $startDateFormatted = $startDate->format('Y-m-d\TH:i:s');
    // Format the end date to 'Y-m-d\TH:i:s' format in UTC.
    $endDateFormatted = $endDate->format('Y-m-d\TH:i:s');

    // Return the formatted start and end dates as an array.
    return [
      'value' => $startDateFormatted,
      'end_value' => $endDateFormatted,
    ];
  }

}