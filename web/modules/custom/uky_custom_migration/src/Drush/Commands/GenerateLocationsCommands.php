<?php

namespace Drupal\uky_custom_migration\Drush\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Utility\Token;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\location\Entity\Location; // Adjust this if the namespace is different.
use Faker\Factory as FakerFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Drush commands to generate ECK Location entities with Faker.
 */
final class GenerateLocationsCommands extends DrushCommands {

  /**
   * Constructs a GenerateLocationsCommands object.
   */
  public function __construct(
    private readonly Token $token,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('token'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Generates a given number of location entities (default 10).
   */
  #[CLI\Command(name: 'uky_custom_migration:generate-locations', aliases: ['genloc'])]
  #[CLI\Argument(name: 'count', description: 'Number of entities to generate')]
  #[CLI\Option(name: 'option-name', description: 'Generates 10 location entities.')]
  #[CLI\Usage(name: 'uky_custom_migration:generate-locations foo', description: 'Usage description')]
  public function generateLocations($count = 10, $options = ['option-name' => 'default']) {
    $faker = FakerFactory::create();

    for ($i = 0; $i < $count; $i++) {
      // Create a new ECK entity of type 'location' and bundle 'linked_location'.
      $location = $this->entityTypeManager->getStorage('location')->create([
        'type' => 'linked_location', // Ensure this is the correct bundle.
        'title' => $faker->company,
        'field_address' => $faker->address,
        'field_link' => ['uri' => $faker->url],
      ]);
      $location->save();
      $this->logger()->info(dt('Created location ID: @id', ['@id' => $location->id()]));
    }
  }

  /**
   * An example of the table output format.
   */
  #[CLI\Command(name: 'uky_custom_migration:token', aliases: ['token'])]
  #[CLI\FieldLabels(labels: [
    'group' => 'Group',
    'token' => 'Token',
    'name' => 'Name'
  ])]
  #[CLI\DefaultTableFields(fields: ['group', 'token', 'name'])]
  #[CLI\FilterDefaultField(field: 'name')]
  public function token($options = ['format' => 'table']): RowsOfFields {
    $all = $this->token->getInfo();
    $rows = [];
    foreach ($all['tokens'] as $group => $tokens) {
      foreach ($tokens as $key => $token) {
        $rows[] = [
          'group' => $group,
          'token' => $key,
          'name' => $token['name'],
        ];
      }
    }
    return new RowsOfFields($rows);
  }
}