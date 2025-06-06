<?php

namespace Drupal\newsroom_connector;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateRollbackEvent;
use Drupal\migrate\Event\MigrateRowDeleteEvent;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\migrate_plus\Event\MigrateEvents as MigratePlusEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Migration Manager.
 *
 * @package Drupal\newsroom_connector
 */
class MigrationManager implements MigrationManagerInterface {

  /**
   * Migration plugin manager service.
   *
   * @var \Drupal\migrate\Plugin\MigrationPluginManager
   */
  protected $migrationPluginManager;

  /**
   * Language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * The cache backend service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * Migratinos plugins.
   *
   * @var array
   */
  protected $migrations = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(
    MigrationPluginManager $migrationPluginManager,
    LanguageManagerInterface $languageManager,
    EntityTypeManagerInterface $entity_type_manager,
    EventDispatcherInterface $dispatcher,
    CacheBackendInterface $cache_backend,
  ) {
    $this->migrationPluginManager = $migrationPluginManager;
    $this->languageManager = $languageManager;
    $this->entityTypeManager = $entity_type_manager;
    $this->dispatcher = $dispatcher;
    $this->cacheBackend = $cache_backend;
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
    $translation_migrations = $this->getTranslationMigrationIds($migration_id);
    foreach ($translation_migrations as $language_id => $translation_migration) {
      $destination_keys['langcode'] = $language_id;
      $this->deleteDestination($translation_migration, $destination_keys);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMappingsBySourceId($migration_id, $source_id) {
    $this->deleteDestinationBySourceId($migration_id, $source_id);

    // Run cleanup for translations' migrations.
    $translation_migrations = $this->getTranslationMigrationIds($migration_id);
    foreach ($translation_migrations as $translation_migration) {
      $this->deleteDestinationBySourceId($translation_migration, $source_id);
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
      $translation_migration_ids[$language_id] = "{$migration_id}_translations:{$language_id}";
    }

    return $translation_migration_ids;
  }

  /**
   * Delete migration destination records based on keys.
   *
   * @param string $migration_id
   *   Migration id.
   * @param array $destination_keys
   *   Destination keys.
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
   * Delete migration destination records based on keys.
   *
   * @param string $migration_id
   *   Migration id.
   * @param string $source_id
   *   Source ID.
   */
  protected function deleteDestinationBySourceId($migration_id, $source_id) {
    $migration = $this->getMigration($migration_id);
    if (empty($migration)) {
      return;
    }

    /** @var \Drupal\migrate\Plugin\MigrateIdMapInterface $id_map */
    $id_map = $migration->getIdMap();
    $destination_ids = $this->getDestinationIdsBySourceIds($migration, [$source_id]);
    if (empty($destination_ids)) {
      return;
    }

    foreach ($destination_ids as $destination_id) {
      $id_map->deleteDestination($destination_id);
    }

  }

  /**
   * Get destination ids by source ids.
   *
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   Migration.
   * @param array $source_id_values
   *   List of source IDs.
   *
   * @return array
   *   List of destination IDs.
   */
  public function getDestinationIdsBySourceIds(MigrationInterface $migration, array $source_id_values) {
    $destination_keys = array_keys($migration->getDestinationPlugin()->getIds());
    $indexed_ids = $migration->getIdMap()
      ->lookupDestinationIds($source_id_values);
    $keyed_ids = [];
    foreach ($indexed_ids as $id) {
      $keyed_ids[] = array_combine($destination_keys, $id);
    }
    return $keyed_ids;
  }

  /**
   * {@inheritdoc}
   */
  public function getMigration($migration_id) {
    if (empty($this->migrations[$migration_id])) {
      $this->migrations[$migration_id] = $this->migrationPluginManager->createInstance($migration_id);
    }

    return $this->migrations[$migration_id];
  }

  /**
   * {@inheritdoc}
   */
  public function rollback($migration_id, array $source_id_values = []) {
    $migration = $this->getMigration($migration_id);
    if (empty($migration)) {
      return;
    }

    $id_map = $migration->getIdMap();
    $id_map->prepareUpdate();

    $id_map->rewind();
    $destination = $migration->getDestinationPlugin();

    while ($id_map->valid()) {
      $map_source_id = $id_map->currentSource();
      if (in_array($map_source_id['item_id'], $source_id_values, TRUE)) {
        $destination_ids = $id_map->currentDestination();
        $this->dispatchRowDeleteEvent(MigrateEvents::PRE_ROW_DELETE, $migration, $destination_ids);
        $this->dispatchRowDeleteEvent(MigratePlusEvents::MISSING_SOURCE_ITEM, $migration, $destination_ids);
        $destination->rollback($destination_ids);
        $this->dispatchRowDeleteEvent(MigrateEvents::POST_ROW_DELETE, $migration, $destination_ids);
        $id_map->delete($map_source_id);
      }
      $id_map->next();
    }
    $this->dispatcher->dispatch(new MigrateRollbackEvent($migration), MigrateEvents::POST_ROLLBACK);
  }

  /**
   * Dispatches MigrateRowDeleteEvent event.
   *
   * @param string $event_name
   *   The event name to dispatch.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The active migration.
   * @param array $destination_ids
   *   The destination identifier values of the record.
   */
  protected function dispatchRowDeleteEvent($event_name, MigrationInterface $migration, array $destination_ids) {
    // Symfony changing dispatcher so implementation could change.
    $this->dispatcher->dispatch(new MigrateRowDeleteEvent($migration, $destination_ids), $event_name);
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityByNewsroomId($newsroom_id, $entity_type, $bundle, $bundle_field) {
    $cid = "newsroom_entity:$newsroom_id:$entity_type:$bundle:$bundle_field";

    $data_cached = $this->cacheBackend->get($cid);

    if (!$data_cached) {
      $items = $this->getEntitiesByNewsroomId($newsroom_id, $entity_type, $bundle, $bundle_field);
      $item = reset($items);

      $this->cacheBackend->set($cid, $item);
    }
    else {
      $item = $data_cached->data;
    }

    return $item;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntitiesByNewsroomId($newsroom_id, $entity_type, $bundle, $bundle_field) {
    return $this->entityTypeManager
      ->getStorage($entity_type)
      ->loadByProperties([
        'field_newsroom_id' => $newsroom_id,
        $bundle_field => $bundle,
      ]);
  }

}
