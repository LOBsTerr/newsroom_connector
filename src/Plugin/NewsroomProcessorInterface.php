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
   * Redirect.
   */
  public function redirect($newsroom_id);

  /**
   * Import.
   */
  public function import($newsroom_id = NULL);

  /**
   * Import from URL.
   */
  public function runImport(Url $url);

  /**
   * Get Newsroom entity URL.
   */
  public function getEntityUrl($newsroom_id = NULL);

}
