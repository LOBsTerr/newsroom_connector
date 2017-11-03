<?php
/**
 * Created by PhpStorm.
 * User: lob
 * Date: 03.11.17
 * Time: 09:46
 */

namespace Drupal\nexteuropa_newsroom\Importer;


use Drupal\config_devel\EventSubscriber\ConfigDevelAutoImportSubscriber;

class ImporterBuilder {
  public static function build($configuration) {
    $importer = new Importer($configuration);
    $importer->setFetcher(new Fetcher($configuration->getUrl()));
    $importer->setParser($configuration->getValue('parser'));
    $importer->setProcessor($configuration->getValue('processor'));
    return $importer;
  }

}
