<?php

namespace Drupal\nexteuropa_newsroom\Helper;

class BaseHelper {

  /**
   * Get newsroom config.
   *
   * @return \Drupal\Core\Config\ImmutableConfig
   *   Configuration of the newsroom.
   */
  protected static function getConfig() {
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
  protected static function getValue($name, $default_value = NULL) {
    $value = self::getConfig()->get($name);
    return !empty($value) ? $value : $default_value;
  }
}
