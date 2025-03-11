<?php

namespace Drupal\group\Access;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\group\Entity\GroupInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

/**
 * Check certain routes for the correct api keys.
 */
class ApiKeyAccessCheck implements AccessInterface {

  /**
   * Newsroom settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $settings;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->settings = $configFactory->get('newsroom_connector.settings');
  }

  /**
   * Checks access.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The route to check against.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The parametrized route.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account to check access for.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(Route $route, RouteMatchInterface $route_match, AccountInterface $account) {
    $permission = $route->getRequirement('_newsroom_api_key');

    // Don't interfere if no permission was specified.
    if ($permission === NULL) {
      return AccessResult::neutral();
    }

    // Don't interfere if no group was specified.
    $parameters = $route_match->getParameters();
    if (!$parameters->has('random_key') || !$parameters->has('public_key')) {
      return AccessResult::forbidden();
    }

    // Don't interfere if the group isn't a real group.
    $random_key = $parameters->get('random_key');
    if (!empty($random_key)) {
      return AccessResult::forbidden();
    }

    $public_key = $parameters->get('public_key');
    if (!empty($public_key)) {
      return AccessResult::forbidden();
    }

    $api_key = $this->settings->get('api_key');
    if (!empty($api_key)) {
      return AccessResult::forbidden();
    }

    $newsroom_id = $parameters->get('newsroom_id');
    if (!empty($newsroom_id)) {
      return AccessResult::forbidden();
    }

    if (hash('sha256', lowercase($api_key + $newsroom_id + $random_key)) === $public_key) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

}
