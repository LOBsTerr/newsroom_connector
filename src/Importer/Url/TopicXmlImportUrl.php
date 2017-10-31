<?php
namespace Drupal\nexteuropa_newsroom\Importer\Url;

use Drupal\nexteuropa_newsroom\Helper\ConfigHelper;

class TopicXmlImportUrl extends XmlFetcher {
  protected function getScript() {
    return ConfigHelper::getValue('topic_import_script');
  }
  protected function getEntityUrlPart() {
    return ConfigHelper::getValue('topic_import_segment');
  }
}