<?php

namespace Drupal\newsroom_connector\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\migrate_tools\MigrateExecutable;
use Drupal\newsroom_connector\MigrateBatchExecutable;
use Drupal\newsroom_connector\UniverseManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base class for Newsroom processor plugins.
 */
abstract class NewsroomProcessorBase extends PluginBase implements NewsroomProcessorInterface {

  use StringTranslationTrait;
  use DependencySerializationTrait;

  protected $useBatch = TRUE;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The universe manager.
   *
   * @var \Drupal\newsroom_connector\UniverseManager
   */
  protected $universeManager;

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    UniverseManagerInterface $universeManager,
    MigrationPluginManager $migrationPluginManager,
    LanguageManagerInterface $languageManager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->universeManager = $universeManager;
    $this->migrationPluginManager = $migrationPluginManager;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('newsroom_connector.universe_manager'),
      $container->get('plugin.manager.migration'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function redirect($newsroom_id) {
    $entity = $this->getEntityByNewsroomId($newsroom_id);
    if (!empty($entity)) {
      return new RedirectResponse($entity->toUrl()->toString());
    }
    else {
      throw new NotFoundHttpException();
    }
  }


  private function getEntityByNewsroomId($newsroom_id) {
    $entity = NULL;
    $definition = $this->getPluginDefinition();
    $items = $this->entityTypeManager
      ->getStorage($definition['content_type'])
      ->loadByProperties([
        'field_newsroom_id' => $newsroom_id,
        $definition['bundle_field'] => $definition['bundle'],
      ]);

    if ($item = reset($items)) {
      $entity = $item;
    }

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityUrl($newsroom_id = NULL) {
    $params = [];
    $definition = $this->getPluginDefinition();
    if (!empty($newsroom_id)) {
      $params[$definition['import_segment']] = $newsroom_id;
    }

    // @TODO move it to a separate class.
    if ($this->getPluginId() == 'newsroom_item') {
      $config = $this->universeManager->getConfig();
      if ($config->get('subsite')) {
        $params['subsite'] = $config->get('subsite');
      }
    }

    $params['t'] = time();

    return $this->universeManager->buildUrl($definition['import_script'], $params);
  }

  /**
   * {@inheritdoc}
   */
  public function import($newsroom_id, $use_batch = TRUE) {
    $this->useBatch = $use_batch;
    $url = $this->getEntityUrl($newsroom_id);
    $this->runImport($url);
  }

  /**
   * {@inheritdoc}
   */
  public function runImport($url) {

    $definition = $this->getPluginDefinition();
    if (empty($definition['migrations'])) {
      return;
    }

    $migration_ids = [];
    $translations_ids = [];

    foreach ($definition['migrations'] as $migration_id) {
      $migration_ids[] = $migration_id;

      // Run translations migrations.
      $languages = $this->languageManager->getLanguages();
      foreach ($languages as $language) {
        $language_id = $language->getId();

        // We skip EN as that is the original language.
        if ($language_id === 'en') {
          continue;
        }

        // For languages pt-pt, we take the first part only.
        if (strpos($language_id, '-') !== FALSE) {
          $parts = explode('-', $language_id);
          $language_id = $parts[0];
        }

        $translations_ids[] = "{$this->pluginId}_translations:{$language_id}";
      }
    }

    // First we run main content migrations.
    foreach ($migration_ids as $id) {
      $this->runMigration($id, $url);
    }

    // Then we run main translations migrations.
    foreach ($translations_ids as $id) {
      $this->runMigration($id, $url);
    }
  }

  /**
   * Execute migration for the given URL.
   *
   * @param string $migration_id
   *   Migration Id.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\migrate\MigrateException
   */
  protected function runMigration($migration_id, $url) {
    $migration = $this->migrationPluginManager->createInstance($migration_id);
    if (!empty($migration)) {
      $status = $migration->getStatus();
      if ($status !== MigrationInterface::STATUS_IDLE) {
        $migration->setStatus(MigrationInterface::STATUS_IDLE);
      }
      if ($this->useBatch) {
        $options = [
          'limit' => 0,
          'update' => 1,
          'force' => 1,
          'source_url' => $url->toUriString(),
        ];
        $executable = new MigrateBatchExecutable($migration, new MigrateMessage(), $options);
        $executable->batchImport();
      }
      else {
        $source = $migration->get('source');
        $source['urls'] = $url->toUriString();
        $migration->set('source', $source);
        $migration->getIdMap()->prepareUpdate();
        $executable = new MigrateExecutable($migration, new MigrateMessage());
        $executable->import();

        $this->t('Import has been successfully finished');
      }
    }
  }

}
