<?php

namespace Drupal\newsroom_connector_item\Plugin\newsroom;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\newsroom_connector\MigrationManagerInterface;
use Drupal\newsroom_connector\Plugin\NewsroomProcessorBase;
use Drupal\newsroom_connector\UniverseManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles typical operations for newsroom item.
 *
 * @NewsroomProcessor (
 *   id = "newsroom_item",
 *   entity_type = "node",
 *   bundle = "newsroom_item",
 *   bundle_field = "type",
 *   import_script = "feed/full/new",
 *   import_segment = "item_id",
 *   label = @Translation("Newsroom item"),
 *   migrations = {
 *     "newsroom_item_image",
 *     "newsroom_item_image_media",
 *     "newsroom_item",
 *   }
 * )
 */
class NewsroomItemNewsroomProcessor extends NewsroomProcessorBase {

  /**
   * The cache backend service.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheBackend;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    UniverseManagerInterface $universe_manager,
    MigrationManagerInterface $migration_manager,
    Request $request,
    LanguageManagerInterface $language_manager,
    CacheBackendInterface $cache_backend,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $universe_manager, $migration_manager, $request, $language_manager);

    $this->cacheBackend = $cache_backend;
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
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('language_manager'),
      $container->get('newsroom_connector.cache'),
    );
  }

  const DELETED_PATH = 'feed/deleted';

  const UNPUBLISHED_PATH = 'feed/deleted';

  // Newsroom item importers.
  const MIGRATION_ITEM = 'newsroom_item';
  const MIGRATION_ITEM_IMAGE_MEDIA = 'newsroom_item_image_media';
  const MIGRATION_ITEM_IMAGE = 'newsroom_item_image';

  /**
   * Delete newsroom item.
   *
   * @param int $newsroom_id
   *   Newsroom id.
   */
  public function delete($newsroom_id) {
    $deleted_items = $this->getDeletedItems();
    if (in_array($newsroom_id, $deleted_items)) {
      $entity = $this->getEntityByNewsroomId($newsroom_id);
      if (!empty($entity)) {
        $entity->delete();
      }
    }
  }

  /**
   * Get deleted items.
   *
   * @return array
   *   Newsroom ids.
   */
  protected function getDeletedItems() {
    return $this->getIdsFromFeed(NewsroomItemNewsroomProcessor::DELETED_PATH);
  }

  /**
   * Get unpublished items.
   *
   * @return array
   *   Newsroom ids.
   */
  protected function getUnpublishedItems() {
    return $this->getIdsFromFeed(NewsroomItemNewsroomProcessor::UNPUBLISHED_PATH);
  }

  /**
   * Get newsroom id for given URL.
   *
   * @param string $path
   *   Sub path for delete or unpublished items.
   *
   * @return array
   *   newsroom ids.
   */
  protected function getIdsFromFeed($path) {
    $newsroom_ids = [];

    $cid = "newsroom_ids:$path";

    $data_cached = $this->cacheBackend->get($cid);

    if (!$data_cached) {

      $url = $this->universeManager->getBaseUrl() . $path;
      $xml_string = file_get_contents($url);

      $xml = simplexml_load_string($xml_string);
      $xml->registerXPathNamespace('infsonewsroom', 'https://ec.europa.eu/newsroom/full-rss.dtd');

      $newsroom_basic_ids = $xml->xpath('//infsonewsroom:BasicId');
      foreach ($newsroom_basic_ids as $newsroom_basic_id) {
        $newsroom_ids[] = (string)$newsroom_basic_id;
      }

      $this->cacheBackend->set($cid, $newsroom_ids, time() + 3600);
    }
    else {
      $newsroom_ids = $data_cached->data;
    }

    return $newsroom_ids;
  }

  /**
   * Unpublish newsroom item.
   *
   * @param int $newsroom_id
   *   Newsroom id.
   */
  public function unpublish($newsroom_id) {
    $unpublished_items = $this->getUnpublishedItems();
    if (in_array($newsroom_id, $unpublished_items)) {
      $entity = $this->getEntityByNewsroomId($newsroom_id);
      if (!empty($entity)) {
        $entity->setPublished()->save();
      }
    }
  }
}
