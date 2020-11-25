<?php

namespace Drupal\newsroom_connector\Plugin\migrate;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Deriver for the newsroom translations.
 */
abstract class BaseNewsroomLanguageDeriver extends DeriverBase implements ContainerDeriverInterface {

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

      $language_code = $language->getId();
      // For languages pt-pt, we take the first part only.
      if (strpos($language_code, '-') !== FALSE) {
        $parts = explode('-', $language_code);
        $language_code = $parts[0];
      }

      $derivative = $this->getDerivativeValues($base_plugin_definition, $language, strtoupper($language_code));
      $this->derivatives[$language_code] = $derivative;

    }

    return $this->derivatives;
  }

  /**
   * Creates a derivative definition for each available language.
   *
   * @param array $base_plugin_definition
   *  Base plugin definition.
   * @param LanguageInterface $language
   *  Language.
   * @param string $language_code
   *  Newsroom language code.
   *
   * @return array
   */
  protected abstract function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code);

}
