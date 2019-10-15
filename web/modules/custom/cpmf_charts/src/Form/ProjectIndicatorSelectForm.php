<?php

namespace Drupal\cpmf_charts\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the ProjectIndicatorSelectForm form controller.
 *
 *
 * @see \Drupal\Core\Form\FormBase
 */
class ProjectIndicatorSelectForm extends FormBase {

  public function getIndicatorsSelectOptions() {
    $nids = array();
    $options = array();
    $project = \Drupal::routeMatch()->getParameter('node');
    if ($project && $project->bundle() == 'project') {
      foreach ($project->field_indicators as $key => $indicator) {
        $nids[] = $project->field_indicators->getValue()[$key]['target_id'];
      }
      if (!empty($nids)) {
        $indicators = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->loadMultiple($nids);
        foreach ($indicators as $nid => $indicator) {
          $pillar_id = $indicator->field_pillars->getValue()[0]['target_id'];
          $pillar = \Drupal::entityTypeManager()
            ->getStorage('node')
            ->load($pillar_id);
          $pillar_number = $pillar->field_pillar_number->value;
          $options['Pillar ' . $pillar_number][$indicator->field_indicator_data_field->value] = $indicator->title->value;
        }
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = $this->getIndicatorsSelectOptions();

    $form['select_project_indicator'] = [
      '#type' => 'select',
      '#options' => $options,
      '#cache' => ['contexts' => ['route.name']],
    ];

    if (isset($options[0])) {
      $form['select_project_indicator']['#default_value'] = array_keys($options)[0];
    }

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
    $nid = '';
//    if ($node = \Drupal::routeMatch()->getParameter('node')) {
//      $nid = '_' . $node->id();
//    }
    $nid = '_' . rand(1, 999999999);
    return 'cpmf_charts_project_indicator_select_form' . $nid;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
