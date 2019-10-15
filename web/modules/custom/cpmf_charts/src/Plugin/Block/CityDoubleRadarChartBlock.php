<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'City Double Radar Chart' block.
 *
 * @Block(
 *   id = "city_double_radar",
 *   admin_label = @Translation("City Double Radar Chart Block")
 * )
 */
class CityDoubleRadarChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'city_double_radar_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-city-double-radar-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
