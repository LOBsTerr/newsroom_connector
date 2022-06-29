<?php

namespace Drupal\newsroom_connector\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\newsroom_connector\MigrateExecutable;
use Drupal\newsroom_connector\MigrateBatchExecutable;
use Drupal\newsroom_connector\MigrationManagerInterface;
use Drupal\newsroom_connector\UniverseManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class for Newsroom processor plugins.
 */
abstract class NewsroomProcessorBase extends PluginBase implements NewsroomProcessorInterface {

  use StringTranslationTrait;
  use DependencySerializationTrait;

  /**
   * Use batch or standard migration.
   *
   * @var bool
   */
  protected $useBatch = TRUE;

  /**
   * The universe manager.
   *
   * @var \Drupal\newsroom_connector\UniverseManager
   */
  protected $universeManager;

  /**
   * Migration manager.
   *
   * @var \Drupal\newsroom_connector\MigrationManager
   */
  protected $migrationManager;

  /**
   * Request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    UniverseManagerInterface $universe_manager,
    MigrationManagerInterface $migration_manager,
    Request $request
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->universeManager = $universe_manager;
    $this->migrationManager = $migration_manager;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('newsroom_connector.universe_manager'),
      $container->get('newsroom_connector.migration_manager'),
      $container->get('request_stack')->getCurrentRequest()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function redirect($newsroom_id) {
    $entity = $this->getEntityByNewsroomId($newsroom_id);
    if (!empty($entity)) {
      $query_parameters = $this->request->query->all();
      $entity_url = $entity->toUrl();
      $entity_url->setOptions(array('query' => $query_parameters));
      return new RedirectResponse($entity_url->toString());
    }
    else {
      throw new NotFoundHttpException();
    }
  }

  /**
   * Get entity by original newsroom id.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Entity associated with newsroom id.
   */
  private function getEntityByNewsroomId($newsroom_id) {
    $definition = $this->getPluginDefinition();
    return $this->migrationManager->getEntityByNewsroomId($newsroom_id, $definition['entity_type'], $definition['bundle'], $definition['bundle_field']);
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
      // To use a new version of RSS.
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
  public function runImport(Url $url) {

    $import_disabled = $this->universeManager->getConfig()->get('import_disabled');
    if ($import_disabled) {
      return;
    }

    $definition = $this->getPluginDefinition();
    if (empty($definition['migrations'])) {
      return;
    }

    $migration_ids = [];
    $translations_ids = [];

    foreach ($definition['migrations'] as $migration_id) {
      $migration_ids[] = $migration_id;
      // User array_values to get an array without language key.
      $translations_ids = array_merge($translations_ids, array_values($this->migrationManager->getTranslationMigrationIds($migration_id)));
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
   * @param \Drupal\Core\Url $url
   *   Url.
   */
  protected function runMigration($migration_id, Url $url) {
    $migration = $this->migrationManager->getMigration($migration_id);
    if (!empty($migration)) {

      // Reset the status if it in IDLE mode after failing migrations.
      $status = $migration->getStatus();
      if ($status !== MigrationInterface::STATUS_IDLE) {
        $migration->setStatus(MigrationInterface::STATUS_IDLE);
      }

      if ($this->useBatch) {
        // Set custom url, based on universe settings.
        // Always force update.
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
        // Set custom url, based on universe settings.
        $source = $migration->getSourceConfiguration();
        $source['urls'] = $url->toUriString();
        $migration->set('source', $source);

        // Always force update.
        $migration->getIdMap()->prepareUpdate();
        $executable = new MigrateExecutable($migration, new MigrateMessage());
        $executable->import();
      }
    }
  }

}
