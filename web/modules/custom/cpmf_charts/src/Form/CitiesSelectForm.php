<?php

namespace Drupal\cpmf_charts\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements the CitySelectForm form controller.
 *
 *
 * @see \Drupal\Core\Form\FormBase
 */
class CitiesSelectForm extends CitySelectForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = $this->getPublishedCitiesSelectOptions();

    $form['select_city_one'] = [
      '#type' => 'select',
      '#options' => $options,
      '#label' => $this->t('Select first city'),
      '#default_value' => array_keys($options)[0],
      '#attributes' => array('class' => array('select-cities')),
      '#prefix' => '<div class="row"><div class="col-xs-6">',
      '#suffix' => '</div>',
    ];
    $form['select_city_two'] = [
      '#type' => 'select',
      '#options' => $options,
      '#label' => $this->t('Select second city'),
      '#default_value' => array_keys($options)[1],
      '#attributes' => array('class' => array('select-cities')),
      '#prefix' => '<div class="col-xs-6">',
      '#suffix' => '</div></div>',
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
    return 'cpmf_charts_cities_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
