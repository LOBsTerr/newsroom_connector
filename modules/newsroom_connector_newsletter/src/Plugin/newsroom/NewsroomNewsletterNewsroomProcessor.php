<?php

namespace Drupal\newsroom_connector_newsletter\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom newsletter.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_newsletter",
 *   entity_type = "taxonomy_term",
 *   bundle = "newsroom_newsletter",
 *   bundle_field = "vid",
 *   import_script = "feed/newsletters",
 *   import_segment = "newsletter_id",
 *   label = @Translation("Newsroom newsletter"),
 *   migrations = {
 *     "newsroom_newsletter",
 *   }
 * )
 */
class NewsroomNewsletterNewsroomProcessor extends NewsroomProcessorBase {

}
