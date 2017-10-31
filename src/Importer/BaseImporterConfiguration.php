<?php


namespace Drupal\nexteuropa_newsroom\Importer;

abstract class BaseImporterConfiguration {
  private $configuration = NULL;

  public abstract function getConfiguration();
}