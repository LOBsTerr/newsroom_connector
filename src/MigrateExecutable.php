<?php

namespace Drupal\newsroom_connector;

use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_tools\MigrateExecutable as MigrateExecutableBase;

/**
 * Defines a migrate executable class for drush.
 */
class MigrateExecutable extends MigrateExecutableBase {

  /**
   * Is debug enabled.
   *
   * @var bool
   */
  protected $debug = FALSE;

  /**
   * {@inheritdoc}
   */
  public function __construct(MigrationInterface $migration, MigrateMessageInterface $message = NULL, array $options = []) {
    $this->debug = \Drupal::service('newsroom_connector.universe_manager')->getConfig()->get('debug');
    parent::__construct($migration, $message, $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function progressMessage($done = TRUE) {
    if ($this->debug) {
      parent::progressMessage($done);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function rollbackMessage($done = TRUE) {
    if ($this->debug) {
      parent::rollbackMessage($done);
    }
  }

}
