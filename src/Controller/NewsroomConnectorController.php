<?php

namespace Drupal\newsroom_connector\Controller;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * NewsroomConnectorTypeController class.
 */
class NewsroomConnectorController extends ControllerBase {

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
   * @var \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager
   */
  protected $newsroomProcessorPluginManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager'),
      $container->get('plugin.manager.migration'),
      $container->get('newsroom_connector.plugin.manager.newsroom_processor')
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
   * Import item.
   */
  public function import($type, $newsroom_id) {
    // Convert type to proper plugin id.
    $plugin_id = "newsroom_$type";

    $plugin = $this->newsroomProcessorPluginManager->createInstance($plugin_id);
    if ($plugin) {
      $plugin->import($newsroom_id);
      $plugin->redirect($newsroom_id);
    }
    else {
      throw new PluginNotFoundException($plugin_id, 'Unable to find the plugin');
    }
  }

  /**
   * Old redirection.
   */
  public function newsRedirect($newsroom_id) {
    $this->redirect('item', $newsroom_id);
  }

  /**
   * Redirects item to local entity.
   *
   * @param $type
   * @param $newsroom_id
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function redirectItem($type, $newsroom_id) {
    $plugin_id = "newsroom_$type";
    $plugin = $this->newsroomProcessorPluginManager->createInstance($plugin_id);
    if ($plugin) {
      $plugin->redirect($newsroom_id);
    }
    else {
      throw new PluginNotFoundException($plugin_id, 'Unable to find the plugin');
    }
  }

  /**
   * Provides the list of importers.
   *
   * @return array
   *   Renderable array with list of importers.
   */
  public function importers() {
    $plugins = $this->newsroomProcessorPluginManager->getDefinitions();
    $data = [];
    foreach ($plugins as $plugin_id => $plugin) {
      $data[] = [Link::fromTextAndUrl($plugin['label']->render(), Url::fromRoute('newsroom_connector.import_form', ['plugin_id' => $plugin_id]))];
    }

    $table = [
      '#theme' => 'table',
      '#rows' => $data,
      '#header' => [
        'Name'
      ],
      '#empty' => $this->t('No importers')
    ];

    return $table;
  }

}
