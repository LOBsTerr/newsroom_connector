<?php
/**
 * Created by PhpStorm.
 * User: cnect
 * Date: 20/10/17
 * Time: 10:13
 */

namespace Drupal\nexteuropa_newsroom\Importer;

interface ImportUrlInterface {
  public function buildImportUrl();
  public function fetch();
}