<?php

namespace Drupal\newsroom_connector_service\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\migrate\Event\MigrateRollbackEvent;
use Drupal\migrate\Event\MigrateRowDeleteEvent;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Event\MigrateEvents as MigratePlusEvents;
use Drupal\newsroom_connector\MigrationManager;
use Drupal\newsroom_connector_service\Plugin\newsroom\NewsroomServiceNewsroomProcessor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Sync newsroom services images.
 */
class MigrationImageSync implements EventSubscriberInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Migration manager.
   *
   * @var \Drupal\newsroom_connector\MigrationManager
   */
  protected $migrationManager;

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManager
   */
  protected $migrationPluginManager;

  /**
   * MigrationImportSync constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param \Drupal\newsroom_connector\MigrationManager $migration_manager
   *   Migration manager.
   */
  public function __construct(EventDispatcherInterface $dispatcher, MigrationManager $migration_manager) {
    $this->dispatcher = $dispatcher;
    $this->migrationManager = $migration_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[MigrateEvents::PRE_IMPORT][] = ['sync'];
    return $events;
  }

  /**
   * Event callback to sync source and destination.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The migration import event.
   */
  public function sync(MigrateImportEvent $event) {
    $migration = $event->getMigration();
    if ($migration->id() == NewsroomServiceNewsroomProcessor::MIGRATION_SERVICE) {
      $source = clone $migration->getSourcePlugin();
      $source->rewind();
      $source_id_values = [];
      while ($source->valid()) {
        $image = $source->current()->getSourceProperty('image');
        if (empty($image)) {
          $source_id_values[] = $source->current()->getSourceProperty('item_id');
        }
        $source->next();
      }

      if (!empty($source_id_values)) {
        $media_migration_id = NewsroomServiceNewsroomProcessor::MIGRATION_SERVICE_IMAGE_MEDIA;
        $migration_to_rollback = [$media_migration_id];
        $migration_to_rollback = array_merge($migration_to_rollback, $this->migrationManager->getTranslationMigrationIds($media_migration_id));
        foreach ($migration_to_rollback as $migration_rollback_id) {
          $this->rollback($migration_rollback_id, $source_id_values);
        }
      }
    }
  }

  /**
   * Rollback missing images.
   *
   * @param string $migration_id
   *   Migration id.
   * @param array $source_id_values
   *   List of items with missing images.
   */
  protected function rollback($migration_id, array $source_id_values) {
    $migration = $this->migrationManager->getMigration($migration_id);
    if (empty($migration)) {
      return;
    }

    $id_map = $migration->getIdMap();
    $id_map->prepareUpdate();

    $id_map->rewind();
    $destination = $migration->getDestinationPlugin();

    while ($id_map->valid()) {
      $map_source_id = $id_map->currentSource();
      if (in_array($map_source_id['item_id'], $source_id_values, TRUE)) {
        $destination_ids = $id_map->currentDestination();
        $this->dispatchRowDeleteEvent(MigrateEvents::PRE_ROW_DELETE, $migration, $destination_ids);
        $this->dispatchRowDeleteEvent(MigratePlusEvents::MISSING_SOURCE_ITEM, $migration, $destination_ids);
        $destination->rollback($destination_ids);
        $this->dispatchRowDeleteEvent(MigrateEvents::POST_ROW_DELETE, $migration, $destination_ids);
        $id_map->delete($map_source_id);
      }
      $id_map->next();
    }
    $this->dispatcher->dispatch(MigrateEvents::POST_ROLLBACK, new MigrateRollbackEvent($migration));
  }

  /**
   * Dispatches MigrateRowDeleteEvent event.
   *
   * @param string $event_name
   *   The event name to dispatch.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The active migration.
   * @param array $destination_ids
   *   The destination identifier values of the record.
   */
  protected function dispatchRowDeleteEvent($event_name, MigrationInterface $migration, array $destination_ids) {
    // Symfony changing dispatcher so implementation could change.
    $this->dispatcher->dispatch($event_name, new MigrateRowDeleteEvent($migration, $destination_ids));
  }

}
