<?php

namespace Drupal\newsroom_connector\Controller;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorManager;
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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('newsroom_connector.plugin.manager.newsroom_processor')
    );
  }

  /**
   * Constructs a Newsroom Connector object.
   *
   * @param \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager $newsroom_processor_plugin_manager
   *   Newsroom process plugin manager.
   */
  public function __construct(NewsroomProcessorManager $newsroom_processor_plugin_manager) {
    $this->newsroomProcessorPluginManager = $newsroom_processor_plugin_manager;
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
   *
   * @return mixed
   *   Response object.
   */
  public function newsRedirect($newsroom_id) {
    return $this->redirectItem('item', $newsroom_id);
  }

  /**
   * Redirects item to local entity.
   *
   * @param string $type
   *   Type of the content.
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return mixed
   *   Response object.
   */
  public function redirectItem($type, $newsroom_id) {
    $plugin_id = "newsroom_$type";
    $plugin = $this->newsroomProcessorPluginManager->createInstance($plugin_id);
    if ($plugin) {
      return $plugin->redirect($newsroom_id);
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
      $url = Url::fromRoute('newsroom_connector.import_form', ['plugin_id' => $plugin_id]);
      $data[] = [
        Link::fromTextAndUrl($plugin['label']->render(), $url),
      ];
    }

    $table = [
      '#theme' => 'table',
      '#rows' => $data,
      '#header' => [
        'Name',
      ],
      '#empty' => $this->t('No importers'),
    ];

    return $table;
  }

}
