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
  protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code) {
    $language_id = $language->getId();
    $base_plugin_definition['source']['item_selector'] = '//channel/item[count(title[@lang="' . $language_code . '"]) > 0]';

    $base_plugin_definition['process']['langcode'] = [
      'plugin' => 'default_value',
      'default_value' => $language_id,
    ];

    $fields = [
      [
        'name' => 'title',
        'field' => 'title',
        'xpath' => 'title',
      ],
      [
        'name' => 'body',
        'field' => 'field_newsroom_body/value',
        'xpath' => 'infsonewsroom:FullContent',
      ],
      [
        'name' => 'teaser',
        'field' => 'field_newsroom_teaser',
        'xpath' => 'infsonewsroom:BasicTeaser',
      ],
      [
        'name' => 'see_also_text',
        'field' => 'field_newsroom_see_also/title',
        'xpath' => 'infsonewsroom:ContextOtherLinkText',
      ],
      [
        'name' => 'see_also_url',
        'field' => 'field_newsroom_see_also/uri',
        'xpath' => 'infsonewsroom:ContextOtherLinkUrl',
      ],
      [
        'name' => 'main_link',
        'field' => 'field_newsroom_main_link',
        'xpath' => 'infsonewsroom:BasicUrl',
      ],
      [
        'name' => 'project_acronym',
        'field' => 'field_newsroom_project_acronym',
        'xpath' => 'infsonewsroom:ContextProjectAcronym',
      ],
      [
        'name' => 'project_name',
        'field' => 'field_newsroom_project_name',
        'xpath' => 'infsonewsroom:ContextProjectName',
      ],
      [
        'name' => 'project_website_url',
        'field' => 'field_newsroom_project_website/uri',
        'xpath' => 'infsonewsroom:ContextProjectURL',
      ],
      [
        'name' => 'project_website_title',
        'field' => 'field_newsroom_project_website/title',
        'xpath' => 'infsonewsroom:ContextProjectURLDisplay',
      ],
      [
        'name' => 'project_coordinator',
        'field' => 'field_newsroom_pr_coordinator',
        'xpath' => 'infsonewsroom:ContextProjectCoordinator',
      ],
      [
        'name' => 'venue',
        'field' => 'field_newsroom_venue',
        'xpath' => 'infsonewsroom:ContextVenue',
      ],
      [
        'name' => 'organiser',
        'field' => 'field_newsroom_organiser',
        'xpath' => 'infsonewsroom:ContextOrganiser',
      ],
      [
        'name' => 'author',
        'field' => 'field_newsroom_author',
        'xpath' => 'infsonewsroom:ContextAuthor',
      ],
      [
        'name' => 'speaker',
        'field' => 'field_newsroom_speaker',
        'xpath' => 'infsonewsroom:ContextSpeaker',
      ],
      [
        'name' => 'author',
        'field' => 'field_newsroom_author',
        'xpath' => 'infsonewsroom:ContextAuthor',
      ],
      [
        'name' => 'registration_link_url',
        'field' => 'field_newsroom_registration_link/uri',
        'xpath' => 'infsonewsroom:ContextRegistrationLink',
      ],
      [
        'name' => 'registration_link_text',
        'field' => 'field_newsroom_registration_link/title',
        'xpath' => 'infsonewsroom:ContextRegistrationLinkText',
      ],
      [
        'name' => 'contact_text',
        'field' => 'field_newsroom_contact_text',
        'xpath' => 'infsonewsroom:ContextContactText',
      ],
      [
        'name' => 'contact_info',
        'field' => 'field_newsroom_contact_info',
        'xpath' => 'infsonewsroom:ContextContactEmail',
      ],
      [
        'name' => 'linked_object',
        'field' => 'field_newsroom_linked_object',
        'xpath' => 'infsonewsroom:FullLinkedObject',
      ],
      [
        'name' => 'quote_box',
        'field' => 'field_newsroom_quote_box',
        'xpath' => 'infsonewsroom:FullQuoteBox',
      ],
      [
        'name' => 'is_machine_translated',
        'field' => 'field_newsroom_is_machine_trans',
        'xpath' => 'infsonewsroom:MachineTranslation',
      ],
    ];

    foreach ($fields as $field) {
      $base_plugin_definition['source']['fields'][] = [
        'name' => $field['name'],
        'label' => $field['name'],
        'selector' => $field['xpath'] . '[@lang="' . $language_code . '"]/text()',
      ];

      $base_plugin_definition['process'][$field['field']] = [
        'plugin' => 'get',
        'source' => $field['name'],
        'language' => $language_id,
      ];
    }

    $base_plugin_definition['process']['field_newsroom_body/format'] = [
      'plugin' => 'default_value',
      'default_value' => 'newsroom_basic_html',
    ];

    $base_plugin_definition['source']['fields'][] = [
      'name' => 'related_documents_url',
      'label' => 'Related documents - URL',
      'selector' => 'enclosure[@nrdoctype="document" and ' . $language_code . ' and not(@external_app="DocsRoom")]/@url',
    ];
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'related_documents_title',
      'label' => 'Related documents - title',
      'selector' => 'enclosure[@nrdoctype="document" and ' . $language_code . ' and not(@external_app="DocsRoom")]/@title',
    ];

    $base_plugin_definition['process']['field_newsroom_documents'] = [
      'plugin' => 'sub_process',
      'source' => 'documents',
      'process' => [
        'title' => 'title',
        'uri' => 'url',
      ],
    ];

    return $base_plugin_definition;
  }

}
