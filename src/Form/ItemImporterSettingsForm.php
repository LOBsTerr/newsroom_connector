<?php

namespace Drupal\newsroom_connector\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\newsroom_connector\Helper\UniverseHelper;

class ItemImporterSettingsForm extends ConfigFormBase {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $entityFieldManager;

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityFieldManagerInterface $entity_field_manager) {
    parent::__construct($config_factory);
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'newsroom_connector.item_import_settings',
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
    $fields = $this->entityFieldManager->getFieldDefinitions('node', 'newsroom_item');

    foreach ($fields as $field_key => $field) {
      $field_config_key = 'xpath_' . $field_key;
      $form[$field_config_key] = [
        '#type' => 'textfield',
        '#title' => $field->getLabel(),
        '#default_value' => $config->get($field_config_key),
        '#description' => $this->t('Xpath for @label', ['@label' => $field->getLabel()]),
        '#required' => TRUE,
      ];
    }

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
      ->set('item_import_script', $values['item_import_script'])
      ->set('item_import_segment', $values['item_import_segment'])
      ->set('topic_import_script', $values['topic_import_script'])
      ->set('topic_import_segment', $values['topic_import_segment'])
      ->set('type_import_script', $values['type_import_script'])
      ->set('type_import_segment', $values['type_import_segment'])
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
    return $base_part . $universe_id . '/' . $paramters;
  }

  private function validateSubsite($base_url, $universe_id, $subsite) {
    $result = TRUE;
    // The subsite is not mandatory.
    if (!empty($subsite)) {
      try {
        $url = $this->buildUrl($base_url, $universe_id, 'validation.cfm?subsite=' . $subsite);
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