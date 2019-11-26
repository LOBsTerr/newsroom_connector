<?php

namespace Drupal\migrate_tools\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_plus\Plugin\MigrationConfigEntityPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for nexteuropa_newsroom message routes.
 */
class NexteuropaNewsroomController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('plugin.manager.migration')
    );
  }

  /**
   * Constructs a MessageController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   A database connection.
   * @param \Drupal\migrate_plus\Plugin\MigrationConfigEntityPluginManager $migration_config_entity_plugin_manager
   *   The plugin manager for config entity-based migrations.
   */
  public function __construct(Connection $database, MigrationConfigEntityPluginManager $migration_config_entity_plugin_manager) {
    $this->database = $database;
    $this->migrationConfigEntityPluginManager = $migration_config_entity_plugin_manager;
  }

  /**
   * Gets an array of log level classes.
   *
   * @return array
   *   An array of log level classes.
   */
  public static function getLogLevelClassMap() {
    return [
      MigrationInterface::MESSAGE_INFORMATIONAL => 'migrate-message-4',
      MigrationInterface::MESSAGE_NOTICE => 'migrate-message-3',
      MigrationInterface::MESSAGE_WARNING => 'migrate-message-2',
      MigrationInterface::MESSAGE_ERROR => 'migrate-message-1',
    ];
  }



}
