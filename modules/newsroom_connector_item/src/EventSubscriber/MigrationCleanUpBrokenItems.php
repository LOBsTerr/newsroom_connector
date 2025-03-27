<?php

namespace Drupal\newsroom_connector_item\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\newsroom_connector\MigrationManager;
use Drupal\newsroom_connector_item\Plugin\newsroom\NewsroomItemNewsroomProcessor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Clean up broken nodes.
 */
class MigrationCleanUpBrokenItems implements EventSubscriberInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Migration manager.
   *
   * @var \Drupal\newsroom_connector\MigrationManager
   */
  protected $migrationManager;

  /**
   * MigrationImportSync constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\newsroom_connector\MigrationManager $migration_manager
   *   Migration manager.
   */
  public function __construct(
    EventDispatcherInterface $dispatcher,
    EntityTypeManagerInterface $entity_type_manager,
    MigrationManager $migration_manager,
  ) {
    $this->dispatcher = $dispatcher;
    $this->entityTypeManager = $entity_type_manager;
    $this->migrationManager = $migration_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[MigrateEvents::PRE_IMPORT][] = ['clean'];
    return $events;
  }

  /**
   * Event callback to sync source and destination.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The migration import event.
   */
  public function clean(MigrateImportEvent $event) {
    // We want to get rid off newsroom items, which don't have mappings and we
    // can't track them, so for us they are broken.
    $migration = $event->getMigration();

    $entity_type = 'node';
    $bundle = 'newsroom_item';
    $bundle_field = 'type';

    if ($migration->id() == NewsroomItemNewsroomProcessor::MIGRATION_ITEM) {
      $source = clone $migration->getSourcePlugin();
      $source->rewind();

      while ($source->valid()) {
        $newsroom_id = $source->current()->getSourceProperty('item_id');
        if (!empty($newsroom_id)) {
          $destination_ids = $this->migrationManager->getDestinationIdsBySourceIds($migration, [$newsroom_id]);
          if (!empty($destination_ids)) {
            $nodes = $this->migrationManager->getEntitiesByNewsroomId($newsroom_id, $entity_type, $bundle, $bundle_field);
            foreach ($destination_ids as $destination) {
              foreach ($nodes as $node) {

                if ($node->id() != $destination['nid']) {
                  $node->delete();
                }
              }
            }
          }
        }

        $source->next();
      }
    }
  }

}
