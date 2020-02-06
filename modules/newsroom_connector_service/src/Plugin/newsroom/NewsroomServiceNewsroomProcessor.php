<?php

namespace Drupal\newsroom_connector_service\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

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
 *   label = @Translation("Newsroom service"),
 *   migrations = {
 *     "newsroom_service_logo",
 *     "newsroom_service_logo_media",
 *     "newsroom_service",
 *   }
 * )
 */
class NewsroomServiceNewsroomProcessor extends NewsroomProcessorBase {

}
