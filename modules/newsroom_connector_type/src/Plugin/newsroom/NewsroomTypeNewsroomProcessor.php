<?php

namespace Drupal\newsroom_connector_type\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom type.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_type",
 *   entity_type = "taxonomy_term",
 *   bundle = "newsroom_type",
 *   bundle_field = "vid",
 *   import_script = "rss-item-type-multilingual.cfm",
 *   import_segment = "item_type_id",
 *   label = @Translation("Newsroom type"),
 *   migrations = {
 *     "newsroom_type",
 *   }
 * )
 */
class NewsroomTypeNewsroomProcessor extends NewsroomProcessorBase {

  // Newsroom type importer.
  const MIGRATION_TYPE = 'newsroom_type';
}
