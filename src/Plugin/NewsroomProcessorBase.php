<?php

namespace Drupal\nexteuropa_newsroom\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\migrate_tools\MigrateExecutable;
use Drupal\nexteuropa_newsroom\UniverseManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base class for Newsroom processor plugins.
 */
abstract class NewsroomProcessorBase extends PluginBase implements NewsroomProcessorInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The universe manager.
   *
   * @var \Drupal\nexteuropa_newsroom\UniverseManager
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
      $container->get('nexteuropa_newsroom.universe_manager'),
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
      $response = new RedirectResponse($entity->toUrl()->toString());
      $response->send();
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

  public function getEntityUrl($newsroom_id = NULL) {
    $params = [];
    $definition = $this->getPluginDefinition();
    if (!empty($newsroom_id)) {
      $params[$definition['import_segment']] = $newsroom_id;
    }
    return $this->universeManager->buildUrl($definition['import_script'], $params);
  }

  public function import($newsroom_id = NULL) {
    $url = $this->getEntityUrl($newsroom_id);
    $this->runImport($this->pluginId, $url);

    $languages = $this->languageManager->getLanguages();
    foreach ($languages as $language) {
      $language_id = $language->getId();

      // We skip EN as that is the original language.
      if ($language_id === 'en') {
        continue;
      }

      $this->runImport("{$this->pluginId}_translations:{$language_id}", $url);
    }
  }

  protected function runImport($plugin_id, $url) {
    $migration = $this->migrationPluginManager->createInstance($plugin_id);
    $source = $migration->get('source');
    $source['url'] = $url;
    $migration->set('source', $source);
    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();
  }

}
