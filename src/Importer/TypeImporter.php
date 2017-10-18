<?php
namespace Drupal\nexteuropa_newsroom\Importer;

use Drupal\nexteuropa_newsroom\Helper\ConfigHelper;

class TypeImporter extends BaseImporter {
  protected function getScript() {
    return ConfigHelper::getValue('type_import_script');
  }
  protected function getEntityUrlPart() {
    return ConfigHelper::getValue('type_import_segment');
  }
}