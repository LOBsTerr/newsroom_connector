<?php
namespace Drupal\nexteuropa_newsroom\Importer\Url;

abstract class BaseImportUrl implements ImportUrlInterface {
  protected $page = 1;
  protected $number = 25;
  protected $newsroom_id = NULL;

  /**
   * Gets page number to be fetched.
   *
   * @return int
   *   Page number.
   */
  public function getPage() {
    return $this->page;
  }

  /**
   * @param int $page
   */
  public function setPage($page) {
    $this->page = $page;
  }

  /**
   * Gets number of items to be fetched.
   *
   * @return int
   *   Number of items.
   */
  public function getNumber() {
    return $this->number;
  }

  /**
   * @param int $number
   */
  public function setNumber($number) {
    $this->number = $number;
  }

  /**
   * Gets origin newsroom ID.
   *
   * @return int
   *   Newsroom ID.
   */
  public function getNewsroomId() {
    return $this->newsroom_id;
  }

  /**
   * @param null $newsroom_id
   */
  public function setNewsroomId($newsroom_id) {
    $this->newsroom_id = $newsroom_id;
  }

}
