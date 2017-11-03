<?php
namespace Drupal\nexteuropa_newsroom\Importer\Url;

use Drupal\nexteuropa_newsroom\Helper\ConfigHelper;

/**
 * Class ItemImporter
 * @package Drupal\nexteuropa_newsroom\Importer
 */
class ItemXmlImportUrl extends XmlImportUrl {
  /**
   * @return array|mixed|null
   */
  protected function getScript() {
    return ConfigHelper::getValue('item_import_script');
  }
  protected function getEntityUrlPart() {
    return ConfigHelper::getValue('item_import_segment');
  }
}
