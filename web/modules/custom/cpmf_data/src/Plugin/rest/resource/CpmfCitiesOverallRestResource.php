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
 *   id = "cpmf_cities_overall_rest_resource",
 *   label = @Translation("Cpmf cities overall data rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/cities-overall"
 *   }
 * )
 */
class CpmfCitiesOverallRestResource extends ResourceBase {

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
   * Constructs a new CpmfCitiesOverallRestResource object.
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

    $year = cpmf_data_get_active_year();
    $cache_metadata->addCacheableDependency($_SESSION['active_year_tid']);

    // Implement the logic of your REST Resource here.
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'city');
    $nids = $query->execute();
    $cities = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($nids);

    $data = [];

    foreach ($cities as $city) {
      $cache_metadata->addCacheableDependency($city);
      $indicator_data_nodes = NULL;
      if ($city->field_indicator_data) {
        $indicator_data_nodes = $city->field_indicator_data->referencedEntities();
      }
      $indicator_data = NULL;

      // Find the indicator node for the active year.
      foreach ($indicator_data_nodes as $key => $indicator_data_node) {
        if ($indicator_data_node->field_year->getValue()[0]['target_id'] == $year) {
          $indicator_data = $indicator_data_node;
        }
      }
      $pillars = cpmf_data_get_pillar_nodes_keyed_by_field_code();
      $city_pillar_values = array();
      $nid = NULL;
      $total_score = NULL;
      if ($indicator_data) {
        $cache_metadata->addCacheableDependency($indicator_data);
        foreach ($pillars as $pillar_field_code => $pillar) {
          $city_pillar_values[$pillar_field_code] = round($indicator_data->get($pillar_field_code)->value);
        }
        $city_pillar_values['field_total_pillar_score'] = round($indicator_data->get('field_total_pillar_score')->value);
        $total_score = round($indicator_data->get('field_total_pillar_score')->value);
        $nid = $indicator_data->id();

        $data[] = [
          'time' => time(),
          'nid' => $nid,
          'title' => $city->label(),
          'field_total_pillar_score' => $total_score,
          'pillars' => $city_pillar_values,
        ];
      }
    }

    if (!empty($data)) {
      // Obtain a list of columns
      foreach ($data as $key => $row) {
        $mid[$key]  = $row['field_total_pillar_score'];
      }

      // Sort the data with mid descending
      // Add $data as the last parameter, to sort by the common key
      array_multisort($mid, SORT_DESC, $data);

      $response = new ResourceResponse($data);
      $response->addCacheableDependency($cache_metadata);
      return $response;
    }
    throw new NotFoundHttpException(t('Nothing found.'));
  }
}
