<?php

namespace Drupal\newsroom_connector\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;

/**
 * Defines an interface for Activity processor plugins.
 */
interface NewsroomProcessorInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Redirect to item by original Newsroom ID.
   */
  public function redirect($newsroom_id);

  /**
   * Import.
   */
  public function import($newsroom_id, $use_batch = TRUE);

  /**
   * Import from URL.
   */
  public function runImport(Url $url);

  /**
   * Get Newsroom entity URL.
   */
  public function getEntityUrl($newsroom_id);

}
