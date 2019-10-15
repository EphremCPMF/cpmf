<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Cities Indicator Line Chart' block.
 *
 * @Block(
 *   id = "cities_indicator_line",
 *   admin_label = @Translation("Cities Indicator Line Chart Block")
 * )
 */
class CitiesIndicatorLineChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'cities_indicator_line_chart_theme',
      '#attached' => array(
        'library' => array(
          'cpmf_charts/cpmf-cities-indicator-line-chart'
        ),
      ),
    ];
  }

}
