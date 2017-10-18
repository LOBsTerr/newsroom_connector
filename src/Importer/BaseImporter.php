<?php
namespace Drupal\nexteuropa_newsroom\Importer;

use Drupal\nexteuropa_newsroom\Helper\UniverseHelper;

abstract class BaseImporter {
  protected $data = NULL;
  protected $page = 1;
  protected $number = 25;
  protected $newsroom_id = NULL;

  public function getPage() {
    return $this->page;
  }

  public function getNumber() {
    return $this->number;
  }

  public function getNewsroomId() {
    return $this->newsroom_id;
  }

  public function __construct($page, $number, $newsroom_id = NULL) {
    $this->page = $page;
    $this->number = $number;
    $this->newsroom_id = $newsroom_id;
  }

  public function buildImportUrl() {
    $params = [
      'Page' => $this->getPage(),
      'n' => $this->getNumber(),
      $this->getEntityUrlPart() => $this->getNewsroomId(),
    ];
    return UniverseHelper::buildUrl($this->getScript(), $params);
  }

  public function import() {
    $url = $this->buildImportUrl();
    var_dump($url);
    exit();
    $this->data = simplexml_load_file($url);
  }

  abstract protected function getScript();
  abstract protected function getEntityUrlPart();
}