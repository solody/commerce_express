<?php

namespace Drupal\commerce_express\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Express method entities.
 */
interface ExpressMethodInterface extends ConfigEntityInterface
{
    /**
     * @return float
     */
    public function getCost();

    /**
     * @param $amount float
     * @return $this
     */
    public function setCost($amount);
}
