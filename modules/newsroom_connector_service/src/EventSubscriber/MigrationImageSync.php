<?php

namespace Drupal\newsroom_connector_service\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
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
    // We want to be sure, that we don't have duplication of medias/images for
    // newsroom item, before the import we do rollback for media importers,
    // which will also remove images.
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
          $this->migrationManager->rollback($migration_rollback_id, $source_id_values);
        }
      }
    }
  }

}
