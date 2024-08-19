<?php

namespace Drupal\newsroom_connector_item\Plugin\migrate\destination;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\newsroom_connector\MigrationManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides unpublish newsroom items destination plugin.
 *
 * @MigrateDestination(
 *   id = "unpublished_newsroom_item",
 *   requirements_met = true
 * )
 */
class UnpublishNewsroomItem extends DestinationBase implements ContainerFactoryPluginInterface {

  /**
   * Migration manager.
   *
   * @var \Drupal\newsroom_connector\MigrationManager
   */
  protected $migrationManager;

  /**
   * Constructs an entity destination plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   * @param \Drupal\newsroom_connector\MigrationManager $migration_manager
   *   Migration manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, MigrationManager $migration_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    $this->migrationManager = $migration_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('newsroom_connector.migration_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['id']['type'] = 'integer';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields(MigrationInterface $migration = NULL) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    /** @var \Drupal\node\Entity\Node $newsroom_item */
    $source_id = $row->getSourceProperty('item_id');
    $newsroom_item = $this->migrationManager->getEntityByNewsroomId($source_id, 'node', 'newsroom_item', 'type');
    if (!empty($newsroom_item)) {
      $newsroom_item->setUnpublished();
      $newsroom_item->save();
    }

    // We need to return TRUE, if we want to skip saving of mappings.
    return TRUE;
  }

}
