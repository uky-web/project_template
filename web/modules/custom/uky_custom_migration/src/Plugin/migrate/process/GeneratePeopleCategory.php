<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\taxonomy\Entity\Term;
use Faker\Factory as FakerFactory;

/**
 * Generates a random taxonomy term reference for the specified vocabulary.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_people_category"
 * )
 */
class GeneratePeopleCategory extends ProcessPluginBase {

  /**
   * Transforms the value.
   *
   * This method generates a random taxonomy term reference.
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
   *   The transformed value as an array of term IDs.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $vocabulary = 'people_categories';

    // Load all terms in the specified vocabulary.
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary);

    // If no terms are found, create some default terms.
    if (empty($terms)) {
      $term_names = ['Faculty', 'Staff', 'Intern'];
      foreach ($term_names as $name) {
        $term = Term::create([
          'vid' => $vocabulary,
          'name' => $name,
        ]);
        $term->save();
        // Add the newly created term to the terms list.
        $terms[] = (object) ['tid' => $term->id(), 'name' => $name];
      }
    }

    // Create a Faker instance for generating random data.
    $faker = FakerFactory::create();

    // Pick a random term from the list.
    $random_term = $faker->randomElement($terms);

    // Ensure the term object has an ID.
    $term_id = $random_term->tid ?? $random_term->id();

    // Return the term ID as an array with target_id key.
    return [
      'target_id' => $term_id,
    ];
  }

}