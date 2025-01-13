<?php

namespace Drupal\uky_custom_migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Faker\Factory as Faker;

/**
 * Provides a 'generate_paragraph' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *   id = "generate_paragraph"
 * )
 */
class GenerateParagraph extends ProcessPluginBase {

  /**
   * The list of possible paragraphs.
   */
  const PARAGRAPHS = [
    'body', 'zipper', 'link_lists', 'calls_to_action', 'pullquote',
    'custom_teasers', 'carousel', 'statistics', 'media', 'gallery',
    'embederator', 'flex_grid', '50_50_slab', '2_1_slab'
  ];

  /**
   * The list of possible building numbers.
   */
  const BUILDING_NUMBERS = [
    '0001', '0003', '0004', '0005', '0009', '0012', '0013', '0014',
    '0015', '0016', '0017', '0019', '0020', '0021', '0022', '0023',
    '0024', '0025', '0027', '0028', '0031', '0032', '0033', '0034'
  ];

  /**
   * The list of possible background options.
   */
  const BACKGROUND_OPTIONS = [
    'none', 'radial', 'speckled', 'splash', 'squares', 'stone'
  ];

  /**
   * Faker instance.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Constructs a GenerateParagraph object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->faker = Faker::create('en_US');
  }

  /**
   * Transforms the value into a paragraph reference.
   *
   * @param mixed $value
   *   The value to transform.
   * @param \Drupal\migrate\MigrateExecutableInterface $migrate_executable
   *   The migrate executable.
   * @param \Drupal\migrate\Row $row
   *   The row object.
   * @param string $destination_property
   *   The destination property.
   *
   * @return array
   *   A reference to the created paragraph.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Access the entity type manager service.
    $entity_type_manager = \Drupal::service('entity_type.manager');

    // Randomly select a paragraph type from the list.
    $random_paragraph_type = self::PARAGRAPHS[array_rand(self::PARAGRAPHS)];
    $paragraph = $this->createParagraph($random_paragraph_type);

    // Set the fields of the paragraph based on its type.
    $this->setParagraphFields($paragraph, $random_paragraph_type, $entity_type_manager);

    // Save the paragraph entity.
    $paragraph->save();

    // Return the paragraph reference.
    return [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ];
  }

  /**
   * Creates a paragraph entity of the given type.
   *
   * @param string $type
   *   The paragraph type.
   *
   * @return \Drupal\paragraphs\Entity\Paragraph
   *   The created paragraph entity.
   */
  protected function createParagraph($type) {
    return Paragraph::create(['type' => $type]);
  }

