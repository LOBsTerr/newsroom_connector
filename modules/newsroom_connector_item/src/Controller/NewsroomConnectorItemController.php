<?php

namespace Drupal\newsroom_connector_item\Controller;

use Drupal\newsroom_connector\Controller\BaseNewsroomController;

/**
 * NewsroomConnectorItemController class.
 */
class NewsroomConnectorItemController extends BaseNewsroomController {

  /**
   * {@inheritdoc}
   */
  private function getPluginId() {
    return 'newsroom_item';
  }

}
