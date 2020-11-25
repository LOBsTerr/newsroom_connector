<?php

namespace Drupal\newsroom_connector_country\Plugin\migrate;

use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;
use Drupal\Core\Language\LanguageInterface;

/**
 * Deriver for the newsroom country translations.
 */
class NewsroomCountryLanguageDeriver extends BaseNewsroomLanguageDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code) {
    $language_id = $language->getId();

    // Name.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'name',
      'label' => 'Name',
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['name'] = [
      'plugin' => 'skip_on_empty',
      'source' => 'name',
      'method' => 'row',
      'language' => $language_id,
    ];

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
