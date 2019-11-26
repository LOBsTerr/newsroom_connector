<?php

namespace Drupal\nexteuropa_newsroom\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

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
    parent::__construct('Plugin/newsroom', $namespaces, $module_handler, 'Drupal\nexteuropa_newsroom\Plugin\NewsroomProcessorInterface', 'Drupal\nexteuropa_newsroom\Annotation\NewsroomProcessor');

    $this->alterInfo('nexteuropa_newsroom_newsroom_processor_info');
    $this->setCacheBackend($cache_backend, 'nexteuropa_newsroom_newsroom_processor_plugins');
  }

}
