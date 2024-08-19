<?php

namespace Drupal\newsroom_connector;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface Migration Manager Interface.
 *
 * @package Drupal\newsroom_connector
 */
interface MigrationManagerInterface {

  /**
   * Clean migration records for a deleted entity.
   *
   * @param string $migration_id
   *   Migration id.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Deleted entity.
   */
  public function cleanUpMigrations($migration_id, ContentEntityInterface $entity);

  /**
   * Normalize language to newsroom format.
   *
   * @param string $language_id
   *   Language id.
   *
   * @return string
   *   Normalized language.
   */
  public function normalizeLanguage($language_id);

  /**
   * Get migration ids for translations.
   *
   * @param string $migration_id
   *   Plugin id.
   *
   * @return array
   *   List of translation migration ids.
   */
  public function getTranslationMigrationIds($migration_id);

  /**
   * Rollback migration items.
   *
   * @param string $migration_id
   *   Migration id.
   * @param array $source_id_values
   *   Sources ids.
   */
  public function rollback($migration_id, array $source_id_values = []);

  /**
   * Get entity by original newsroom id.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Bundle.
   * @param string $bundle_field
   *   Bundle field.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Entity associated with newsroom id.
   */
  public function getEntityByNewsroomId($newsroom_id, $entity_type, $bundle, $bundle_field);

  /**
   * Get entities by original newsroom id.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Bundle.
   * @param string $bundle_field
   *   Bundle field.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Entity associated with newsroom id.
   */
  public function getEntitiesByNewsroomId($newsroom_id, $entity_type, $bundle, $bundle_field);

  /**
   * Get migration.
   *
   * @param string $migration_id
   *   Migration id.
   *
   * @return \Drupal\migrate\Plugin\MigrationInterface|null
   *   Migration object.
   */
  public function getMigration($migration_id);

  /**
   * Clean mappings by source id.
   *
   * @param string $migration_id
   *   Migration id.
   * @param string $source_id
   *   Source id.
   */
  public function deleteMappingsBySourceId($migration_id, $source_id);
}
