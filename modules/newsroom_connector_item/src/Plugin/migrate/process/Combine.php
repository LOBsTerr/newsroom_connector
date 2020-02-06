<?php

namespace Drupal\newsroom_connector_item\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Combines two arrays.
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "combine"
 * )
 */
class Combine extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    var_dump($value);

    $first = $row->getSourceProperty($this->configuration['first']);
    $second = $row->getSourceProperty($this->configuration['second']);

    if (empty($first)) {
      throw new MigrateException('First array is empty');
    }
    else if (!is_array($first)) {
      throw new MigrateException('First array is not array');
    }

    if (empty($this->configuration['second'])) {
      throw new MigrateException('Second array is empty');
    }
    else if (!is_array($this->configuration['first'])) {
      throw new MigrateException('Second array is not array');
    }

    if (count($first) != count($second)) {
      throw new MigrateException('Arrays are not the same size');
    }

    $output = [];

    foreach ($first as $key => $item) {
      $output[$key]['first'] = $first[$key];
      $output[$key]['second'] = $second[$key];
    }

    var_dump($output);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return TRUE;
  }

}
