<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'City Polar Chart' block.
 *
 * @Block(
 *   id = "city_polar",
 *   admin_label = @Translation("City Polar Chart Block")
 * )
 */
class CityPolarChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'city_polar_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-city-polar-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
