<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Top Zones Chart' block.
 *
 * @Block(
 *   id = "top_zones",
 *   admin_label = @Translation("Top Zones Chart Block")
 * )
 */
class TopZonesChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $activeYearName = cpmf_data_get_active_year('year');
    $build = [];
    $build['#theme'] = 'top_zones_chart_theme';
    $build['#attached']['library'][] = 'cpmf_charts/cpmf-top-zones-chart';
    $build['#attached']['drupalSettings']['activeYearName'] = $activeYearName;

    return $build;
  }

}
