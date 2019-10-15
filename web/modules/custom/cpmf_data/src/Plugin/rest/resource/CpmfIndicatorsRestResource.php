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
 *   id = "cpmf_indicators_rest_resource",
 *   label = @Translation("Cpmf indicators rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/top-cities-by-indicator/{indicator}"
 *   }
 * )
 */
class CpmfIndicatorsRestResource extends ResourceBase {

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
        ->condition('type', 'indicator_data')
        ->condition('field_year', $active_year)
        ->sort('title', 'ASC');
      $nids = $query->execute();
      $datas = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
      $city_values = [];
      foreach ($datas as $nid => $data) {
        $cache_metadata->addCacheableDependency($data);
        $indicator_values = NULL;
        $indicator_baseline = NULL;
        $indicator_target = NULL;
        $indicator_weight = NULL;
        $indicator_performance = NULL;
        $baselines_targets = cpmf_data_get_baselines_targets_by_indicator_data($data);
        if ($baselines_targets) {
          $indicator_dataset = array_column($baselines_targets, $indicator);
          if ($indicator_dataset) {
            $indicator_values = $indicator_dataset[0];
            $indicator_baseline = $indicator_values['indicator_baseline'];
            $indicator_target = $indicator_values['indicator_target'];
            $indicator_weight = $indicator_values['indicator_weight'];
            $indicator_performance = $indicator_values['indicator_performance'];
          }

          $city_id = $data->field_city->getValue()[0]['target_id'];
          $city_values[] = [
            'title' => $data->label(),
            'city_id' => $city_id,
            'indicator_value' => $data->{$indicator}->value,
            'indicator_baseline' => $indicator_baseline,
            'indicator_target' => $indicator_target,
            'indicator_weight' => $indicator_weight,
            'indicator_performance' => $indicator_performance
          ];
        }
      }

      if (!empty($city_values)) {
        // Obtain a list of columns
        foreach ($city_values as $key => $row) {
          $mid[$key]  = $row['indicator_performance'];
        }

        // Sort the data with mid descending
        // Add $data as the last parameter, to sort by the common key
        array_multisort($mid, SORT_DESC, $city_values);

        $response = new ResourceResponse($city_values);
        $response->addCacheableDependency($cache_metadata);
        return $response;
      }
      throw new NotFoundHttpException(t('Scores for indicator @indicator were not found', array('@indicator' => $indicator)));
    }

    throw new HttpException(t('Indicator wasn\'t provided'));
  }
}
