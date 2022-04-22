<?php

namespace Drupal\newsroom_connector_item\EventSubscriber;

use Drupal\entity_data\EntityData;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\newsroom_connector_item\Plugin\newsroom\NewsroomItemNewsroomProcessor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handle newsroom item imports.
 */
class NewsroomItemMigrateSubscriber implements EventSubscriberInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * EntityData service.
   *
   * @var \Drupal\entity_data\EntityData
   */
  protected $entityData;

  /**
   * EntityData service.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   * @param \Drupal\entity_data\EntityData $entity_data
   *   The entity data service.
   */
  public function __construct(EventDispatcherInterface $dispatcher, EntityData $entity_data) {
    $this->dispatcher = $dispatcher;
    $this->entityData = $entity_data;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[MigrateEvents::POST_ROW_SAVE][] = ['updateDocuments'];
    return $events;
  }

  /**
   * Event callback to sync source and destination.
   *
   * @param \Drupal\migrate\Event\MigratePostRowSaveEvent $event
   *   The migration import event.
   */
  public function updateDocuments(MigratePostRowSaveEvent $event) {
    $migration_id = $event->getMigration()->id();
    if (strpos($migration_id, 'newsroom_item_translations') !== FALSE || $migration_id == NewsroomItemNewsroomProcessor::MIGRATION_ITEM) {
      $nid = $event->getDestinationIdValues()[0] ?? NULL;
      if (empty($nid)) {
        return;
      }
      $row = $event->getRow();
      $related_documents_urls = $row->getSourceProperty('related_documents_url');
      $related_documents_machine_translations = $row->getSourceProperty('related_documents_machine_translation');

      if (!empty($related_documents_urls)) {
        if (is_array($related_documents_urls)) {
          foreach ($related_documents_urls as $key => $item) {
            $this->entityData->set('newsroom_connector_item', $nid, $related_documents_urls[$key], 'node', $related_documents_machine_translations[$key] == 'True' ? 1 : 0);
          }
        }
        else {
          $this->entityData->set('newsroom_connector_item', $nid, $related_documents_urls, 'node', $related_documents_machine_translations == 'True' ? 1 : 0);
        }
      }
    }
  }

}
