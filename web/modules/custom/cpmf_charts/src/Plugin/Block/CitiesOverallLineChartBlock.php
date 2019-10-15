<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Cities Overall Line Chart' block.
 *
 * @Block(
 *   id = "cities_overall_line",
 *   admin_label = @Translation("Cities Overall Line Chart Block")
 * )
 */
class CitiesOverallLineChartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'cities_overall_line_chart_theme',
      '#attached' => array(
        'library' => array(
          'cpmf_charts/cpmf-cities-overall-line-chart',
          ),
      ),
    ];
  }

}
