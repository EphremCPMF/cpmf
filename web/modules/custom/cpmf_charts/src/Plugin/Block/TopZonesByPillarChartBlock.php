<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Top Zones by Pillar Chart' block.
 *
 * @Block(
 *   id = "top_zones_by_pillar",
 *   admin_label = @Translation("Top Zones by Pillar Chart Block")
 * )
 */
class TopZonesByPillarChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'top_zones_by_pillar_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-top-zones-by-pillar-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
