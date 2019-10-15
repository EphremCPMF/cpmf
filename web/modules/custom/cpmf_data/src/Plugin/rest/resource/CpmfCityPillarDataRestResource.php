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
 *   id = "cpmf_city_pillar_data_rest_resource",
 *   label = @Translation("Cpmf city pillar data rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/city-pillar-data/{city}"
 *   }
 * )
 */
class CpmfCityPillarDataRestResource extends ResourceBase {

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
   * Constructs a new CpmfCityPillarDataRestResource object.
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
  public function get($city_id) {
    if ($city_id) {
      // Use current user after pass authentication to validate access.
      if (!$this->currentUser->hasPermission('access content')) {
        throw new AccessDeniedHttpException();
      }

      $cache_metadata = new CacheableMetadata();
      $year = cpmf_data_get_active_year();
      $cache_metadata->addCacheableDependency($_SESSION['active_year_tid']);

      // Implement the logic of your REST Resource here.
      $city = \Drupal::entityTypeManager()->getStorage('node')->load($city_id);
      $cache_metadata->addCacheableDependency($city);
      $indicator_data_nodes = NULL;
      if ($city->field_indicator_data) {
        $indicator_data_nodes = $city->field_indicator_data->referencedEntities();
      }
      $indicator_data = NULL;

      // Find the indicator node for the active year.
      foreach ($indicator_data_nodes as $key => $indicator_data_node) {
        $cache_metadata->addCacheableDependency($indicator_data_node);
        if ($indicator_data_node->field_year->getValue()[0]['target_id'] == $year) {
          $indicator_data = $indicator_data_node;
        }
      }
      $pillars = cpmf_data_get_pillar_nodes_keyed_by_field_code();
      $city_pillar_values = array();
      $data = [];
      foreach ($pillars as $pillar_field_code => $pillar) {
        $cache_metadata->addCacheableDependency($pillar);
        $data[$pillar_field_code] = $indicator_data ? round($indicator_data->get($pillar_field_code)->value) : 0;
      }

      $city_pillar_values[] = $data;

      if (!empty($city_pillar_values)) {
        $response = new ResourceResponse($city_pillar_values);
        $response->addCacheableDependency($cache_metadata);
        return $response;
      }
      throw new NotFoundHttpException();
    }
    throw new HttpException(t('Nothing found.'));
  }

}
