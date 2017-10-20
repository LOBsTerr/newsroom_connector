<?php
namespace Drupal\nexteuropa_newsroom\Importer;

use Drupal\nexteuropa_newsroom\Helper\ConfigHelper;

class TopicXmlFetcher extends XmlFetcher {
  protected function getScript() {
    return ConfigHelper::getValue('topic_import_script');
  }
  protected function getEntityUrlPart() {
    return ConfigHelper::getValue('topic_import_segment');
  }
}