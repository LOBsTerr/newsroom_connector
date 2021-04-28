<?php

namespace Drupal\newsroom_connector_newsletter_issue\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom newsletter issue.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_newsletter_issue",
 *   entity_type = "node",
 *   bundle = "newsroom_newsletter_issue",
 *   bundle_field = "type",
 *   import_script = "rss-issue-multilingual.cfm",
 *   import_segment = "",
 *   label = @Translation("Newsroom newsletter issue"),
 *   migrations = {
 *   }
 * )
 */
class NewsroomNewsletterIssueNewsroomProcessor extends NewsroomProcessorBase {

}
