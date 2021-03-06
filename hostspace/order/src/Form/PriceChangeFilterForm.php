<?php

/**
 * @file
 * Contains \Drupal\order\Form\PriceChangeFilterForm.
 */

namespace Drupal\order\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class PriceChangeFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'apply_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['filters'] = array(
      '#type' => 'details',
      '#title' => $this->t('Filter messages'),
      '#open' => !empty($_SESSION['apply_filter']),
    );
    
    $form['filters']['oid'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Order Code')
    );
   
	 	//加载订单状态到筛选条件里
	$form['filters']['status']= array(
      '#type' => 'select',
      '#title' => $this->t('Status'),
	  '#options' => array('' => 'All') + changePriceStatus(), 
    );
    
    $order_service = \Drupal::service('order.orderservice');
    $ask = $order_service->getAuditUser('ask');
    $ask_ops = array();
    foreach($ask as $key=>$value) {
      $ask_ops[$value->ask_uid] = $value->employee_name ;
    }
    $form['filters']['ask_uid'] = array(
      '#type' => 'select',
      '#title' => t('Applicant'),
      '#options' => array('' => 'All') + $ask_ops
    );
    $audit = $order_service->getAuditUser('audit');
    $audit_ops = array();
    foreach($audit as $key=>$value) {
      $audit_ops[$value->audit_uid] = $value->employee_name ;
    }
    $form['filters']['aduit_uid'] = array(
      '#type' => 'select',
      '#title' => t('Auditor'),
      '#options' => array('' => 'All') + $audit_ops
    );
    
 

    $fields = array('oid', 'status', 'ask_uid', 'aduit_uid');
    $allempty = true;
    foreach ($fields as $field) {
      if(!empty($_SESSION['apply_filter'][$field])) {
        $form['filters'][$field]['#default_value'] = $_SESSION['apply_filter'][$field];
        $allempty = false;
      }
    }
    if($allempty) {
      $_SESSION['apply_filter'] = array();
    }
    $form['filters']['actions'] = array(
      '#type' => 'actions',
      '#attributes' => array('class' => array('container-inline')),
    );
    $form['filters']['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Filter')
    );
    if (!empty($_SESSION['apply_filter'])) {
      $form['filters']['actions']['reset'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Reset'),
        '#limit_validation_errors' => array(),
        '#submit' => array('::resetForm'),
      );
    }
    $form['#prefix'] = '<div class="column">';
    $form['#suffix'] = '</div>';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $_SESSION['apply_filter']['oid'] = $form_state->getValue('oid');
	  $_SESSION['apply_filter']['status'] = $form_state->getValue('status');
    $_SESSION['apply_filter']['ask_uid'] = $form_state->getValue('ask_uid');
	  $_SESSION['apply_filter']['aduit_uid'] = $form_state->getValue('aduit_uid');
    
  }

  /**
   * Resets the filter form.
   */
  public function resetForm(array &$form, FormStateInterface $form_state) {
    $_SESSION['apply_filter'] = array();
  }
}
