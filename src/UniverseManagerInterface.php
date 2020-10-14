<?php

namespace Drupal\newsroom_connector;

interface UniverseManagerInterface {

  /**
   * Get newsroom config.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   Configuration of the newsroom.
   */
  public function getConfig();

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
  public function getValue($name, $default_value = NULL);

  /**
   * Get universe Id.
   *
   * @return string
   *   Universe Id.
   */
  public function getUniverseId();

  /**
   * Return newsroom base url.
   *
   * @return string
   *   Newsroom base URL.
   */
  public function getBaseUrl();

  /**
   * Return newsroom item id edit link.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public function getItemEditUrl($newsroom_id);

  /**
   * Return newsroom item id edit link.
   *
   * @param string $script_name
   *   Original newsroom id.
   * @param array $params
   *   URL parameters.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public function buildUrl($script_name, $params = []);
}