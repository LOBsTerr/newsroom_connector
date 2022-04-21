<?php

namespace Drupal\newsroom_connector_item\Plugin\migrate;

use Drupal\Core\Language\LanguageInterface;
use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;

/**
 * Deriver for the newsroom item document translations.
 */
class NewsroomItemDocumentLanguageDeriver extends BaseNewsroomLanguageDeriver {

  /**
   * {@inheritdoc}
   */
  protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code) {
    $language_id = $language->getId();

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language_id,
    ];

    // Title.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'title',
      'label' => 'Title',
      'selector' => 'enclosure[@lang="' . $language_code . '" and @nrdoctype="document" and not(@external_app="DocsRoom")]/@title',
    ];

    $base_plugin_definition['process']['field_newsroom_documents/title'] = [
      'plugin' => 'get',
      'source' => 'title',
      'language' => $language_id,
    ];

    // URL.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'url',
      'label' => 'URL',
      'selector' => 'enclosure[@lang="' . $language_code . '" and @nrdoctype="document" and not(@external_app="DocsRoom")]/@url',
    ];

    $base_plugin_definition['process']['field_newsroom_documents/uri'] = [
      'plugin' => 'get',
      'source' => 'url',
      'language' => $language_id,
    ];

    // Is machine translation.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'is_machine_translated',
      'label' => 'Is document machine translated',
      'selector' => 'enclosure[@lang="' . $language_code . '" and @nrdoctype="document" and not(@external_app="DocsRoom")]/@machineTranslation',
    ];

    $base_plugin_definition['process']['field_newsroom_is_machine_trans'] = [
      'plugin' => 'get',
      'source' => 'is_machine_translated',
      'language' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
