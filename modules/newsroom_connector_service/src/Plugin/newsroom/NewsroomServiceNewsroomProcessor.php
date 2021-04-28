<?php

namespace Drupal\newsroom_connector_service\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom service.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_service",
 *   entity_type = "taxonomy_term",
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

  const MIGRATION_SERVICE = 'newsroom_service';
  const MIGRATION_SERVICE_IMAGE_MEDIA = 'newsroom_service_logo_media';
  const MIGRATION_SERVICE_IMAGE = 'newsroom_service_logo';

}
