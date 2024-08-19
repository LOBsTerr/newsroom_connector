<?php

namespace Drupal\newsroom_connector_country\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom country.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_country",
 *   entity_type = "taxonomy_term",
 *   bundle = "newsroom_country",
 *   bundle_field = "vid",
 *   import_script = "feed/topics",
 *   import_segment = "country_id",
 *   label = @Translation("Newsroom country"),
 *   migrations = {
 *     "newsroom_country",
 *   }
 * )
 */
class NewsroomCountryNewsroomProcessor extends NewsroomProcessorBase {

  const MIGRATION_COUNTRY = 'newsroom_country';

}
