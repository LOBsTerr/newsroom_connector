<?php

namespace Drupal\newsroom_connector\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorManager;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\newsroom_connector\UniverseManager;

/**
 * Class Settings Form.
 *
 * @package Drupal\newsroom_connector\Form
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Processor plugin manager service.
   *
   * @var \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager
   */
  protected $newsroomProcessorPluginManager;

  /**
   * Universe manager.
   *
   * @var \Drupal\newsroom_connector\UniverseManager
   */
  protected $universeManager;

  /**
   * Settings form.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The Guzzle HTTP client.
   * @param \Drupal\newsroom_connector\UniverseManager $universe_manager
   *   Universe manager.
   * @param \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager $processor_plugin_manager
   *   Processor plugin manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ClientInterface $http_client, UniverseManager $universe_manager, NewsroomProcessorManager $processor_plugin_manager) {
    parent::__construct($config_factory);
    $this->httpClient = $http_client;
    $this->universeManager = $universe_manager;
    $this->newsroomProcessorPluginManager = $processor_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('http_client'),
      $container->get('newsroom_connector.universe_manager'),
      $container->get('newsroom_connector.plugin.manager.newsroom_processor')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'newsroom_connector.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'newsroom_connector_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('newsroom_connector.settings');

    $form['universe_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Universe settings'),
    ];
    $form['universe_settings']['general'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General settings'),
    ];
    $form['universe_settings']['general']['universe_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Universe ID:'),
      '#default_value' => $config->get('universe_id'),
      '#description' => $this->t('Universe ID.'),
      '#required' => TRUE,
      '#disabled' => !empty($this->universeManager->getUniverseId()),
    ];
    $form['universe_settings']['general']['import_disabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable import'),
      '#default_value' => $config->get('import_disabled') ?? 0,
    ];
    $form['universe_settings']['general']['base_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base newsroom URL:'),
      '#default_value' => $config->get('base_url'),
      '#description' => $this->t('Base newsroom URL.'),
      '#required' => TRUE,
    ];
    $form['universe_settings']['general']['subsite'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of the subsite:'),
      '#default_value' => $config->get('subsite'),
      '#description' => $this->t('The value you enter here will be used to filter the items belonging to this website.'),
    ];
    $form['universe_settings']['general']['app'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Application:'),
      '#default_value' => $config->get('app'),
      '#description' => $this->t('Application name.'),
    ];
    $form['universe_settings']['general']['app_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Application key:'),
      '#default_value' => $config->get('app_key'),
      '#description' => $this->t('Application key (hash sha256).'),
    ];
    $form['universe_settings']['general']['docsroom_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Newsroom Docsroom URL:'),
      '#default_value' => $config->get('docsroom_url'),
    ];

    $plugins = $this->newsroomProcessorPluginManager->getDefinitions();

    if (!empty($plugins)) {
      $form['importers_settings'] = [
        '#type' => 'details',
        '#title' => $this->t('Importer settings'),
        '#open' => FALSE,
      ];
      foreach ($plugins as $plugin_id => $plugin) {
        $form['importers_settings']["importer_setting_{$plugin_id}"] = [
          '#type' => 'fieldset',
          '#title' => $this->t('%plugin settings', [
            '%plugin' => $plugin['label']->render(),
          ]),
        ];
        $cron_import_setting_name = $this->getCronImortSettingName($plugin_id);
        $form['importers_settings']["importer_setting_{$plugin_id}"][$cron_import_setting_name] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Import on cron'),
          '#default_value' => $config->get($cron_import_setting_name) ?? 0,
        ];
      }
    }

    return $form;
  }

  /**
   * Get cron import setting name.
   *
   * @param string $plugin_id
   *   Plugin id.
   *
   * @return string
   *   Setting name.
   */
  protected function getCronImortSettingName($plugin_id) {
    return "cron_import_{$plugin_id}";
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $settings = $this->config('newsroom_connector.settings')
      ->set('universe_id', $values['universe_id'])
      ->set('import_disabled', $values['import_disabled'])
      ->set('base_url', $values['base_url'])
      ->set('app', $values['app'])
      ->set('app_key', $values['app_key'])
      ->set('subsite', $values['subsite'])
      ->set('item_edit_segment', $values['item_edit_segment'])
      ->set('item_edit_script', $values['item_edit_script'])
      ->set('proposal_script', $values['proposal_script'])
      ->set('docsroom_url', $values['docsroom_url']);

    $plugins = $this->newsroomProcessorPluginManager->getDefinitions();
    foreach ($plugins as $plugin_id => $plugin) {
      $cron_import_setting_name = $this->getCronImortSettingName($plugin_id);
      $settings->set($cron_import_setting_name, $values[$cron_import_setting_name]);
    }

    $settings->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function noValidateForm(array &$form, FormStateInterface $form_state) {
    $universe_id = $form_state->getValue('universe_id');
    $base_url = $form_state->getValue('base_url');

    // @TODO Find another way to validate universe id.
    if (!$this->validateUniverseId($base_url, $universe_id)) {
      $form_state->setErrorByName('universe_id', $this->t('Wrong newsroom universe ID.'));
    }

    // @TODO Bring it back, when it is done on the newsroom
    $subsite = $form_state->getValue('subsite');
    if (!$this->validateSubsite($base_url, $universe_id, $subsite)) {
      $form_state->setErrorByName('subsite', $this->t('Wrong subsite.'));
    }
  }

  /**
   * Validates universe id.
   *
   * @param string $base_url
   *   Base part of URL.
   * @param string $universe_id
   *   Universe id.
   *
   * @return bool
   *   Result.
   */
  private function validateUniverseId($base_url, $universe_id) {
    $result = FALSE;

    try {
      $url = $this->buildUrl($base_url, $universe_id, 'logout.cfm');
      $response = $this->httpClient->get($url);
      $result = $response->getStatusCode() == 200;
    }
    catch (RequestException $exception) {
      watchdog_exception('newsroom_connector', $exception);
    }

    return $result;
  }

  /**
   * Builds universe URL.
   *
   * @param string $base_part
   *   Base part of URL.
   * @param string $universe_id
   *   Universe id.
   * @param array $paramters
   *   URL parameters.
   *
   * @return string
   *   URL string.
   */
  private function buildUrl($base_part, $universe_id, array $paramters) {
    // @TODO: Use universe manager instead.
    return "{$base_part}{$universe_id}/{$paramters}";
  }

  /**
   * Validates subsite.
   *
   * @param string $base_url
   *   Base part of URL.
   * @param string $universe_id
   *   Universe id.
   * @param string $subsite
   *   Subsite.
   *
   * @return bool
   *   Result
   */
  private function validateSubsite($base_url, $universe_id, $subsite) {
    $result = TRUE;
    // The subsite is not mandatory.
    if (!empty($subsite)) {
      try {
        $url = $this->buildUrl($base_url, $universe_id, "validation.cfm?subsite=$subsite");
        $response = $this->httpClient->get($url, ['headers' => ['Accept' => 'text/plain']]);
        $body = trim((string) $response->getBody());
        $result = $body == 'True' ? TRUE : FALSE;
      }
      catch (RequestException $exception) {
        watchdog_exception('newsroom_connector', $exception);
      }
    }

    return $result;
  }

}
