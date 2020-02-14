<?php

namespace Drupal\newsroom_connector_newsletter\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom newsletter.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_newsletter",
 *   content_type = "taxonomy_term",
 *   bundle = "newsroom_newsletter",
 *   bundle_field = "vid",
 *   import_script = "rss-newsletter-multilingual-v2.cfm",
 *   import_segment = "newsletter_id",
 *   label = @Translation("Newsroom newsletter"),
 *   migrations = {
 *     "newsroom_newsletter",
 *   }
 * )
 */
class NewsroomNewsletterNewsroomProcessor extends NewsroomProcessorBase {

}
