<?php

namespace Drupal\nexteuropa_newsroom_type\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\migrate_plus\Plugin\MigrationConfigEntityPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\nexteuropa_newsroom\Plugin\NewsroomProcessorManager;

/**
 * NexteuropaNewsroomItemTypeController class.
 */
class NexteuropaNewsroomItemTypeController extends ControllerBase {

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManager
   */
  protected $migrationPluginManager;

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\nexteuropa_newsroom\Plugin\NewsroomProcessorManager
   */
  protected $newsroomProcessorPluginManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager'),
      $container->get('plugin.manager.migration'),
      $container->get('nexteuropa_newsroom.plugin.manager.newsroom_processor')
    );
  }

  /**
   * Constructs a MessageController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   A database connection.
   * @param \Drupal\migrate_plus\Plugin\MigrationConfigEntityPluginManager $migration_config_entity_plugin_manager
   *   The plugin manager for config entity-based migrations.
   */
  public function __construct(LanguageManagerInterface $languageManager, MigrationPluginManager $migrationPluginManager, NewsroomProcessorManager $newsroomProcessorPluginManager) {
    $this->languageManager = $languageManager;
    $this->migrationPluginManager = $migrationPluginManager;
    $this->newsroomProcessorPluginManager = $newsroomProcessorPluginManager;
  }

  /**
   * Gets an array of log level classes.
   *
   * @return array
   *   An array of log level classes.
   */
  public function import($newsroom_id) {
    $this->newsroomProcessorPluginManager->createInstance('newsroom_type')->import($newsroom_id);
    $this->redirectItem($newsroom_id);
  }

  public function redirectItem($newsroom_id) {
    $this->newsroomProcessorPluginManager->createInstance('newsroom_type')->redirect($newsroom_id);
  }

}
