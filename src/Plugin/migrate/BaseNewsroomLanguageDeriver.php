<?php

namespace Drupal\newsroom_connector\Plugin\migrate;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\newsroom_connector\MigrationManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Deriver for the newsroom translations.
 */
abstract class BaseNewsroomLanguageDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Migration manager.
   *
   * @var \Drupal\newsroom_connector\MigrationManagerInterface
   */
  protected $migrationManager;

  /**
   * NewsroomItemServiceLanguageDeriver constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   Language manager.
   * @param \Drupal\newsroom_connector\MigrationManagerInterface $migration_manager
   *   Migration manager.
   */
  public function __construct(LanguageManagerInterface $languageManager, MigrationManagerInterface $migration_manager) {
    $this->languageManager = $languageManager;
    $this->migrationManager = $migration_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    $base_plugin_id,
  ) {
    return new static(
      $container->get('language_manager'),
      $container->get('newsroom_connector.migration_manager')
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

      $language_code = $this->migrationManager->normalizeLanguage($language->getId());

      $this->derivatives[$language_code] = $this->getDerivativeValues($base_plugin_definition, $language, mb_strtoupper($language_code));
    }

    return $this->derivatives;
  }

  /**
   * Creates a derivative definition for each available language.
   *
   * @param array $base_plugin_definition
   *   Base plugin definition.
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   Language.
   * @param string $language_code
   *   Newsroom language code.
   *
   * @return array
   *   List of derivatives.
   */
  abstract protected function getDerivativeValues(array $base_plugin_definition, LanguageInterface $language, $language_code);

}
