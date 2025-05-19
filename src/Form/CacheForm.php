<?php

namespace Drupal\newsroom_connector\Form;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Utility\Error;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Cache Form.
 *
 * @package Drupal\newsroom_connector\Form
 */
class CacheForm extends FormBase {

  /**
   * The cache backend service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Cache form.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend service.
   */
  public function __construct(
    CacheBackendInterface $cache_backend,
  ) {
    $this->cacheBackend = $cache_backend;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('newsroom_connector.cache'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'newsroom_connector_cache_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clean cache'),
    ];
    return $form;
  }

  /**
   * Submission handler to clean the cache.
   */
  public function cleanCache() {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->cacheBackend->deleteAll();
    $this->messenger()->addMessage($this->t('Cache has been cleaned.'));
  }

}
