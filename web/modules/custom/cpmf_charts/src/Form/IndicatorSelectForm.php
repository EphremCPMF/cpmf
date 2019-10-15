<?php

namespace Drupal\cpmf_charts\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the IndicatorSelectForm form controller.
 *
 *
 * @see \Drupal\Core\Form\FormBase
 */
class IndicatorSelectForm extends FormBase {

  public function getIndicatorsSelectOptions() {
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'indicator');
    $nids = $query->execute();
    $indicators = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    $options = array();
    $pillar_number = 0;
    foreach ($indicators as $nid => $indicator) {
      $indicator_number = $indicator->field_indicator_number->value;
      if ($indicator_number == 1) {
        $pillar_number++;
      }
      $options['Pillar ' . $pillar_number][$indicator->field_indicator_data_field->value] = $indicator->title->value;

    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = $this->getIndicatorsSelectOptions();

    $form['select_indicator'] = [
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => array_keys($options)[0],
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#attributes' => array('class' => array('hidden'))
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cpmf_charts_indicator_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
