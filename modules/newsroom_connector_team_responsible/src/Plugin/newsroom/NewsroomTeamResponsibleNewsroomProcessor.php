<?php

namespace Drupal\newsroom_connector_team_responsible\Plugin\newsroom;

use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;

/**
 * Handles typical operations for newsroom team responsible.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_team_responsible",
 *   content_type = "taxonomy_term",
 *   bundle = "newsroom_team_responsible",
 *   bundle_field = "vid",
 *   import_script = "rss-topic-multilingual-v2.cfm",
 *   import_segment = "topic_id",
 *   label = @Translation("Newsroom team responsible"),
 *   migrations = {
 *     "team_responsible",
 *   }
 * )
 */
class NewsroomTeamResponsibleNewsroomProcessor extends NewsroomProcessorBase {

  const MIGRATION_TEAM_RESPONSIBLE = 'team_responsible';
}
