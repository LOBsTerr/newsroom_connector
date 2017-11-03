<?php


namespace Drupal\nexteuropa_newsroom\Importer;

use Drupal\nexteuropa_newsroom\Helper\ConfigHelper;
use Drupal\nexteuropa_newsroom\Importer\Url\ImportUrlAbstractFactory;

class Configuration {
  private $configuration = NULL;
  private $url = NULL;
  private $type = NULL;

  public function __construct($type) {
    $import_settings = ConfigHelper::getValue('import');
    if (!empty($import_settings[$type])) {
      $this->configuration = $import_settings[$type];

    }
    else {
      throw new \Exception('Configuration not found');
    }
    $this->type = $type;
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function getUrl() {
    if (empty($this->url)) {
      $url_factory = ImportUrlAbstractFactory::getImportUrlFactory($this->getValue('parser'));
      $this->url = $url_factory->getImportUrl($this->type);
    }
    return $this->url;
  }

  private function getValue($name) {
    return !empty($this->configuration[$name]) ? $this->configuration[$name] : NULL;
  }
}
