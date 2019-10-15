<?php

namespace Drupal\cpmf_data\Plugin\rest\resource;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "cpmf_zones_rest_resource",
 *   label = @Translation("Cpmf zones rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/zones"
 *   }
 * )
 */
class CpmfZonesRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   *  A instance of entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new CpmfIndicatorsRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    EntityManagerInterface $entity_manager,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('entity.manager'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $cache_metadata = new CacheableMetadata();
    $cache_metadata->addCacheableDependency($_SESSION['active_year_tid']);

    // Implement the logic of your REST Resource here.
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'sub_region')
      ->sort('title', 'ASC');
    $nids = $query->execute();
    $zones = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);
    $zone_values = [];
    $pillars = cpmf_data_get_pillar_field_names();

    foreach ($zones as $nid => $zone) {
      $cache_metadata->addCacheableDependency($zone);
      $zone_value = cpmf_data_get_zone_score($zone);
      $zone_values[$nid] = [
        'title' => $zone->label(),
        'zone_value' => $zone_value,
      ];
      foreach ($pillars as $key => $pillar) {
        $value = cpmf_data_get_zone_pillar_value($zone, $pillar);
        $zone_values[$nid][$pillar] = $value;
      }
    }

    if (!empty($zone_values)) {
      // Obtain a list of columns
      foreach ($zone_values as $key => $row) {
        $mid[$key] = $row['zone_value'];
      }

      // Sort the data with mid descending
      // Add $data as the last parameter, to sort by the common key
      array_multisort($mid, SORT_DESC, $zone_values);

      $response = new ResourceResponse($zone_values);
      $response->addCacheableDependency($cache_metadata);
      return $response;
    }
    throw new NotFoundHttpException(t('Nothing found'));
  }

}
