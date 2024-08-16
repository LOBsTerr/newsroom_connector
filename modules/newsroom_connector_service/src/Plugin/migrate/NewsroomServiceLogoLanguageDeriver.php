<?php

namespace Drupal\newsroom_connector_service\Plugin\migrate;

use Drupal\Core\Language\LanguageInterface;
use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;

/**
 * Deriver for the newsroom service logo translations.
 */
class NewsroomServiceLogoLanguageDeriver extends BaseNewsroomLanguageDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code) {
    $language_id = $language->getId();

    $base_plugin_definition['source']['fields'][] = [
      'name' => 'service_name',
      'label' => 'Name',
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language_id,
    ];

    $base_plugin_definition['source']['fields'][] = [
      'name' => "filename_$language_id",
      'label' => "File name - $language_id",
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['filename'][] = [
      'plugin' => 'skip_on_empty',
      'source' => "filename_$language_id",
      'method' => 'row',
    ];

    $base_plugin_definition['process']['filename'][] = [
      'plugin' => 'get',
      'source' => "filename_$language_id",
      'language' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
