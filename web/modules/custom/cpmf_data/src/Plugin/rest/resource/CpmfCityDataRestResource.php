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
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "cpmf_city_data_rest_resource",
 *   label = @Translation("CPMF city data rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/city-data/{city}"
 *   }
 * )
 */
class CpmfCityDataRestResource extends ResourceBase {

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
   * Constructs a new CpmfCityDataRestResource object.
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

      // Implement the logic of your REST Resource here.

      $city = \Drupal::entityTypeManager()->getStorage('node')->load($city_id);
      $cache_metadata->addCacheableDependency($city);
      $data = [];
      $data['time'] = time();
      $data[$city->label()]['nid'] = $city->id();

      $pillar_nodes = cpmf_data_get_pillar_nodes_keyed_by_field_code();
      $indicator_data_references = $city->field_indicator_data->getValue();
      foreach ($indicator_data_references as $indicator_data_reference) {
        $indicator_data = \Drupal::entityTypeManager()->getStorage('node')->load($indicator_data_reference['target_id']);
        $cache_metadata->addCacheableDependency($indicator_data);
        $year_tid = $indicator_data->field_year->getValue()[0]['target_id'];
        $year = Term::load($year_tid);
        $data[$city->label()]['years'][$year->getName()]['year_name'] = $year->getName();
        $data[$city->label()]['years'][$year->getName()]['year_tid'] = $year_tid;

        $indicator_fields = $indicator_data->getFields();
        foreach ($indicator_fields as $indicator_field_name => $indicator_field) {
          if (substr($indicator_field_name, 0, 13) === 'field_pillar_') {
            // TODO: check if the field_pillar_* actually exists as a published pillar
            $pillar_field = $indicator_field_name;
            $pillar_value = null;
            if (isset($indicator_data->{$pillar_field}->getValue()[0])) {
              $pillar_value = $indicator_data->{$pillar_field}->getValue()[0]['value'];
            }
            $pillar = $pillar_nodes[$pillar_field];
            $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['number'] = $pillar->field_pillar_number->value;
            $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['label'] = $pillar->label();
            $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['short_label'] = $pillar->field_abbreviation->value;
            $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['pillar_score'] = $pillar_value;
            $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['color'] = $pillar->field_color->getValue()[0]['color'];
          }
        }
      }

      $yearly_baselines_targets_references = $city->field_yearly_baselines_targets->getValue();
      foreach ($yearly_baselines_targets_references as $yearly_baselines_targets_reference) {
        $yearly_baselines_targets = Paragraph::load($yearly_baselines_targets_reference['target_id']);
        $cache_metadata->addCacheableDependency($yearly_baselines_targets);
        $year_tid = $yearly_baselines_targets->field_year->getValue()[0]['target_id'];
        $year = Term::load($year_tid);
        $data[$city->label()]['years'][$year->getName()]['year_name'] = $year->getName();
        $data[$city->label()]['years'][$year->getName()]['year_tid'] = $year_tid;

        foreach ($yearly_baselines_targets->field_baselines_targets as $key => $bt) {
          $baseline_target = Paragraph::load($bt->getValue()['target_id']);
          $cache_metadata->addCacheableDependency($baseline_target);
          $indicator = \Drupal::entityTypeManager()->getStorage('node')->load($baseline_target->field_indicator->getValue()[0]['target_id']);
          $cache_metadata->addCacheableDependency($indicator);
          $indicator_field = $indicator->field_indicator_data_field->getValue()[0]['value'];
          $pillar_reference = $indicator->field_pillars->getValue()[0]['target_id'];
          $pillar = \Drupal::entityTypeManager()->getStorage('node')->load($pillar_reference);
          $cache_metadata->addCacheableDependency($pillar);
          $pillar_field = 'field_pillar_' . $pillar->field_field_name_code->getValue()[0]['value'];
          $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['indicators'][$indicator_field]['label'] = $indicator->label();

          $baseline = isset($baseline_target->field_indicator_baseline->getValue()[0]['value']) ? $baseline_target->field_indicator_baseline->getValue()[0]['value'] : NULL;
          $target = isset($baseline_target->field_indicator_target->getValue()[0]) ? $baseline_target->field_indicator_target->getValue()[0]['value'] : NULL;
          $value = isset($indicator_data->{$indicator_field}->getValue()[0]) ? $indicator_data->{$indicator_field}->getValue()[0]['value'] : NULL;
          $weight = $indicator->field_indicator_weight->value;
          $indicator_performance = cpmf_data_get_indicator_performance($value, $baseline, $target);

          $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['indicators'][$indicator_field]['baseline'] = $baseline;
          $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['indicators'][$indicator_field]['target'] = $target;
          $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['indicators'][$indicator_field]['value'] = $value;
          $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['indicators'][$indicator_field]['weight'] = $weight;
          $data[$city->label()]['years'][$year->getName()]['pillars'][$pillar_field]['indicators'][$indicator_field]['indicator_performance'] = $indicator_performance;
        }
      }

      if (!empty($data)) {
        $response = new ResourceResponse($data);
        $response->addCacheableDependency($cache_metadata);
        return $response;
      }
      throw new NotFoundHttpException(t('Nothing found'));
    }
    throw new HttpException(t('City wasn\'t provided'));
  }

}
