<?php

namespace Drupal\newsroom_connector_service\Plugin\migrate;

use Drupal\Core\Language\LanguageInterface;
use Drupal\newsroom_connector\Plugin\migrate\BaseServiceLanguageDeriver;

/**
 * Deriver for the newsroom service translations.
 */
class NewsroomServiceLanguageDeriver extends BaseServiceLanguageDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language) {
    $language_id = $language->getId();
    $language_code = strtoupper($language_id);

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language_id,
    ];

    // Name.
    $base_plugin_definition['source']['fields'][] = [
      'name' => "service_name_$language_id",
      'label' => "Service name - $language_id",
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['name'][] = [
      'plugin' => 'skip_on_empty',
      'source' => "service_name_$language_id",
      'method' => 'row',
    ];
    $base_plugin_definition['process']['name'][] = [
      'plugin' => 'get',
      'source' => "service_name_$language_id",
      'language' => $language_id,
    ];

    // Description.
    $base_plugin_definition['source']['fields'][] = [
      'name' => "service_description_$language_id",
      'label' => "Service description - $language_id",
      'selector' => 'description[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['description'] = [
      'plugin' => 'get',
      'source' => "service_description_$language_id",
      'language' => $language_id,
    ];

    // Archive link.
    $base_plugin_definition['source']['fields'][] = [
      'name' => "service_archive_link_$language_id",
      'label' => "Service archive link - $language_id",
      'selector' => 'infsonewsroom:archivesLink[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['field_newsroom_archive_link'] = [
      'plugin' => 'get',
      'source' => "service_archive_link_$language_id",
      'language' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
