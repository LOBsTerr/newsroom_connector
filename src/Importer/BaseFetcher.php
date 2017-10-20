<?php
namespace Drupal\nexteuropa_newsroom\Importer;

abstract class BaseFetcher implements FetcherInterface {
  protected $data = NULL;
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
   * Gets number of items to be fetched.
   *
   * @return int
   *   Number of items.
   */
  public function getNumber() {
    return $this->number;
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
   * Gets fetched data.
   *
   * @return array
   *   Data fetched from the URL.
   */
  public function getData() {
    return $this->data;
  }

  /**
   * BaseImporter constructor.
   *
   * @param int $page
   *   Page number.
   * @param int $number
   *   Number of items.
   * @param int $newsroom_id
   *   Original newsroom id.
   */
  public function __construct($page, $number, $newsroom_id = NULL) {
    $this->page = $page;
    $this->number = $number;
    $this->newsroom_id = $newsroom_id;
  }
}