<?php

namespace Drupal\newsroom_connector\Access;

use Drupal\Core\Config\ConfigFactoryInterface;
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
    if ($route->getRequirement('_newsroom_api_key') === NULL) {
      return AccessResult::neutral();
    }

    $api_key = $this->settings->get('api_key');
    $route_name = $route_match->getRouteName();

    if (empty($api_key)) {
      // Allow delete and unpublish action only if API key is set.
      if (in_array($route_name, [
        'newsroom_connector.delete',
        'newsroom_connector.unpublish',
      ])) {
        return AccessResult::forbidden();
      }
      else {
        // If API key is not set allow all requests.
        return AccessResult::allowed();
      }
    }

    // Allow admins to perform any actions.
    if (in_array('administrator', $account->getRoles())) {
      return AccessResult::allowed();
    }

    $parameters = $route_match->getParameters();
    if (!$parameters->has('random_key') ||
      !$parameters->has('public_key') ||
      !$parameters->has('newsroom_id')
    ) {
      return AccessResult::forbidden();
    }

    // Don't interfere if the group isn't a real group.
    $random_key = $parameters->get('random_key');
    if (empty($random_key)) {
      return AccessResult::forbidden();
    }

    $public_key = $parameters->get('public_key');
    if (empty($public_key)) {
      return AccessResult::forbidden();
    }

    $newsroom_id = $parameters->get('newsroom_id');
    if (empty($newsroom_id)) {
      return AccessResult::forbidden();
    }

    $hash = hash('sha256', mb_strtolower("{$api_key}{$newsroom_id}{$random_key}"));
    if ($hash === $public_key) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

}
