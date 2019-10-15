<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Top Zones by Indicator Chart' block.
 *
 * @Block(
 *   id = "top_zones_by_indicator",
 *   admin_label = @Translation("Top Zones by Indicator Chart Block")
 * )
 */
class TopZonesByIndicatorChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'top_zones_by_indicator_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-top-zones-by-indicator-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
