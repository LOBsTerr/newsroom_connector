<?php

namespace Drupal\newsroom_connector\Form;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorManager;
use Drupal\newsroom_connector\UniverseManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Import Form.
 *
 * @package Drupal\newsroom_connector\Form
 */
class ImportForm extends FormBase {

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
   * Process newsroom plugin instance.
   *
   * @var Drupal\newsroom_connector\Plugin\NewsroomProcessorInterface
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'newsroom_connector_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(NewsroomProcessorManager $newsroom_processor_plugin_manager, UniverseManagerInterface $universe_manager) {
    $this->newsroomProcessorPluginManager = $newsroom_processor_plugin_manager;
    $this->universeManager = $universe_manager;
  }

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
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $plugin_id = NULL) {
    $import_disabled = $this->universeManager->getConfig()->get('import_disabled');
    if ($import_disabled) {
      $form['message'] = [
        '#type' => 'markup',
        '#markup' => 'Import is disabled',
      ];
    }
    else {
      $plugin = $this->newsroomProcessorPluginManager->createInstance($plugin_id);
      if (!$plugin) {
        throw new PluginNotFoundException($plugin_id, 'Unable to find the plugin');
      }
      else {
        $this->plugin = $plugin;
      }

      $form = [];
      $form['url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#default_value' => $plugin->getEntityUrl()->toUriString(),
        '#required' => TRUE,
      ];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Import'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $url = Url::fromUri($form_state->getValue('url'));
    $this->plugin->runImport($url);
  }

}
