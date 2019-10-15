<?php

namespace Drupal\cpmf_reports\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Reports Home' block.
 *
 * @Block(
 *   id = "reports_home",
 *   admin_label = @Translation("Reports Home Block")
 * )
 */
class ReportsHomeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $reports = [
      [
        'title' => 'Cities',
        'button' => 'success',
        'link1' => 'reports/cities',
        'link2' => 'reports/cities/pillar',
        'link3' => 'reports/cities/indicator',
      ],
      [
        'title' => 'Regions',
        'button' => 'info',
        'link1' => 'reports/regions',
        'link2' => 'reports/regions/pillar',
        'link3' => 'reports/regions/indicator',
      ],
      [
        'title' => 'Zones',
        'button' => 'warning',
        'link1' => 'reports/zones',
        'link2' => 'reports/zones/pillar',
        'link3' => 'reports/zones/indicator',
      ],
      [
        'title' => 'National',
        'button' => 'default',
        'link1' => 'reports/national',
        'link2' => 'reports/national/pillar',
        'link3' => 'reports/national/indicator',
      ],
    ];

    $markup = '<div class="jumbotron well">';
//    $markup .= '    <p>Select a report</p>';
    $markup .= '    <div class="row">';

    foreach ($reports as $report) {
      $markup .= '        <div class="col-lg-3 col-sm-6 col-xs-12">';
      $markup .= '            <div class="panel panel-primary">';
      $markup .= '                <div class="panel-heading">';
      $markup .= '                    <h2 class="panel-title">' . $report['title'] . '</h3>';
      $markup .= '                </div>'; // panel-heading
      $markup .= '                <div class="panel-body">';
      $markup .= '                        <p><a href="' . $report['link1'] . '" class="btn btn-' . $report['button'] . '">Overall</a></p>';
      $markup .= '                        <p><a href="' . $report['link2'] . '" class="btn btn-' . $report['button'] . '">By Pillar</a></p>';
      $markup .= '                        <p><a href="' . $report['link3'] . '" class="btn btn-' . $report['button'] . '">By Indicator</a></p>';
      $markup .= '                </div>'; // panel-body
      $markup .= '             </div><br /><br />'; // panel
      $markup .= '        </div>'; //col
    }

    $markup .= '    </div>';
    $markup .= '</div>';
    return array(
      '#type' => 'markup',
      '#markup' => $markup,
    );
  }

}
