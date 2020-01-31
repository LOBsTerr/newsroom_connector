<?php

namespace Drupal\newsroom_connector_item\Plugin\migrate;

use Drupal\Core\Language\LanguageInterface;
use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;

/**
 * Deriver for the newsroom item translations.
 */
class NewsroomItemLanguageDeriver extends BaseNewsroomLanguageDeriver {

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
      'name' => "item_name_$language_id",
      'label' => "Item name - $language_id",
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['name'][] = [
      'plugin' => 'skip_on_empty',
      'source' => "item_name_$language_id",
      'method' => 'row',
    ];
    $base_plugin_definition['process']['name'][] = [
      'plugin' => 'get',
      'source' => "item_name_$language_id",
      'language' => $language_id,
    ];

    // Description.
    $base_plugin_definition['source']['fields'][] = [
      'name' => "item_description_$language_id",
      'label' => "Item description - $language_id",
      'selector' => 'description[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['description'] = [
      'plugin' => 'get',
      'source' => "item_description_$language_id",
      'language' => $language_id,
    ];

    // Archive link.
    $base_plugin_definition['source']['fields'][] = [
      'name' => "item_archive_link_$language_id",
      'label' => "Item archive link - $language_id",
      'selector' => 'infsonewsroom:archivesLink[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['field_newsroom_archive_link'] = [
      'plugin' => 'get',
      'source' => "item_archive_link_$language_id",
      'language' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
