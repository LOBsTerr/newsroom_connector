<?php
namespace Drupal\nexteuropa_newsroom\Importer;

class BaseImporter {
  protected $data = NULL;

  public function import($url) {
    $this->data = simplexml_load_file($url);
  }
}