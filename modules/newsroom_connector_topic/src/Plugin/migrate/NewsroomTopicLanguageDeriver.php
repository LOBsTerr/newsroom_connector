<?php

namespace Drupal\newsroom_connector_topic\Plugin\migrate;

use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;
use Drupal\Core\Language\LanguageInterface;

/**
 * Deriver for the newsroom topic translations.
 */
class NewsroomTopicLanguageDeriver extends BaseNewsroomLanguageDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code) {
    $language_id = $language->getId();

    $base_plugin_definition['source']['item_selector'] = '//channel/item[string-length(title[@lang="' . $language_code . '"]) > 0]';

    // Name.
    $base_plugin_definition['source']['fields'][] = [
      'name' => "topic_name_$language_id",
      'label' => "Topic name - $language_id",
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['name'][] = [
      'plugin' => 'get',
      'source' => "topic_name_$language_id",
      'language' => $language_id,
    ];

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
