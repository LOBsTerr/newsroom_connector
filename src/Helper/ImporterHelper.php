<?php

namespace Drupal\nexteuropa_newsroom\Helper;

use Drupal\nexteuropa_newsroom\Importer\ItemImporter;
use Drupal\nexteuropa_newsroom\Importer\TypeImporter;
use Drupal\nexteuropa_newsroom\Importer\TopicImporter;

class ImporterHelper extends ConfigHelper {

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

  public static function getImporter($type, $page, $number) {
    switch ($type) {
      case 'item':
        return new ItemImporter($page, $number);
        break;

      case 'topic':
        return new TopicImporter($page, $number);
        break;

      case 'type':
        return new TypeImporter($page, $number);
        break;
    }
  }
}
