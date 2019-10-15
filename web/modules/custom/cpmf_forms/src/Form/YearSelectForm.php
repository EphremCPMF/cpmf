<?php

namespace Drupal\cpmf_forms\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the YearSelectForm form controller.
 *
 *
 * @see \Drupal\Core\Form\FormBase
 */
class YearSelectForm extends FormBase {

  public function getAvailableYearsSelectOptions() {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('vid', 'year');
    $tids = $query->execute();
    $years = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
    $options = array();
    foreach ($years as $tid => $year) {
      $options[$tid] = $year->get('name')->value;
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = $this->getAvailableYearsSelectOptions();

    $form['select_year'] = [
      '#type' => 'select',
      '#options' => $options,
      '#label' => $this->t('Select a year'),
//      '#title' => $this->t('Select a year'),
      '#default_value' => cpmf_data_get_active_year(),
      '#attributes' => array('onChange' => 'document.getElementById("cpmf-forms-year-select-form").submit();'),
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

    $form['#cache'] = ['max-age' => 0];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cpmf_forms_year_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $year = \Drupal\taxonomy\Entity\Term::load($values['select_year'])
      ->get('name')->value;
    $_SESSION['active_year_name'] = $year;
    $_SESSION['active_year_tid'] = $values['select_year'];

    drupal_set_message(t('Year changed to ') . $_SESSION['active_year_name']);
  }

}
