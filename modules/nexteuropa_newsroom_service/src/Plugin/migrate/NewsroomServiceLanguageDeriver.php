<?php

namespace Drupal\nexteuropa_newsroom_service\Plugin\migrate;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Deriver for the newsroom service translations.
 */
class NewsroomServiceLanguageDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * NewsroomItemServiceLanguageDeriver constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   */
  public function __construct(LanguageManagerInterface $languageManager) {
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    $base_plugin_id
  ) {
    return new static(
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $languages = $this->languageManager->getLanguages();
    foreach ($languages as $language) {
      // We skip EN as that is the original language.
      if ($language->getId() === 'en') {
        continue;
      }

      $derivative = $this->getDerivativeValues($base_plugin_definition, $language);
      $this->derivatives[$language->getId()] = $derivative;
    }

//    var_dump($this->derivatives[$language->getId()]['process']);
//    var_dump($this->derivatives[$language->getId()]['source']);

    return $this->derivatives;
  }

  /**
   * Creates a derivative definition for each available language.
   *
   * @param array $base_plugin_definition
   * @param LanguageInterface $language
   *
   * @return array
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
