<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Project by Indicator Chart' block.
 *
 * @Block(
 *   id = "project_by_indicator",
 *   admin_label = @Translation("Project by Indicator Chart Block")
 * )
 */
class ProjectByIndicatorChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'project_by_indicator_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-project-by-indicator-chart';
    if ($project = \Drupal::routeMatch()->getParameter('node')) {
      foreach ($project->field_cities as $key => $city) {
        $build['#attached']['drupalSettings']['projectByIndicator']['cities'][] = $city->target_id;
      }
    }
    return $build;
  }

}
