<?php

namespace Drupal\nexteuropa_newsroom\Helpers;

class UniverseHelper {

  /**
   * Get newsroom config.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   Configuration of the newsroom.
   */
  private static function getConfig() {
    return \Drupal::config('nexteuropa_newsroom.settings');
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
  private static function getValue($name, $default_value = NULL) {
    $value = self::getConfig()->get($name);
    return !empty($value) ? $value : $default_value;
  }

  /**
   * Get universe Id.
   *
   * @return string
   *   Universe Id.
   */
  public static function getUniverseId() {
    return self::getValue('universe_id');
  }

  /**
   * Return newsroom base url.
   *
   * @return string
   *   Newsroom base URL.
   */
  public static function getBaseUrl() {
    return self::getValue('base_url') . self::getUniverseId();
  }
}
