<?php
namespace Drupal\nexteuropa_newsroom\Importer\Url;

use Drupal\nexteuropa_newsroom\Helper\ConfigHelper;

class TypeXmlImportUrl extends XmlFetcher {
  protected function getScript() {
    return ConfigHelper::getValue('type_import_script');
  }
  protected function getEntityUrlPart() {
    return ConfigHelper::getValue('type_import_segment');
  }
}