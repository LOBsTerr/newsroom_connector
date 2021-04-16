<?php

namespace Drupal\newsroom_connector;

use Drupal\Core\Entity\ContentEntityInterface;

interface MigrationManagerInterface {

  /**
   * Clean migration records for a deleted entity.
   *
   * @param string $migration_id
   *   Migration id
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Deleted entity.
   */
  public function cleanUpMigrations($migration_id, ContentEntityInterface $entity);

  /**
   * Normalize language to newsroom format.
   *
   * @param string $language_id
   *   Language id.
   * @return string
   *   Normalized language.
   */
  public function normalizeLanguage($language_id);

  /**
   * Get migration ids for translations.
   *
   * @param string $migration_id
   *   Plugin id
   * @return array
   *   List of translation migration ids.
   */
  public function getTranslationMigrationIds($migration_id);

}
