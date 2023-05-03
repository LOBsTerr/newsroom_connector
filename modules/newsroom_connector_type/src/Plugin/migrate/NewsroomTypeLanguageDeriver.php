<?php

namespace Drupal\newsroom_connector_type\Plugin\migrate;

use Drupal\Core\Language\LanguageInterface;
use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;

/**
 * Deriver for the newsroom type translations.
 */
class NewsroomTypeLanguageDeriver extends BaseNewsroomLanguageDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code) {
    $language_id = $language->getId();

    $base_plugin_definition['source']['item_selector'] = '//channel/item[string-length(title[@lang="' . $language_code . '"]) > 0]';

    $base_plugin_definition['source']['fields'][] = [
      'name' => "type_name_$language_id",
      'label' => "Type name - $language_id",
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['name'][] = [
      'plugin' => 'get',
      'source' => "type_name_$language_id",
      'language' => $language_id,
    ];

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