  /**
   * Sets fields on the paragraph entity based on its type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param string $type
   *   The paragraph type.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setParagraphFields(Paragraph $paragraph, $type, $entity_type_manager) {
    // Set the headline group field for certain paragraph types.
    if (in_array($type, ['body', 'zipper', 'link_lists', 'calls_to_action', 'custom_teasers', 'carousel', 'statistics', 'gallery', 'flex_grid', '50_50_slab', '2_1_slab'])) {
      $paragraph->set('field_headline_group', [
        'superhead' => $this->faker->words(3, true),
        'headline' => $this->faker->words(5, true),
        'subhead' => $this->faker->words(4, true),
      ]);
    }

    // Set the body field for certain paragraph types.
    if (in_array($type, ['body', 'zipper', 'carousel', 'gallery'])) {
      $paragraph->set('field_body', [
        'value' => $this->faker->paragraphs(3, true),
        'format' => 'basic_html',
      ]);
    }

    // Set specific fields based on the paragraph type.
    switch ($type) {
      case 'carousel':
        $this->setCarouselFields($paragraph, $entity_type_manager);
        break;
      case 'statistics':
        $this->setStatisticsFields($paragraph, $entity_type_manager);
        break;
      case 'zipper':
        $this->setZipperFields($paragraph, $entity_type_manager);
        break;
      case 'link_lists':
      case 'calls_to_action':
        $this->setLinkListsOrCallsToActionFields($paragraph, $entity_type_manager);
        break;
      case 'pullquote':
        $this->setPullquoteFields($paragraph, $entity_type_manager);
        break;
      case 'custom_teasers':
        $this->setCustomTeasersFields($paragraph, $entity_type_manager);
        break;
      case 'media':
        $this->setMediaFields($paragraph, $entity_type_manager);
        break;
      case 'embederator':
        $this->setEmbederatorFields($paragraph, $entity_type_manager);
        break;
      case '50_50_slab':
        $this->set50_50SlabFields($paragraph, $entity_type_manager);
        break;
      case '2_1_slab':
        $this->set2_1SlabFields($paragraph, $entity_type_manager);
        break;
      case 'flex_grid':
        $this->setFlexGridFields($paragraph, $entity_type_manager);
        break;
    }
  }

  /**
   * Sets fields for 'carousel' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setCarouselFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_theme_carousel_centered', $this->faker->randomElement(['yes', 'no']));
    $paragraph->set('field_theme_color_scheme', $this->faker->word);
    $paragraph->set('field_collection_items', $this->createCollectionItems('media_figure', 3, $entity_type_manager));
  }

  /**
   * Sets fields for 'statistics' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setStatisticsFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_theme_color_scheme', $this->faker->word);
    $paragraph->set('field_theme_show_divisions', $this->faker->randomElement(['yes', 'no']));
    $paragraph->set('field_collection_items', $this->createCollectionItems('statistic', 3, $entity_type_manager));
  }

  /**
   * Sets fields for 'zipper' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setZipperFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_collection_items', $this->createCollectionItems('accordion_panel', 3, $entity_type_manager));
  }

  /**
   * Sets fields for 'link_lists' or 'calls_to_action' paragraph types.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setLinkListsOrCallsToActionFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_collection_items', $this->createCollectionItems('link_collection', 3, $entity_type_manager));
  }

  /**
   * Sets fields for 'pullquote' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setPullquoteFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_collection_item', $this->createPullquoteItem($entity_type_manager));
  }

  /**
   * Sets fields for 'custom_teasers' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setCustomTeasersFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_collection_items', $this->createCollectionItems('custom_teaser', 3, $entity_type_manager));
  }

  /**
   * Sets fields for 'media' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setMediaFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_collection_item', $this->createCollectionItem('media_figure', $entity_type_manager));
  }

  /**
   * Sets fields for 'embederator' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setEmbederatorFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_embed_type', $this->createEmbederatorItem($entity_type_manager));
  }

  /**
   * Sets fields for '50_50_slab' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function set50_50SlabFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_background', self::BACKGROUND_OPTIONS[array_rand(self::BACKGROUND_OPTIONS)]);
    $paragraph->set('field_main', $this->createCollectionItem('main', $entity_type_manager));
    $paragraph->set('field_aside', $this->createCollectionItem('aside', $entity_type_manager));
  }

  /**
   * Sets fields for '2_1_slab' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function set2_1SlabFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_flip', $this->faker->boolean);
    $paragraph->set('field_main', $this->createCollectionItem('main', $entity_type_manager));
    $paragraph->set('field_aside', $this->createCollectionItem('aside', $entity_type_manager));
  }

  /**
   * Sets fields for 'flex_grid' paragraph type.
   *
   * @param \Drupal\paragraphs\Entity\Paragraph $paragraph
   *   The paragraph entity.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  protected function setFlexGridFields(Paragraph $paragraph, $entity_type_manager) {
    $paragraph->set('field_collection_items', $this->createCollectionItems('flex_grid_item', 3, $entity_type_manager));
  }

  /**
   * Creates a collection item of the given type.
   *
   * @param string $type
   *   The item type.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @return array
   *   The collection item reference array.
   */
  protected function createCollectionItem($type, $entity_type_manager) {
    $item = $entity_type_manager->getStorage('collection_item')->create([
      'type' => $type,
      'title' => $this->faker->sentence(6, true),
      'field_description' => [
        'value' => $this->faker->paragraph(3, true),
        'format' => 'basic_html',
      ],
    ]);
    $item->save();

    return [
      'target_id' => $item->id(),
      'target_type' => 'collection_item',
    ];
  }

  /**
   * Creates multiple collection items of the given type.
   *
   * @param string $type
   *   The item type.
   * @param int $count
   *   The number of items to create.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @return array
   *   The collection item references array.
   */
  protected function createCollectionItems($type, $count, $entity_type_manager) {
    $items = [];
    for ($i = 0; $i < $count; $i++) {
      $items[] = $this->createCollectionItem($type, $entity_type_manager);
    }
    return $items;
  }

  /**
   * Creates an embederator item.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @return array
   *   The embederator item reference array.
   */
  protected function createEmbederatorItem($entity_type_manager) {
    $item = $entity_type_manager->getStorage('embederator')->create([
      'type' => 'uky_maps_embed',
      'title' => $this->faker->sentence(6, true),
      'field_iframe_height' => $this->faker->numberBetween(300, 9999),
      'field_building_number' => self::BUILDING_NUMBERS[array_rand(self::BUILDING_NUMBERS)],
    ]);
    $item->save();

    return [
      'target_id' => $item->id(),
      'target_type' => 'embederator',
    ];
  }

  /**
   * Creates a pullquote item.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @return array
   *   The pullquote item reference array.
   */
  protected function createPullquoteItem($entity_type_manager) {
    $item = $entity_type_manager->getStorage('collection_item')->create([
      'type' => 'pullquote',
      'field_attribution_1' => $this->faker->name,
      'field_attribution_2' => $this->faker->company,
      'field_brief' => $this->faker->boolean,
      'field_theme_color_scheme' => $this->faker->word,
      'field_description' => [
        'value' => $this->faker->paragraph(3, true),
        'format' => 'basic_html',
      ],
    ]);
    $item->save();

    return [
      'target_id' => $item->id(),
      'target_type' => 'collection_item',
    ];
  }
}