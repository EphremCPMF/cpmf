<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Cities Pillar Line Chart' block.
 *
 * @Block(
 *   id = "cities_pillar_line",
 *   admin_label = @Translation("Cities Pillar Line Chart Block")
 * )
 */
class CitiesPillarLineChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'cities_pillar_line_chart_theme',
      '#attached' => array(
        'library' => array(
          'cpmf_charts/cpmf-cities-pillar-line-chart'
        ),
      ),
    ];
  }

}
