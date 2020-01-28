<?php

namespace Drupal\newsroom_connector_topic\Controller;

use Drupal\newsroom_connector\Controller\BaseNewsroomController;

/**
 * NewsroomConnectorTopicController class.
 */
class NewsroomConnectorTopicController extends BaseNewsroomController {

  /**
   * {@inheritdoc}
   */
  private function getPluginId() {
    return 'newsroom_topic';
  }

}
