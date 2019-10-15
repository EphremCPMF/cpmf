<?php

namespace Drupal\cpmf_data\Plugin\rest\resource;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\Log\LoggerInterface;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "cpmf_region_by_indicators_rest_resource",
 *   label = @Translation("Cpmf regions by indicators rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/regions-by-indicator/{indicator}"
 *   }
 * )
 */
class CpmfRegionsByIndicatorsRestResource extends ResourceBase {

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
   * Constructs a new CpmfRegionsByIndicatorsRestResource object.
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
  public function get($indicator) {
    if ($indicator) {
      // Use current user after pass authentication to validate access.
      if (!$this->currentUser->hasPermission('access content')) {
        throw new AccessDeniedHttpException();
      }

    $cache_metadata = new CacheableMetadata();

    // Implement the logic of your REST Resource here.
      $active_year = cpmf_data_get_active_year();
      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'region')
        ->sort('title', 'ASC');
      $nids = $query->execute();
      $regions = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadMultiple($nids);

      foreach ($regions as $nid => $region) {
        $cache_metadata->addCacheableDependency($region);
        $region_value = cpmf_data_get_region_score($region);
        if ($region_value) {
          $region_values[$nid] = [
            'title' => $region->label(),
            'indicator_value' => cpmf_data_get_region_indicator_value($region, $indicator)
          ];
        }
      }

      if (!empty($region_values)) {
        // Obtain a list of columns
        foreach ($region_values as $key => $row) {
          $mid[$key]  = $row['indicator_value'];
        }

        // Sort the data with mid descending
        // Add $data as the last parameter, to sort by the common key
        array_multisort($mid, SORT_DESC, $region_values);

        $response = new ResourceResponse($region_values);
        $response->addCacheableDependency($cache_metadata);
        return $response;
      }
      throw new NotFoundHttpException(t('Scores for indicator @indicator were not found', array('@indicator' => $indicator)));
    }

    throw new HttpException(t('Indicator wasn\'t provided'));
  }
}
