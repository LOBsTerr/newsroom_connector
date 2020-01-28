<?php

namespace Drupal\newsroom_connector\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Newsroom processor item annotation object.
 *
 * @see \Drupal\newsroom_connector\Plugin\NewsroomProcessorManager
 * @see plugin_api
 *
 * @Annotation
 */
class NewsroomProcessor extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The content type.
   *
   * @var string
   */
  public $content_type;

  /**
   * The bundle.
   *
   * @var string
   */
  public $bundle;

  /**
   * The bundle field.
   *
   * @var string
   */
  public $bundle_field;

  /**
   * The import script.
   *
   * @var string
   */
  public $import_script;

  /**
   * The import segment.
   *
   * @var string
   */
  public $import_segment;

}
