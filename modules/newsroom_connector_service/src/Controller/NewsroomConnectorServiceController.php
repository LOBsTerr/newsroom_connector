<?php

namespace Drupal\newsroom_connector_service\Controller;

use Drupal\newsroom_connector\Controller\BaseNewsroomController;

/**
 * NewsroomConnectorServiceController class.
 */
class NewsroomConnectorServiceController extends BaseNewsroomController {

  /**
   * {@inheritdoc}
   */
  private function getPluginId() {
    return 'newsroom_service';
  }

}
