<?php

namespace Drupal\newsroom_connector;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Redirector {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Redirector constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function redirect($entity_type, $bundle, $newsroom_id) {
    $entity = $this->getEntityByNewsroomId($entity_type, $bundle, $newsroom_id);
    if (!empty($entity)) {
      $response = new RedirectResponse($entity->toUrl()->toString());
      $response->send();
    }
    else {
      throw new NotFoundHttpException();
    }
  }

  private function getEntityByNewsroomId($entity_type, $bundle, $newsroom_id) {
    $entity = NULL;
    $items = $this->entityTypeManager
      ->getStorage($entity_type)
      ->loadByProperties([
        'field_newsroom_id' => $newsroom_id,
        'vid' => $bundle,
      ]);

    if ($item = reset($items)) {
      $entity = $item;
    }

    return $entity;
  }

}