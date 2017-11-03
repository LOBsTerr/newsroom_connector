<?php
/**
 * Created by PhpStorm.
 * User: lob
 * Date: 31.10.17
 * Time: 11:42
 */

namespace Drupal\nexteuropa_newsroom\Importer\Url;

class ImportUrlAbstractFactory {
  public static function getImportUrlFactory($type) {
    switch ($type) {
      case 'xml':
        return new XmlImportUrlFactory();
        break;

      default:
        throw new Exception('Type of import URL factory does not exist');
    }
  }
}
