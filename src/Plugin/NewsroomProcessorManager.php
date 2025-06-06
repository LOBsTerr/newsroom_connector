<?php

namespace Drupal\newsroom_connector\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides the Newsroom processor plugin manager.
 */
class NewsroomProcessorManager extends DefaultPluginManager {

  /**
   * Constructs a new NewsroomProcessorManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/newsroom', $namespaces, $module_handler, 'Drupal\newsroom_connector\Plugin\NewsroomProcessorInterface', 'Drupal\newsroom_connector\Annotation\NewsroomProcessor');

    $this->alterInfo('newsroom_connector_newsroom_processor_info');
    $this->setCacheBackend($cache_backend, 'newsroom_connector_newsroom_processor_plugins');
  }

}
