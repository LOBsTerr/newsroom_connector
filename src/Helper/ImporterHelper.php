<?php

namespace Drupal\nexteuropa_newsroom\Helper;

class ImporterHelper extends BaseHelper {

  /**
   * Return newsroom item id edit link.
   *
   * @param int $newsroom_id
   *   Original newsroom id.
   *
   * @return string
   *   Edit url on the newsroom side.
   */
  public static function getItemImportUrl($newsroom_id) {
    return self::getValue('base_url') . self::getUniverseId() . '/' . self::getValue('item_edit_segment') . $newsroom_id;
  }
}
