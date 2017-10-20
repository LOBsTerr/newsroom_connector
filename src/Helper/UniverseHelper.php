<?php

namespace Drupal\nexteuropa_newsroom\Helper;

use Drupal\Core\Url;

class UniverseHelper extends ConfigHelper {

  /**
   * Get universe Id.
   *
   * @return string
   *   Universe Id.
   */
  public static function getUniverseId() {
    return self::getValue('universe_id');
  }

  /**
   * Return newsroom base url.
   *
   * @return string
   *   Newsroom base URL.
   */
  public static function getBaseUrl() {
    return self::getValue('base_url') . self::getUniverseId();
  }

  /**
   * Return newsroom item id edit link.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public static function getItemEditUrl($newsroom_id) {
    return self::buildUrl(self::getValue('item_edit_segment'), [self::getValue('item_edit_segment') => $newsroom_id]);
  }

  /**
   * Return newsroom item id edit link.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public static function getItemUrl($newsroom_id = NULL) {
    return self::getEntityUrl('item', $newsroom_id);
  }

  // TODO: Remove later - Move code to importer classes.
  public static function getTopicUrl($newsroom_id = NULL) {
    return self::getEntityUrl('topic', $newsroom_id);
  }

  // TODO: Remove later - Move code to importer classes.
  public static function getTypeUrl($newsroom_id = NULL) {
    return self::getEntityUrl('type', $newsroom_id);
  }

  // TODO: Remove later - Move code to importer classes.
  private static function getEntityUrl($entity_type, $newsroom_id) {
    $params = [];
    if (!empty($newsroom_id)) {
      $params[self::getValue($entity_type . '_import_segment')] = $newsroom_id;
    }
    return self::buildUrl(self::getValue($entity_type . '_import_script'), $params);
  }

  /**
   * Return newsroom item id edit link.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public static function buildUrl($script_name, $params = []) {
    return Url::fromUri(self::getBaseUrl() . $script_name, ['query' => $params])->toUriString();
  }
}
