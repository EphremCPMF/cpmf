<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Top Regions by Pillar Chart' block.
 *
 * @Block(
 *   id = "top_regions_by_pillar",
 *   admin_label = @Translation("Top Regions by Pillar Chart Block")
 * )
 */
class TopRegionsByPillarChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'top_regions_by_pillar_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-top-regions-by-pillar-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
