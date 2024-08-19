<?php

namespace Drupal\newsroom_connector_item\EventSubscriber;

use Drupal\migrate\Row;
use Drupal\migrate_plus\Event\MigrateEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Preprocess newsroom item data.
 */
class MigrationItemPreprocess implements EventSubscriberInterface {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * MigrationItemPreprocess constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
   *   The event dispatcher.
   */
  public function __construct(EventDispatcherInterface $dispatcher) {
    $this->dispatcher = $dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[MigrateEvents::PREPARE_ROW][] = ['preprocess'];
    return $events;
  }

  /**
   * Event callback to sync source and destination.
   *
   * @param \Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The migration import event.
   */
  public function preprocess(MigratePrepareRowEvent $event) {
    $migration = $event->getMigration();
    $row = $event->getRow();

    // Validate URLs and set values to NULL if the URL is invalid.
    $migration_id = $migration->id();
    if (strpos($migration_id, 'newsroom_item_translations') !== FALSE || $migration_id == 'newsroom_item') {
      $this->validateUrls($row);
      // @TODO: Get rid off  "True" value when it is updated in the feed.
      $row->setSourceProperty('is_machine_translated', $row->getSourceProperty('is_machine_translated') == 1 || $row->getSourceProperty('is_machine_translated') == 'True');

      // We need to preprocess documents to handle them correctly by
      // plugin: sub_process.
      $related_documents_titles = $row->getSourceProperty('related_documents_title');
      $related_documents_urls = $row->getSourceProperty('related_documents_url');

      $data = [];

      if (!empty($related_documents_urls)) {
        if (is_array($related_documents_urls)) {
          foreach ($related_documents_urls as $key => $item) {
            $data[$key]['url'] = $related_documents_urls[$key];
            $data[$key]['title'] = $related_documents_titles[$key];
          }
        }
        else {
          $data[0]['url'] = $related_documents_urls;
          $data[0]['title'] = $related_documents_titles;
        }
      }

      $row->setSourceProperty('documents', $data);
    }

    if ($migration_id == 'newsroom_item') {
      // We set empty string for fields, which don't have values.
      // So, they will be reset.
      $source_fields = $row->getSource()['fields'];
      foreach ($source_fields as $source_field) {
        $source_field_name = $source_field['name'];
        $source_field_value = $row->getSourceProperty($source_field_name);
        if (empty($source_field_value)) {
          $row->setSourceProperty($source_field_name, '');
        }
      }

      // Convert the date to timestamp, to get a correct date
      // we need to clean UTC.
      $published_date = strtotime(str_replace('UTC', '', $row->getSourceProperty('published_date')));
      $row->setSourceProperty('published_date', $published_date);

      $start_date = $row->getSourceProperty('start_date');
      $end_date = $row->getSourceProperty('end_date');
      if (!empty($start_date) && empty($end_date)) {
        $row->setSourceProperty('end_date', $start_date);
      }
    }

    // Set title as alt text if it is empty.
    if (strpos($migration_id, 'newsroom_item_image_media_translations') !== FALSE || $migration_id == 'newsroom_item_image_media') {
      $picture_title = $row->getSourceProperty('item_name');
      if (empty($picture_title)) {
        // Set item title as picture title if it's empty.
        $picture_title = $row->getSourceProperty('title');
        // We limit the length to the length of DB field.
        $row->setSourceProperty('image_alt', substr($picture_title, 0, 500));
        $row->setSourceProperty('item_name', $picture_title);
      }

      $image_alt = $row->getSourceProperty('image_alt');
      if (empty($image_alt)) {
        $row->setSourceProperty('image_alt', substr($picture_title, 0, 500));
      }
      $row->setSourceProperty('image_alt', substr($picture_title, 0, 500));
    }

  }

  /**
   * Validate urls.
   *
   * @param \Drupal\migrate\Row $row
   *   Migration row.
   */
  protected function validateUrls(Row &$row) {
    // Because NR doesn't have proper validation of URLs, when can face
    // an exception when value for link field is set that URL is not valid
    // to avoid this case we set to NULL not valid URLs.
    $newsroom_id = $row->getSourceProperty('item_id');
    $logger = \Drupal::logger('newsroom');

    $see_also_url = $row->getSourceProperty('see_also_url');
    if (!empty($see_also_url) && !filter_var($see_also_url, FILTER_VALIDATE_URL)) {
      $row->setSourceProperty('see_also_url', NULL);
      $row->setSourceProperty('see_also_title', NULL);
      $logger->error(t('URL for field "See also" is not correct for newsroom id - @id', ['@id' => $newsroom_id]));
    }

    $main_url = $row->getSourceProperty('main_link');
    if (!empty($main_url) && !filter_var($main_url, FILTER_VALIDATE_URL)) {
      $row->setSourceProperty('main_link', NULL);
      $logger->error(t('URL for field "Main url" is not correct for newsroom id - @id', ['@id' => $newsroom_id]));
    }

    $project_website_url = $row->getSourceProperty('project_website_url');
    if (!empty($project_website_url) && !filter_var($project_website_url, FILTER_VALIDATE_URL)) {
      $row->setSourceProperty('project_website_url', NULL);
      $row->setSourceProperty('project_website_title', NULL);
      $logger->error(t('URL for field "Project website" is not correct for newsroom id - @id', ['@id' => $newsroom_id]));
    }

    $registration_link_url = $row->getSourceProperty('registration_link_url');
    if (!empty($registration_link_url) && !filter_var($registration_link_url, FILTER_VALIDATE_URL)) {
      $row->setSourceProperty('registration_link_url', NULL);
      $logger->error(t('URL for field "Registration link" is not correct for newsroom id - @id', ['@id' => $newsroom_id]));
    }
  }

}
