<?php

namespace Drupal\newsroom_connector\Controller;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorManager;
use Drupal\newsroom_connector\UniverseManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Newsroom Connector Controller class.
 */
class NewsroomConnectorController extends ControllerBase {

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager
   */
  protected $newsroomProcessorPluginManager;

  /**
   * The universe manager.
   *
   * @var \Drupal\newsroom_connector\UniverseManager
   */
  protected $universeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('newsroom_connector.plugin.manager.newsroom_processor'),
      $container->get('newsroom_connector.universe_manager')
    );
  }

  /**
   * Constructs a Newsroom Connector object.
   *
   * @param \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager $newsroom_processor_plugin_manager
   *   Newsroom process plugin manager.
   * @param \Drupal\newsroom_connector\UniverseManagerInterface $universe_manager
   *   The universe manager.
   */
  public function __construct(NewsroomProcessorManager $newsroom_processor_plugin_manager, UniverseManagerInterface $universe_manager) {
    $this->newsroomProcessorPluginManager = $newsroom_processor_plugin_manager;
    $this->universeManager = $universe_manager;
  }

  /**
   * Import item.
   *
   * @param string $type
   *   Content type.
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return mixed
   *   Response object.
   */
  public function import($type, $newsroom_id) {
    // Convert type to proper plugin id.
    $plugin_id = "newsroom_$type";

    $plugin = $this->newsroomProcessorPluginManager->createInstance($plugin_id);
    if ($plugin) {
      $plugin->import($newsroom_id, FALSE);

      return $plugin->redirect($newsroom_id);
    }
    else {
      throw new PluginNotFoundException($plugin_id, 'Unable to find the plugin');
    }
  }

  /**
   * Redirect item by original newsroom id.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   * @param string $language_id
   *   Language id .
   *
   * @return mixed
   *   Response object.
   */
  public function newsRedirect($newsroom_id, $language_id = 'en') {
    return $this->redirectItem('item', $newsroom_id, $language_id);
  }

  /**
   * Redirects item to local entity.
   *
   * @param string $type
   *   Type of the content.
   * @param int $newsroom_id
   *   Original newsroom id.
   * @param string $language_id
   *   Language id.
   *
   * @return mixed
   *   Response object.
   */
  public function redirectItem($type, $newsroom_id, $language_id = 'en') {
    $plugin_id = "newsroom_$type";
    $plugin = $this->newsroomProcessorPluginManager->createInstance($plugin_id);
    if ($plugin) {
      return $plugin->redirect($newsroom_id, $language_id);
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
    $empty_message = $this->t('No importers');
    $import_disabled = $this->universeManager->getConfig()->get('import_disabled');
    if ($import_disabled) {
      $empty_message = $this->t('Import is disabled');
    }
    else {
      foreach ($plugins as $plugin_id => $plugin) {
        $import_url = Url::fromRoute('newsroom_connector.import_form', ['plugin_id' => $plugin_id]);
        $clean_url = Url::fromRoute('newsroom_connector.clean_mappings', ['plugin_id' => $plugin_id]);
        $data[] = [
          Link::fromTextAndUrl($plugin['label']->render(), $import_url),
          Link::fromTextAndUrl('Clean', $clean_url),
        ];
      }
    }

    $table = [
      '#theme' => 'table',
      '#rows' => $data,
      '#header' => [
        'Name',
        'Clean mappings'
      ],
      '#empty' => $empty_message,
    ];

    return $table;
  }

}
