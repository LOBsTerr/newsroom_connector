<?php

namespace Drupal\newsroom_connector\Plugin\migrate_plus\data_parser;

use Drupal\migrate\MigrateException;
use Drupal\migrate_plus\DataParserPluginBase;
use Drupal\migrate_plus\Plugin\migrate_plus\data_parser\XmlTrait;

/**
 * Obtain XML data for migration using the SimpleXML API.
 *
 * @DataParser(
 *   id = "newsroom_simple_xml",
 *   title = @Translation("Newsroom Simple XML")
 * )
 */
class SimpleXml extends DataParserPluginBase {

  use XmlTrait;

  /**
   * Array of matches from item_selector.
   *
   * @var array
   */
  protected $matches = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    // Suppress errors during parsing, so we can pick them up after.
    libxml_use_internal_errors(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  protected function openSourceUrl($url): bool {
    // Clear XML error buffer. Other Drupal code that executed during the
    // migration may have polluted the error buffer and could create false
    // positives in our error check below. We are only concerned with errors
    // that occur from attempting to load the XML string into an object here.
    libxml_clear_errors();

    // We cache result of fetch operation to accelerate batch operations.
    $cid = 'newsroom_http_plugin' . crc32($url);
    $cache_backend = \Drupal::service('cache.default');
    $data_cached = $cache_backend->get($cid);

    if (!$data_cached) {

      $xml_data = $this->sanitizeXml((string) $this->getDataFetcherPlugin()->getResponseContent($url));

      // Store the tree into the cache.
      $cache_backend->set($cid, $xml_data, time() + 60);
    }
    else {
      $xml_data = $data_cached->data;
    }

    // Pass LIBXML_NOCDATA to use CDATA as a string.
    $xml = simplexml_load_string(trim($xml_data), 'SimpleXMLElement', LIBXML_NOCDATA);
    foreach (libxml_get_errors() as $error) {
      $error_string = self::parseLibXmlError($error);
      throw new MigrateException($error_string);
    }
    $this->registerNamespaces($xml);
    $xpath = $this->configuration['item_selector'];
    $this->matches = $xml->xpath($xpath);
    return TRUE;
  }

  /**
   * Sanitize wrong HTML entities.
   *
   * @param string $string
   *   String to be sanitized.
   *
   * @return string
   *   Sanitized string.
   */
  protected function sanitizeXml($string) {
    if (empty($string)) {
      return $string;
    }

    return preg_replace('/&#\d{5};/i', '', $string);
  }

  /**
   * {@inheritdoc}
   */
  protected function fetchNextRow(): void {
    $target_element = array_shift($this->matches);

    // If we've found the desired element, populate the currentItem and
    // currentId with its data.
    if ($target_element !== FALSE && !is_null($target_element)) {
      foreach ($this->fieldSelectors() as $field_name => $xpath) {
        foreach ($target_element->xpath($xpath) as $value) {
          if ($value->children() && !trim((string) $value)) {
            $this->currentItem[$field_name] = $value;
          }
          else {
            $this->currentItem[$field_name][] = (string) $value;
          }
        }
      }
      // Reduce single-value results to scalars.
      foreach ($this->currentItem as $field_name => $values) {
        if (is_array($values) && count($values) == 1) {
          $this->currentItem[$field_name] = reset($values);
        }
      }
    }
  }

}
