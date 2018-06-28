<?php

namespace Drupal\commerce_express;

use Drupal\commerce_express\Entity\ExpressMethodInterface;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_price\Price;

/**
 * 执行运费价格调整
 */
class ExpressCostOrderProcessor implements OrderProcessorInterface
{

    /**
     * Constructs a new ExpressCostOrderProcessor object.
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        $express_method = $order->get('express_method')->entity;
        if ($express_method instanceof ExpressMethodInterface && $express_method->getCost()) {
            $cost_amount = new Price((string)$express_method->getCost(), 'CNY');
            $order->addAdjustment(new Adjustment([
                'type' => 'commerce_express_cost',
                // @todo Change to label from UI when added in #2770731.
                'label' => $express_method->label(),
                'amount' => $cost_amount,
                'source_id' => $express_method->id(),
            ]));
            $order->recalculateTotalPrice();
        }
    }

}
