<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Top Regions Chart' block.
 *
 * @Block(
 *   id = "top_regions",
 *   admin_label = @Translation("Top Regions Chart Block")
 * )
 */
class TopRegionsChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'top_regions_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-top-regions-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
