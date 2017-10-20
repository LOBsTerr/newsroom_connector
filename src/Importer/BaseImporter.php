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
abstract class BaseImporter {
  protected $fetcher = NULL;

  /**
   * BaseImporter constructor.
   * @param $fetcher
   */
  function __construct(FetcherInterface $fetcher) {
    $this->fetcher = $fetcher;
  }

  abstract function import();


}