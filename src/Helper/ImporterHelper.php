<?php

namespace Drupal\nexteuropa_newsroom\Helper;

use Drupal\aggregator\ItemsImporter;
use Drupal\nexteuropa_newsroom\Importer\ItemXmlImportUrl;
use Drupal\nexteuropa_newsroom\Importer\TypeXmlImportUrl;
use Drupal\nexteuropa_newsroom\Importer\TopicXmlImportUrl;
use Drupal\taxonomy\Plugin\views\argument\Taxonomy;

class ImporterHelper extends ConfigHelper {

  public static function getImport($type, $page, $number) {
    $fetcher_class = $type . 'Importer';
    $importer = new $fetcher_class($page, $number);

    switch ($type) {
      case 'item':
        $importer = new ItemImporter($type);
        break;
    }


//    switch ($type) {
//      case 'item':
//        $importer = new Importer($type);
//        break;
//
//      case 'topic':
//        $importer = new TaxonomyImporter(new TopicXmlFetcher($page, $number));
//        break;
//
//      case 'type':
//        $importer = new TaxonomyImporter(new TypeXmlFetcher($page, $number));
//        break;
//    }

    $importer->import();
  }


}
