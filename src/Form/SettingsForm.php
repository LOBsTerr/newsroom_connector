<?php

namespace Drupal\newsroom_connector\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\newsroom_connector\UniverseManager;

class SettingsForm extends ConfigFormBase {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The Guzzle HTTP client.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ClientInterface $http_client, UniverseManager $universeManager) {
    parent::__construct($config_factory);
    $this->httpClient = $http_client;
    $this->universeManager = $universeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('http_client'),
      $container->get('newsroom_connector.universe_manager')
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
    $form['universe_settings']['general']['allowed_ips'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IP addresses allowed for import:'),
      '#default_value' => $config->get('allowed_ips'),
      '#description' => $this->t('Comma separated list of IP addresses where the importer can be launched from.'),
    ];

    $form['universe_settings']['general']['item_edit_script'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL chunk to edit an item in the Newsroom:'),
      '#default_value' => $config->get('item_edit_script'),
      '#required' => TRUE,
    ];
    $form['universe_settings']['general']['proposal_script'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Newsroom proposal script:'),
      '#default_value' => $config->get('proposal_script'),
    ];
    $form['universe_settings']['general']['docsroom_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Newsroom Docsroom URL:'),
      '#default_value' => $config->get('docsroom_url'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('newsroom_connector.settings')
      ->set('universe_id', $values['universe_id'])
      ->set('base_url', $values['base_url'])
      ->set('allowed_ips', $values['allowed_ips'])
      ->set('app', $values['app'])
      ->set('app_key', $values['app_key'])
      ->set('subsite', $values['subsite'])
      ->set('item_edit_segment', $values['item_edit_segment'])
      ->set('proposal_script', $values['proposal_script'])
      ->set('docsroom_url', $values['docsroom_url'])
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $universe_id = $form_state->getValue('universe_id');
    $base_url = $form_state->getValue('base_url');
    $subsite = $form_state->getValue('subsite');
    if (!$this->validateUniverseId($base_url, $universe_id)) {
      $form_state->setErrorByName('universe_id', $this->t('Wrong newsroom universe ID.'));
    }
    if (!$this->validateSubsite($base_url, $universe_id, $subsite)) {
      $form_state->setErrorByName('subsite', $this->t('Wrong subsite.'));
    }
  }

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

  private function buildUrl($base_part, $universe_id, $paramters) {
    return "{$base_part}{$universe_id}/{$paramters}";
  }

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