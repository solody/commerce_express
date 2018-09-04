<?php

namespace Drupal\commerce_express;

use Drupal\commerce_express\Entity\ExpressMethodInterface;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\ProductInterface;

/**
 * 执行运费价格调整
 */
class ExpressCostOrderProcessor implements OrderProcessorInterface {

  /**
   * Constructs a new ExpressCostOrderProcessor object.
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function process(OrderInterface $order) {
    $cost_amount = $this->determineExpressCost($order);
    if ($cost_amount instanceof Price && !$cost_amount->isZero()) {
      $express_method = $order->get('express_method')->entity;
      $order->addAdjustment(new Adjustment([
        'type' => 'commerce_express_cost',
        // @todo Change to label from UI when added in #2770731.
        'label' => '邮费',
        'amount' => $cost_amount,
        'source_id' => $express_method->id(),
      ]));
      $order->recalculateTotalPrice();
    }
  }

  private function determineExpressCost(OrderInterface $order) {
    $express_free = false;

    // 只要期中的一个商品免邮，那么整单免邮
    foreach ($order->getItems() as $orderItem) {
      if ($express_free) break;

      $purchased_entity = $orderItem->getPurchasedEntity();
      if (method_exists($purchased_entity, 'getProduct')) {
        $product = $purchased_entity->getProduct();
        if ($product instanceof ProductInterface) {
          if ($product->hasField('express_free') && $product->get('express_free')->value) {
            $express_free = true;
          }
        }
      }
    }

    $cost_amount = null;
    $express_method = $order->get('express_method')->entity;
    if ($express_method instanceof ExpressMethodInterface && $express_method->getCost()) {
      $cost_amount = new Price((string)$express_method->getCost(), 'CNY');
    }

    if (!$express_free && $cost_amount instanceof Price && !$cost_amount->isZero()) {
      return $cost_amount;
    }
  }
}
