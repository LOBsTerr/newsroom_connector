<?php
/**
 * Created by PhpStorm.
 * User: lob
 * Date: 03.11.17
 * Time: 16:07
 */

namespace Drupal\nexteuropa_newsroom\Importer;


class Fetcher {
  private $url = NULL;
  /**
   * BaseImporter constructor.
   * @param $fetcher
   */
  function __construct(ImportUrlInterface $url) {
    $this->url = $url;
  }

  public function fetch() {

  }
}