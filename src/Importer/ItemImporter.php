<?php
namespace Drupal\nexteuropa_newsroom\Importer;

use Drupal\nexteuropa_newsroom\Helper\ConfigHelper;

class ItemImporter extends BaseImporter {
  protected function getScript() {
    return ConfigHelper::getValue('item_import_script');
  }
  protected function getEntityUrlPart() {
    return ConfigHelper::getValue('item_import_segment');
  }
}