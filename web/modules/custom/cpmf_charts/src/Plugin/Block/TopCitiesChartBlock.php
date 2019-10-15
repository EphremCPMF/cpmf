<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Top Cities Chart' block.
 *
 * @Block(
 *   id = "top_cities",
 *   admin_label = @Translation("Top Cities Chart Block")
 * )
 */
class TopCitiesChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'top_cities_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-top-cities-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
