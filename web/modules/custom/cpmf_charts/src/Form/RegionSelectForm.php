<?php

namespace Drupal\cpmf_charts\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the RegionSelectForm form controller.
 *
 *
 * @see \Drupal\Core\Form\FormBase
 */
class RegionSelectForm extends FormBase {

  public function getRegionsSelectOptions() {
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'region')
      ->sort('title', 'ASC');
    $nids = $query->execute();
    $regions = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    $options = array();
    foreach ($regions as $nid => $region) {
      $options[$region->id()] = $region->title->value;
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = $this->getRegionsSelectOptions();

    $form['select_region'] = [
      '#type' => 'select',
      '#options' => $options,
      '#label' => $this->t('Select a region'),
      '#title' => $this->t('Select a region'),
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
    return 'cpmf_charts_region_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
