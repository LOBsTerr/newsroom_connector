<?php

namespace Drupal\newsroom_connector_topic\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom topic.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_topic",
 *   entity_type = "taxonomy_term",
 *   bundle = "newsroom_topic",
 *   bundle_field = "vid",
 *   import_script = "feed/topics",
 *   import_segment = "topic_id",
 *   label = @Translation("Newsroom topic"),
 *   migrations = {
 *     "newsroom_topic",
 *   }
 * )
 */
class NewsroomTopicNewsroomProcessor extends NewsroomProcessorBase {

  const MIGRATION_TOPIC = 'newsroom_topic';

}
