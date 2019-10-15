<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Top Cities by Indicator Chart' block.
 *
 * @Block(
 *   id = "top_cities_by_indicator",
 *   admin_label = @Translation("Top Cities by Indicator Chart Block")
 * )
 */
class TopCitiesByIndicatorChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'top_cities_by_indicator_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-top-cities-by-indicator-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
