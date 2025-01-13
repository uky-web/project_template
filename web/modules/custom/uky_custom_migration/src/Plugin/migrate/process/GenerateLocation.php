<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a 'generate_location' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_location"
 * )
 */
class GenerateLocation extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a GenerateLocation object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Load all location entities.
    $query = $this->entityTypeManager->getStorage('location')->getQuery();
    $query->accessCheck(false); // Disable access check for this query.
    $location_ids = $query->execute();

    // Check if any locations are available.
    if (empty($location_ids)) {
      throw new \Exception('No locations found in the database.');
    }

    // Get a random location ID.
    $random_id = array_rand($location_ids);

    // Return the random location ID.
    return $location_ids[$random_id];
  }
}