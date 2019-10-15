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
 *   id = "cpmf_cities_pillar_line_rest_resource",
 *   label = @Translation("Cpmf cities pillar line rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/cities-pillar-line/{pillar}"
 *   }
 * )
 */
class CpmfCitiesPillarLineRestResource extends ResourceBase {

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
   * Constructs a new CpmfCitiesPillarLineRestResource object.
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
  public function get($pillar) {
    if ($pillar) {
      // Use current user after pass authentication to validate access.
      if (!$this->currentUser->hasPermission('access content')) {
        throw new AccessDeniedHttpException();
      }

      $cache_metadata = new CacheableMetadata();

      // Implement the logic of your REST Resource here.
      $city_values = [];
      $years = cpmf_data_get_all_years();

      $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', 'city');
      $nids = $query->execute();
      $cities = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

      foreach ($cities as $nid => $city) {
        $cache_metadata->addCacheableDependency($city);
        $indicator_data_nodes = $city->field_indicator_data->referencedEntities();
        $categories = [];
        $data = [];

        // Find the indicator node for each year.
        $score = [];
        foreach ($years as $year_name => $year_tid) {
          $categories[] = $year_name;
          $score[$year_name] = NULL;
          foreach($indicator_data_nodes as $indicator_data) {
            $cache_metadata->addCacheableDependency($indicator_data);
            if ($indicator_data->field_year->getValue()[0]['target_id'] == $year_tid) {
              $score[$year_name] = round(cpmf_data_get_pillar_score($pillar, $indicator_data));
            }
          }
          $data[] = $score[$year_name];
        }

        $city_values[] = [
          'name' => $city->label(),
          'years' => $categories,
          'data' => $data,
        ];
      }

      if (!empty($city_values)) {
        $response = new ResourceResponse($city_values);
        $response->addCacheableDependency($cache_metadata);
        return $response;
      }
      throw new NotFoundHttpException(t('Nothing found'));
    }
    throw new HttpException(t('Pillar wasn\'t provided'));
  }

}
