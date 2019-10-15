<?php

namespace Drupal\cpmf_charts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Project Indicator Select' block.
 *
 * @Block(
 *   id = "project_indicator_select",
 *   admin_label = @Translation("Project Indicator Select Block")
 * )
 */
class ProjectIndicatorSelectBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\cpmf_charts\Form\ProjectIndicatorSelectForm');
    return $form;
  }

  public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }

}
