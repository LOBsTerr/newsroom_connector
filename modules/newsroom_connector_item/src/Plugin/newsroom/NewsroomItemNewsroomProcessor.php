<?php

namespace Drupal\newsroom_connector_item\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom item.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_item",
 *   entity_type = "node",
 *   bundle = "newsroom_item",
 *   bundle_field = "type",
 *   import_script = "feed/full",
 *   import_segment = "item_id",
 *   label = @Translation("Newsroom item"),
 *   migrations = {
 *     "newsroom_item_image",
 *     "newsroom_item_image_media",
 *     "newsroom_item",
 *   }
 * )
 */
class NewsroomItemNewsroomProcessor extends NewsroomProcessorBase {

  // Newsroom item importers.
  const MIGRATION_ITEM = 'newsroom_item';
  const MIGRATION_ITEM_IMAGE_MEDIA = 'newsroom_item_image_media';
  const MIGRATION_ITEM_IMAGE = 'newsroom_item_image';
}
