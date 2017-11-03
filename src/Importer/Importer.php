<?php
/**
 * Created by PhpStorm.
 * User: cnect
 * Date: 20/10/17
 * Time: 10:09
 */

namespace Drupal\nexteuropa_newsroom\Importer;

/**
 * Class BaseImporter
 * @package Drupal\nexteuropa_newsroom\Importer
 */
class Importer {
  protected $configuration = NULL;
  protected $processor = NULL;
  protected $fetcher = NULL;

  /**
   * BaseImporter constructor.
   * @param $fetcher
   */
  function __construct($configuration) {
    $this->configuration = $configuration;
  }

  function import() {

  }


}