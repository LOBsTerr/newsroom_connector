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
   *
   * @param string $newsroom_id
   *   Newsroom ID.
   * @param string $language
   *   Language.
   */
  public function redirect($newsroom_id, $language = 'en');

  /**
   * Run import.
   *
   * @param string $newsroom_id
   *   Newsroom ID.
   * @param bool $use_batch
   *   Use batch mode.
   */
  public function import($newsroom_id, $use_batch = TRUE);

  /**
   * Import from URL.
   *
   * @param string $url
   *   Url.
   */
  public function runImport(Url $url);

  /**
   * Get Newsroom entity URL.
   *
   * @param string $newsroom_id
   *   Newsroom ID.
   */
  public function getEntityUrl($newsroom_id);

  /**
   * Clean up mapping records by source id.
   *
   * @param string $newsroom_id
   *   Newsroom ID.
   */
  public function deleteMappingsBySourceId($newsroom_id);

}
