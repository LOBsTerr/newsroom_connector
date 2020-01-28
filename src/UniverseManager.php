<?php

namespace Drupal\newsroom_connector;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;

class UniverseManager implements UniverseManagerInterface {

  /**
   * Imutable configuration.
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
   * Get newsroom config.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   Configuration of the newsroom.
   */
  public function getConfig() {
    return $this->settings;
  }

  /**
   * Get configuration value.
   *
   * @param string $name
   *   Configuration name.
   * @param mix|null $default_value
   *   Default value for configuration.
   *
   * @return array|mixed|null
   *   Configuration value.
   */
  public function getValue($name, $default_value = NULL) {
    $value = $this->getConfig()->get($name);
    return !empty($value) ? $value : $default_value;
  }

  /**
   * Get universe Id.
   *
   * @return string
   *   Universe Id.
   */
  public function getUniverseId() {
    return $this->getValue('universe_id');
  }

  /**
   * Return newsroom base url.
   *
   * @return string
   *   Newsroom base URL.
   */
  public function getBaseUrl() {
    return $this->getValue('base_url') . $this->getUniverseId();
  }

  /**
   * Return newsroom item id edit link.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public function getItemEditUrl($newsroom_id) {
    return $this->buildUrl($this->getValue('item_edit_segment'), [$this->getValue('item_edit_segment') => $newsroom_id]);
  }

  /**
   * Return newsroom item id edit link.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public function buildUrl($script_name, $params = []) {
    return Url::fromUri($this->getBaseUrl() . '/' . $script_name, ['query' => $params])->toUriString();
  }




}
