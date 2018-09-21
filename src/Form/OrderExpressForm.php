<?php

namespace Drupal\commerce_express\Form;

use Drupal\commerce\EntityHelper;
use Drupal\commerce_express\Entity\ExpressMethod;
use Drupal\commerce_order\Entity\Order;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OrderStateTransition.
 */
class OrderExpressForm extends FormBase {

  private $order;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_express_order_express_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['order_number'] = [
      '#type' => 'markup',
      '#markup' => '订单号：'.$this->getOrder()->getOrderNumber()
    ];

    $form['express'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('快递信息'),
    ];
    $form['express']['express_method'] = [
      '#type' => 'select',
      '#title' => $this->t('快递方式'),
      '#default_value' => $this->getOrder()->get('express_method')->value,
      '#options' => EntityHelper::extractLabels(ExpressMethod::loadMultiple()),
    ];
    $form['express']['express_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('快递单号'),
      '#default_value' => $this->getOrder()->get('express_number')->value,
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->canTransition() ? '发货' : '修改',
      '#description' => '如果订单处于待发货状态，提交快递信息后会执行发货状态转换。'
    ];

    return $form;
  }

  /**
   * @return Order
   * @throws \Exception
   */
  private function getOrder() {
    if ($this->order instanceof Order) return $this->order;
    else {
      $order_id = $this->getRouteMatch()->getParameter('commerce_order');
      $order = Order::load($order_id);
      if ($order instanceof Order) {
        $this->order = $order;
        return $this->order;
      } else {
        throw new \Exception('找不到订单['.$order_id.']');
      }
    }
  }

  /**
   * @return bool
   * @throws \Exception
   */
  private function canTransition() {
    $transitions = $this->getOrder()->getState()->getTransitions();
    $canTransition = false;
    foreach ($transitions as $transition) {
      if ($transition->getToState()->getId() === 'fulfillment') {
        $canTransition = true;
      }
    }
    return $canTransition;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    $values = $form_state->getValues();

    $this->getOrder()
      ->set('express_method', $values['express_method'])
      ->set('express_number', $values['express_number'])
      ->save();

    $msg = '';
    if ($this->canTransition()) {
      if ($this->switchToFulfillment()) $msg = '订单['.$this->getOrder()->getOrderNumber().']发货成功';
      else $msg = '订单['.$this->getOrder()->getOrderNumber().']的快递信息修改成功';
    } else {
      $msg = '订单['.$this->getOrder()->getOrderNumber().']的快递信息修改成功';
    }

    if (!empty($msg)) \Drupal::messenger()->addMessage($msg);
  }

  private function switchToFulfillment() {
    if ($this->canTransition()) {
      $transitions = $this->getOrder()->getState()->getTransitions();
      foreach ($transitions as $transition) {
        if ($transition->getToState()->getId() === 'fulfillment') {
          $this->getOrder()->getState()->applyTransition($transition);
          $this->getOrder()->save();
          return true;
        }
      }
    }

    return false;
  }
}
