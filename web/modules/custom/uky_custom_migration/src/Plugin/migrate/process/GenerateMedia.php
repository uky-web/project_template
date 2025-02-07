<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Faker\Factory as FakerFactory;
use Drupal\file\Entity\File;
use Psr\Log\LoggerInterface;

/**
 * Provides a 'generate_media' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_media"
 * )
 */
class GenerateMedia extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a GenerateMedia object.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('logger.factory')->get('migrate')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $faker = FakerFactory::create();

    // Randomize background and text colors.
    $bg_color = substr($faker->hexColor, 1); // Remove the "#" prefix.
    $text_color = substr($faker->hexColor, 1); // Remove the "#" prefix.

    // Avoid having the same color for background and text.
    while ($bg_color === $text_color) {
      $text_color = substr($faker->hexColor, 1);
    }

    // Customize the placeholder image URL.
    $width = 800;
    $height = 600;
    $format = 'jpg'; // Image format.
    $text = urlencode($faker->sentence(3)); // Custom text for the image.
    $image_url = "https://placehold.co/{$width}x{$height}/{$bg_color}/{$text_color}.{$format}?text={$text}";

    try {
      // Download the image from placehold.co.
      $image_data = @file_get_contents($image_url);
      if ($image_data === FALSE) {
        throw new \Exception('Failed to download image from URL: ' . $image_url);
      }

      // Generate a unique file name and URI.
      $file_name = uniqid() . '.jpg';
      $uri = 'public://' . $file_name;

      // Save the file data to Drupal's file system.
      $file = File::create([
        'uri' => $uri,
        'status' => 1, // Mark the file as permanent.
      ]);

      // Write the image data to the file system.
      if (!file_put_contents($file->getFileUri(), $image_data)) {
        throw new \Exception('Failed to write file to the public file system.');
      }

      // Save the file entity.
      $file->save();

      // Create a media entity.
      $media = $this->entityTypeManager->getStorage('media')->create([
        'bundle' => 'image',
        'name' => $faker->sentence(3),
        'status' => 1, // Ensure the media is published.
        'field_media_image' => [
          'target_id' => $file->id(),
          'alt' => $faker->sentence(6),
          'title' => $faker->sentence(3),
        ],
      ]);
      $media->save();

      // Return the media entity ID.
      return $media->id();

    } catch (\Exception $e) {
      // Log the error.
      $this->logger->error($e->getMessage());
      // Optionally, return a default value or null.
      return null;
    }
  }
}
