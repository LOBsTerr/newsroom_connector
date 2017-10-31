<?php
/**
 * Created by PhpStorm.
 * User: lob
 * Date: 31.10.17
 * Time: 11:42
 */

namespace Drupal\nexteuropa_newsroom\Importer\Url;


use Masterminds\HTML5\Exception;

class XmlImportUrlFactory {
  public static function getImportUrl($type) {
    switch ($type) {
      case 'item':
        return new ItemXmlImportUrl();
        break;
      case 'type':
        return new TypeXmlImportUrl();
        break;
      case 'topic':
        return new TopicXmlImportUrl();
        break;

      default:
        throw new Exception('Type of import URL does not exist');
    }
  }
}