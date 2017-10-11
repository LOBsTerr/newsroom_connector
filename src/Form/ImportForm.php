<?php

namespace Drupal\nexteuropa_newsroom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\nexteuropa_newsroom\Helper\UniverseHelper;
use Drupal\nexteuropa_newsroom\Importer\BaseImporter;

class ImportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'nexteuropa_newsroom.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'nexteuropa_newsroom_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Entity type:'),
      '#default_value' => 'item',
      '#options' => [
        'item' => $this->t('Item'),
        'topic' => $this->t('Topic'),
        'type' => $this->t('Type'),
      ],
      '#required' => TRUE,
    ];
    $form['number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of items:'),
      '#default_value' => 25,
      '#description' => $this->t('The maximum number per page 500.'),
      '#required' => TRUE,
    ];
    $form['page'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Page:'),
      '#default_value' => 1,
      '#description' => $this->t('To avoid performance issues we import restricted item per page.'),
      '#required' => TRUE,
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = simplexml_load_file(UniverseHelper::getBaseUrl() . UniverseHelper::get);
    var_dump($data);
  }

}