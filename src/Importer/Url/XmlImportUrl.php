<?php
namespace Drupal\nexteuropa_newsroom\Importer\Url;

use Drupal\nexteuropa_newsroom\Helper\UniverseHelper;

abstract class XmlImportUrl extends BaseImportUrl {

  /**
   * Builds URL to be imported.
   *
   * @return string
   *   URL.
   */
  public function buildImportUrl() {
    $params = [
      'Page' => $this->getPage(),
      'n' => $this->getNumber(),
      $this->getEntityUrlPart() => $this->getNewsroomId(),
    ];
    return UniverseHelper::buildUrl($this->getScript(), $params);
  }

  /**
   * Gets script name.
   *
   * @return string
   */
  abstract protected function getScript();

  /**
   * Gets entity URL parameter.
   *
   * @return string
   */
  abstract protected function getEntityUrlPart();
}
