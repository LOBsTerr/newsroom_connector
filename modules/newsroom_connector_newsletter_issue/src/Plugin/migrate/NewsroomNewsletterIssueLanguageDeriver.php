<?php

namespace Drupal\newsroom_connector_newsletter_issue\Plugin\migrate;

use Drupal\Core\Language\LanguageInterface;
use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;

/**
 * Deriver for the newsroom newsletter issue translations.
 */
class NewsroomNewsletterIssueLanguageDeriver extends BaseNewsroomLanguageDeriver {

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
    $base_plugin_definition['process']['title'][] = [
      'plugin' => 'skip_on_empty',
      'source' => 'title',
      'method' => 'row',
    ];
    $base_plugin_definition['process']['title'][] = [
      'plugin' => 'get',
      'source' => 'title',
      'language' => $language_id,
    ];

    $base_plugin_definition['source']['fields'][] = [
      'name' => 'title',
      'label' => 'Title',
      'selector' => 'title[@lang="' . $language_code . '"]/text()',
    ];

    // Link.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'link',
      'label' => 'Link',
      'selector' => 'link[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['field_newsroom_main_link/uri'] = [
      'plugin' => 'get',
      'source' => 'link',
      'language' => $language_id,
    ];

    return $base_plugin_definition;
  }

}
