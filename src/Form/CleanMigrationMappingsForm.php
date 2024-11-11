<?php

namespace Drupal\newsroom_connector\Form;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Clean Migration Mappings Form.
 *
 * @package Drupal\newsroom_connector\Form
 */
class CleanMigrationMappingsForm extends FormBase {

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager
   */
  protected $newsroomProcessorPluginManager;

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
    return 'newsroom_connector_clean_migration_mappings';
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(NewsroomProcessorManager $newsroom_processor_plugin_manager) {
    $this->newsroomProcessorPluginManager = $newsroom_processor_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('newsroom_connector.plugin.manager.newsroom_processor')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $plugin_id = NULL) {
    $plugin = $this->newsroomProcessorPluginManager->createInstance($plugin_id);
    if (!$plugin) {
      throw new PluginNotFoundException($plugin_id, 'Unable to find the plugin');
    }
    else {
      $this->plugin = $plugin;
    }

    $form = [];
    $form['source_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Source ID'),
      '#required' => TRUE,
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete mappings'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $source_id = $form_state->getValue('source_id');
    $this->plugin->deleteMappingsBySourceId($source_id);
  }

}
