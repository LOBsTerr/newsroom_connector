<?php

namespace Drupal\nexteuropa_newsroom_topic\Plugin\newsroom;

use Drupal\nexteuropa_newsroom\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom topic.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_topic",
 *   content_type = "taxonomy_term",
 *   bundle = "newsroom_topic",
 *   bundle_field = "vid",
 *   import_script = "rss-service-multilingual.cfm",
 *   import_segment = "topic_id",
 *   label = @Translation("Newsroom topic")
 * )
 */
class NewsroomTopicNewsroomProcessor extends NewsroomProcessorBase {

}
