<?php

namespace Drupal\cpmf_data\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Random;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("city_pillars_scores")
 */
class CityPillarsScoresViewsField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    // Return a random text, here you can include your custom logic.
    // Include any namespace required to call the method required to generate
    // the desired output.
//    dpm($this->view->field['field_pillar_utl']);
    $output = '';
    foreach ($this->view->field as $key => $field) {
      $pos = strpos($key, 'field_pillar');
      if ($pos === 0) {
        $value = round($this->view->field[$key]->original_value->__toString());
        $output .= '<div>' . $this->view->field[$key]->options['label'] . '</div>';
        $output .= '<div class="score score-' . $value . ' score-' . $key . '"><div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100">' . $value . '</div></div></div>';
      }
    }
    return [
      '#type' => 'markup',
      '#markup' => $output
    ];
  }

}
