<?php

namespace Drupal\nexteuropa_newsroom_type\Plugin\newsroom;

use Drupal\nexteuropa_newsroom\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom type.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_type",
 *   content_type = "taxonomy_term",
 *   bundle = "newsroom_type",
 *   bundle_field = "vid",
 *   import_script = "rss-item-type-multilingual.cfm",
 *   import_segment = "item_type_id",
 *   label = @Translation("Newsroom type")
 * )
 */
class NewsroomTypeNewsroomProcessor extends NewsroomProcessorBase {

}
