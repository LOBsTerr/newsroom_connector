<?php

namespace Drupal\nexteuropa_newsroom_item_type\Plugin\newsroom;

use Drupal\fut_activity\Plugin\ActivityProcessorBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Drupal\nexteuropa_newsroom\Plugin\NewsroomProcessorBase;
use Symfony\Component\EventDispatcher\Event;
use Drupal\fut_activity\ActivityRecord;
use Drupal\fut_activity\Event\TrackerCreateEvent;
use Drupal\fut_activity\Plugin\ActivityProcessorInterface;
use Drupal\fut_activity\ActivityRecordStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\fut_activity\Event\TrackerDeleteEvent;
use Drupal\fut_activity\Entity\EntityActivityTrackerInterface;

/**
 * Sets activity when entity is created.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_item_type",
 *   content_type = "taxonomy_term",
 *   bundle = "newsroom_item_type",
 *   bundle_field = "vid",
 *   import_script = "rss-item-type-multilingual.cfm",
 *   import_segment = "item_type_id",
 *   label = @Translation("Entity Create")
 * )
 */
class NewsroomItemTypeNewsroomProcessor extends NewsroomProcessorBase {

}
