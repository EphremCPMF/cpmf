<?php

namespace Drupal\cpmf_data\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'City Select' block.
 *
 * @Block(
 *   id = "city_select",
 *   admin_label = @Translation("City Select Block")
 * )
 */
class CitySelectBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\cpmf_charts\Form\CitySelectForm');
    return $form;
  }

}
