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
 *   id = "cpmf_program_data_rest_resource",
 *   label = @Translation("CPMF program data rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/program-data/{program_or_project_id}"
 *   }
 * )
 */
class CpmfProgramDataRestResource extends ResourceBase {

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
   * Constructs a new CpmfProgramDataRestResource object.
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
  public function get($program_id) {
    if ($program_id) {
      // Use current user after pass authentication to validate access.
      if (!$this->currentUser->hasPermission('access content')) {
        throw new AccessDeniedHttpException();
      }

      $cache_metadata = new CacheableMetadata();

      // Implement the logic of your REST Resource here.
      $program = \Drupal::entityTypeManager()->getStorage('node')->load($program_id);
      $cache_metadata->addCacheableDependency($program);
      $type = $program->getType();

      if ($type != 'program') {
        throw new NotFoundHttpException(t('Not a program.'));
      }

      $data = [];
      $data['time'] = time();
      $data[$program->label()]['nid'] = $program->id();
      foreach($program->field_projects as $key1 => $project_id) {
        foreach ($program->field_regions as $key2 => $region_id) {
          $region = \Drupal::entityTypeManager()->getStorage('node')->load($region_id->target_id);
          $cache_metadata->addCacheableDependency($region);
          $data[$program->label()]['regions'][$region->label()]['nid'] = $region->id();
        }
        foreach ($program->field_cities as $key3 => $city_id) {
          $city = \Drupal::entityTypeManager()->getStorage('node')->load($city_id->target_id);
          $cache_metadata->addCacheableDependency($city);
          $data[$program->label()]['cities'][$city->label()]['nid'] = $city->id();
        }
        $project = \Drupal::entityTypeManager()->getStorage('node')->load($project_id->target_id);
        $cache_metadata->addCacheableDependency($project);
        if ($project) {
          $data[$program->label()]['projects'][$project->label()]['nid'] = $project->id();
          foreach ($project->field_regions as $key4 => $region_id) {
            $region = \Drupal::entityTypeManager()->getStorage('node')->load($region_id->target_id);
            $cache_metadata->addCacheableDependency($region);
            $data[$program->label()]['projects'][$project->label()]['regions'][$region->label()]['nid'] = $region->id();
          }
          foreach ($project->field_cities as $key5 => $city_id) {
            $city = \Drupal::entityTypeManager()->getStorage('node')->load($city_id->target_id);
            $cache_metadata->addCacheableDependency($city);
            $data[$program->label()]['projects'][$project->label()]['cities'][$city->label()]['nid'] = $city->id();
          }
          foreach ($project->field_indicators as $key6 => $indicator_id) {
            $indicator = \Drupal::entityTypeManager()->getStorage('node')->load($indicator_id->target_id);
            if ($indicator) {
              $cache_metadata->addCacheableDependency($indicator);
              $indicator_field = $indicator->field_indicator_data_field->getValue()[0]['value'];
              $data[$program->label()]['projects'][$project->label()]['indicators'][$indicator_field]['label'] = $indicator->label();
            }
          }
        }
      }


      if (!empty($data)) {
        $response = new ResourceResponse($data);
        $response->addCacheableDependency($cache_metadata);
        return $response;
      }
      throw new NotFoundHttpException(t('Nothing found'));
    }
    throw new HttpException(t('Program or project wasn\'t provided'));
  }

}
