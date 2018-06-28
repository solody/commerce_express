<?php

namespace Drupal\commerce_express;

use Drupal\commerce_express\Entity\ExpressMethodInterface;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Express method entities.
 */
class ExpressMethodListBuilder extends ConfigEntityListBuilder
{

    /**
     * {@inheritdoc}
     */
    public function buildHeader()
    {
        $header['label'] = $this->t('名称');
        $header['cost'] = $this->t('运费');
        $header['id'] = $this->t('Machine name');
        return $header + parent::buildHeader();
    }

    /**
     * {@inheritdoc}
     */
    public function buildRow(EntityInterface $entity)
    {
        /** @var ExpressMethodInterface $entity */
        $row['label'] = $entity->label();
        $row['cost'] = $entity->getCost();
        $row['id'] = $entity->id();
        // You probably want a few more properties here...
        return $row + parent::buildRow($entity);
    }

}
