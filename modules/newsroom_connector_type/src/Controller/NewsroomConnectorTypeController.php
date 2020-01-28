<?php

namespace Drupal\newsroom_connector_type\Controller;

use Drupal\newsroom_connector\Controller\BaseNewsroomController;

/**
 * NewsroomConnectorTypeController class.
 */
class NewsroomConnectorTypeController extends BaseNewsroomController {

  /**
   * {@inheritdoc}
   */
  private function getPluginId() {
    return 'newsroom_type';
  }

}
