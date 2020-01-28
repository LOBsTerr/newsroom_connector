<?php

namespace Drupal\nexteuropa_newsroom_service\Plugin\newsroom;

use Drupal\nexteuropa_newsroom\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom service.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_service",
 *   content_type = "taxonomy_term",
 *   bundle = "newsroom_service",
 *   bundle_field = "vid",
 *   import_script = "rss-service-multilingual-v2.cfm",
 *   import_segment = "service_id",
 *   label = @Translation("Newsroom service")
 * )
 */
class NewsroomServiceNewsroomProcessor extends NewsroomProcessorBase {

}
