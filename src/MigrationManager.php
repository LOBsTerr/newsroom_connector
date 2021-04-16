<?php

namespace Drupal\newsroom_connector;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\migrate\Plugin\MigrationPluginManager;

class MigrationManager implements MigrationManagerInterface {

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManager
   */
  protected $migrationPluginManager;

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(MigrationPluginManager $migrationPluginManager, LanguageManagerInterface $languageManager) {
    $this->migrationPluginManager = $migrationPluginManager;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public function cleanUpMigrations($migration_id, ContentEntityInterface $entity) {
    $id = $entity->getEntityType()->getKey('id');
    $destination_keys = [];
    $destination_keys[$id] = $entity->id();
    $this->deleteDestination($migration_id, $destination_keys);

    // Run cleanup for translations' migrations.
    $languages = $this->languageManager->getLanguages();
    foreach ($languages as $language) {
      $destination_keys['langcode'] = $language->getId();
      $this->deleteDestination($this->getTranslationMigrationIds($migration_id), $destination_keys);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function normalizeLanguage($language_id) {
    // For languages pt-pt, we take the first part only.
    if (strpos($language_id, '-') !== FALSE) {
      $parts = explode('-', $language_id);
      $language_id = $parts[0];
    }

    return $language_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getTranslationMigrationIds($migration_id) {
    // Run translations migrations.
    $translation_migration_ids = [];
    $languages = $this->languageManager->getLanguages();
    foreach ($languages as $language) {
      $language_id = $language->getId();

      // We skip EN as that is the original language.
      if ($language_id === 'en') {
        continue;
      }

      $language_id = $this->normalizeLanguage($language_id);
      $translation_migration_ids[] = "{$migration_id}_translations:{$language_id}";
    }

    return $translation_migration_ids;
  }

  /**
   * Delete migration records based on keys.
   *
   * @param string $migration_id
   *  Migration id.
   * @param array $destination_keys
   *  Destination keys.
   */
  protected function deleteDestination($migration_id, array $destination_keys) {
    $migration = $this->getMigration($migration_id);
    if (empty($migration)) {
      return;
    }
    /** @var \Drupal\migrate\Plugin\MigrateIdMapInterface $id_map */
    $id_map = $migration->getIdMap();
    $id_map->deleteDestination($destination_keys);
  }

  /**
   * Gets migration.
   *
   * @param string $migration_id
   *   Migration id.
   *
   * @return \Drupal\migrate\Plugin\MigrationInterface|null
   *   Migration object.
   */
  public function getMigration($migration_id) {
    return $this->migrationPluginManager->createInstance($migration_id);
  }

}
