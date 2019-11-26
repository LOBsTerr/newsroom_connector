<?php

namespace Drupal\nexteuropa_newsroom\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\EventDispatcher\Event;

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
   * Get Newsroom entity URL.
   */
  public function getEntityUrl($newsroom_id = NULL);

}
