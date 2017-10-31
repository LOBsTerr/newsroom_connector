<?php

namespace Drupal\nexteuropa_newsroom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nexteuropa_newsroom\Helper\ImporterHelper;
use Drupal\nexteuropa_newsroom\Helper\UniverseHelper;

/**
 * Class ImportForm
 * @package Drupal\nexteuropa_newsroom\Form
 */
class ImportForm extends FormBase {

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
    $values = $form_state->getValues();

    $import = ImporterHelper::getImporter($values['type'], $values['page'], $values['number']);

    var_dump($import->buildImportUrl());

    $string = file_get_contents($import->buildImportUrl());
    $xml = new \SimpleXMLElement($string); //
    foreach ($xml->channel->item as $item) {
      var_dump($item);
//      $item->registerXPathNamespace('infsonewsroom', 'http://www.w3.org/2005/Atom');
      $result = $item->xpath("infsonewsroom:BasicTeaser");
      var_dump($result);
    }
//    $data = simplexml_load_file( );

    var_dump($xml);
    exit();
  }

}