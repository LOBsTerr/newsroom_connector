<?php

namespace Drupal\newsroom_connector_item\Plugin\migrate;

use Drupal\Core\Language\LanguageInterface;
use Drupal\newsroom_connector\Plugin\migrate\BaseNewsroomLanguageDeriver;

/**
 * Deriver for the newsroom item translations.
 */
class NewsroomItemImageMediaLanguageDeriver extends BaseNewsroomLanguageDeriver {

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
      'name' => 'image_name',
      'label' => 'Image name',
      'selector' => 'infsonewsroom:PicTitle[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['name'] = [
      'plugin' => 'get',
      'source' => 'image_name',
      'language' => $language_id,
    ];

    // Alt.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'alt_text',
      'label' => 'Alt text',
      'selector' => 'infsonewsroom:PicAlt[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['field_newsroom_alt_text'] = [
      'plugin' => 'get',
      'source' => 'alt_text',
      'language' => $language_id,
    ];

    // Caption.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'caption',
      'label' => 'Caption',
      'selector' => 'infsonewsroom:PicCaption[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['field_newsroom_caption'] = [
      'plugin' => 'get',
      'source' => 'caption',
      'language' => $language_id,
    ];

    // Copyrights.
    $base_plugin_definition['source']['fields'][] = [
      'name' => 'copyright',
      'label' => 'Copyright',
      'selector' => 'infsonewsroom:PicCopyright[@lang="' . $language_code . '"]/text()',
    ];

    $base_plugin_definition['process']['field_newsroom_copyright'] = [
      'plugin' => 'get',
      'source' => 'copyright',
      'language' => $language_id,
    ];

    // @TODO add it later.
    // File title.
//    $base_plugin_definition['process']['field_media_image_newsroom/title'] = [
//      'plugin' => 'get',
//      'source' => 'image_name',
//      'language' => $language_id,
//    ];

    // File alt text.
//    $base_plugin_definition['process']['field_media_image_newsroom/alt'] = [
//      'plugin' => 'get',
//      'source' => 'image_name',
//      'language' => $language_id,
//    ];

//    var_dump($base_plugin_definition['source']['fields']);
//    var_dump($base_plugin_definition['process']);

    return $base_plugin_definition;
  }

}
