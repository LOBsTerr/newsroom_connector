<?php

namespace Drupal\newsroom_connector\Form;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Error;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorManager;
use Drupal\newsroom_connector\UniverseManager;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The cache backend service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

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
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    ClientInterface $http_client,
    UniverseManager $universe_manager,
    NewsroomProcessorManager $processor_plugin_manager,
    LoggerInterface $logger,
    CacheBackendInterface $cache_backend,
  ) {
    parent::__construct($config_factory);
    $this->httpClient = $http_client;
    $this->universeManager = $universe_manager;
    $this->newsroomProcessorPluginManager = $processor_plugin_manager;
    $this->logger = $logger;
    $this->cacheBackend = $cache_backend;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('http_client'),
      $container->get('newsroom_connector.universe_manager'),
      $container->get('newsroom_connector.plugin.manager.newsroom_processor'),
      $container->get('logger.channel.newsroom_connector'),
      $container->get('newsroom_connector.cache'),
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
    $form['universe_settings']['general']['debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Debug'),
      '#default_value' => $config->get('debug') ?? 0,
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
    $form['universe_settings']['general']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key:'),
      '#default_value' => $config->get('api_key'),
      '#description' => $this->t('SHA256 hash.'),
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

    $form['actions']['clean_cache'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clean cache'),
      '#button_type' => 'primary',
      '#submit' => ['::cleanCache'],
    ];

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
      ->set('debug', $values['debug'])
      ->set('import_disabled', $values['import_disabled'])
      ->set('base_url', $values['base_url'])
      ->set('api_key', $values['api_key'])
      ->set('subsite', $values['subsite']);

    $plugins = $this->newsroomProcessorPluginManager->getDefinitions();
    foreach (array_keys($plugins) as $plugin_id) {
      $cron_import_setting_name = $this->getCronImortSettingName($plugin_id);
      $settings->set($cron_import_setting_name, $values[$cron_import_setting_name]);
    }

    $settings->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $universe_id = $form_state->getValue('universe_id');
    $base_url = $form_state->getValue('base_url');

    if (!$this->validateUniverseId($base_url, $universe_id)) {
      $form_state->setErrorByName('universe_id', $this->t('Wrong newsroom universe ID.'));
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
      $url = $this->buildUrl($base_url, $universe_id, 'newsletter-archives/view');
      $response = $this->httpClient->get($url);
      $result = $response->getStatusCode() == 200;
    }
    catch (RequestException $exception) {
      Error::logException($this->logger, $exception);
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
   * @param string $paramters
   *   URL parameters.
   *
   * @return string
   *   URL string.
   */
  private function buildUrl($base_part, $universe_id, $paramters) {
    return "{$base_part}{$universe_id}/{$paramters}";
  }

  /**
   * Submission handler to clean the cache.
   */
  public function cleanCache() {
    $this->cacheBackend->deleteAll();
    $this->messenger()->addMessage($this->t('Cache has been cleaned.'));
  }

}
