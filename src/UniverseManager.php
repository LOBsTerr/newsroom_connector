<?php

namespace Drupal\newsroom_connector;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;

class UniverseManager implements UniverseManagerInterface {

  /**
   * Immutable configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->settings = $configFactory->get('newsroom_connector.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig() {
    return $this->settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue($name, $default_value = NULL) {
    $value = $this->getConfig()->get($name);
    return !empty($value) ? $value : $default_value;
  }

  /**
   * {@inheritdoc}
   */
  public function getUniverseId() {
    return $this->getValue('universe_id');
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseUrl() {
    return $this->getValue('base_url') . $this->getUniverseId() . '/';
  }

  /**
   * {@inheritdoc}
   */
  public function getItemEditUrl($newsroom_id) {
    return $this->buildUrl("item/{$newsroom_id}/edit");
  }

  /**
   * {@inheritdoc}
   */
  public function buildUrl($script_name, $params = []) {
    return Url::fromUri($this->getBaseUrl() . $script_name, ['query' => $params]);
  }

}
